<?php
/**
 * File name: OrderController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Criteria\Users\ClientsCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\DriversOfRestaurantCriteria;
use App\DataTables\OrderDataTable;
use App\DataTables\FoodOrderDataTable;
use App\Events\OrderChangedEvent;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Notification as ModelsNotification;
use App\Notifications\AssignedOrder;
use App\Notifications\OrderServeRequest;
use App\Notifications\StatusChangedOrder;
use App\Notifications\StatusChangedOrderDriver;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DriverRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Exceptions\ValidatorException;
use stdClass;

class OrderController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    private $driverRepository;

    public function __construct(DriverRepository $driverRepository, OrderRepository $orderRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , OrderStatusRepository $orderStatusRepo, NotificationRepository $notificationRepo, PaymentRepository $paymentRepo)
    {
        parent::__construct();
        $this->driverRepository = $driverRepository;
        $this->orderRepository = $orderRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->orderStatusRepository = $orderStatusRepo;
        $this->notificationRepository = $notificationRepo;
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Order.
     *
     * @param OrderDataTable $orderDataTable
     * @return Response
     */
    public function index(OrderDataTable $orderDataTable, Request $request)
    {
        return $orderDataTable->with(['showLiveOrders' => $request->input('showLiveOrders')])->render('orders.index');
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');

        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');

        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));

        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
            $html = generateCustomField($customFields);
        }

        return view('orders.create')->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus);
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());

        try { 
            $order = $this->orderRepository->create($input);
            $order->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Display the specified Order.
     *
     * @param int $id
     * @param FoodOrderDataTable $foodOrderDataTable
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */

    public function show(FoodOrderDataTable $foodOrderDataTable, $id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);


        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));
            return redirect(route('orders.index'));
        }

        $subtotal = 0;

        foreach ($order->foodOrders as $foodOrder) {
            foreach ($foodOrder->orderExtras as $extra) {
                $foodOrder->price += $extra->price;
            }
            $subtotal += $foodOrder->price * $foodOrder->quantity;
        }

        $total = $subtotal + $order['delivery_fee'];
        $taxAmount = $total * $order['tax'] / 100;
        $total += $taxAmount;
        $foodOrderDataTable->id = $id;


        /*********** ADDING NECESSARY DATA FOR RECEIPT ********/
        
        $orderDetails = [];
        $orderDetails['id'] = $order->id;
        $orderDetails['hint'] = $order->hint;
        $orderDetails['order_type'] = $order->order_type;
        $orderDetails['delivery_address'] = $order->deliveryAddress ? $order->deliveryAddress->address : null;
		$orderDetails['subtotal'] = $subtotal;
		$orderDetails['tax'] = $order['tax'];
		$orderDetails['tax_amount'] = $taxAmount;
		$orderDetails['delivery_fee'] = $order['delivery_fee'];
		$orderDetails['total'] = $total;
		$orderDetails['restaurant_name'] = $order->foodOrders[0]->food->restaurant->name;
		$orderDetails['driver_name'] = $order->driver ? $order->driver->name : null;
		$orderDetails['customer_name'] = $order->user->name;
		$orderDetails['customer_phone'] = $order->user->custom_fields['phone'] ? $order->user->custom_fields['phone']['view'] : null;
        $orderDetails['payment_method'] = $order->payment->method;
        $orderDetails['order_note'] = $order->note;
        $orderDetails['preorder_info'] = $order->preorder_info;
		
		$foodCategories = [];
		
		foreach ($order->foodOrders as $foodOrder) {
			$food = $foodOrder->food;
			$category = $food->category;
			
			if(!array_key_exists($category->id, $foodCategories)) {
				$foodCategories[$category->id] = ['name' => $category->name, 'foods' => [] ];
			}
				
			$foodStrict = ['name' => $food->name, 'price' => $foodOrder->price, 'quantity' => $foodOrder->quantity, 'extras'=> []];
											
			for($i=0; $i<count($foodOrder->extras); $i++) 
			{
				$name = $foodOrder->extras[$i]->name;
				$price = $foodOrder->extras[$i]->price;
				$extra = ['name' => $name, 'price' => $price];
				array_push($foodStrict['extras'], $extra);
			}
			
			array_push($foodCategories[$category->id]['foods'], $foodStrict);	
			
		}
		
		$orderDetails['food_categories'] = array_values($foodCategories);

        return $foodOrderDataTable->render('orders.show', ["order" => $order, "total" => $total, "subtotal" => $subtotal,"taxAmount" => $taxAmount, "orderDetails" => $orderDetails]);
    }

    /**
     * Show the form for editing the specified Order.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));
            return redirect(route('orders.index'));
        }

        $restaurant = $order->foodOrders()->first();
        $restaurant = isset($restaurant) ? $restaurant->food['restaurant_id'] : 0;

        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversOfRestaurantCriteria($restaurant))->pluck('name', 'id');
        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');

        /***** newly added ******/
        $allOrderStatus = $orderStatus;
        $orderStatus = new stdClass();

        foreach ($allOrderStatus as $id => $status) {

            if ($id == $order->orderStatus->id) {

                $orderStatus->$id = $status;

                if ($id != 5) {
                    $next = $allOrderStatus[++$id];

                    if ($id == 4 && $order->order_type == 'Pickup') {
                        $next = $allOrderStatus[++$id];
                    }

                    $orderStatus->$id = $next;
                    break;
                }
            }    
        }
        
        /***** newly added ******/

        $customFieldsValues = $order->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));

        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('orders.edit')->with('order', $order)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param UpdateOrderRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateOrderRequest $request)
    {        
        
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $oldOrder = $this->orderRepository->findWithoutFail($id);

        if (empty($oldOrder)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));
            return redirect(route('orders.index'));
        }

        if (!$oldOrder->active) {
            return redirect(route('orders.index'));
        }

        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());

        if ($oldOrder['use_app_drivers'] == true && $input['use_app_drivers'] == false) $input['use_app_drivers'] = true;

        if ($oldOrder['use_app_drivers'] == true && !isset($oldOrder['driver_id']) && $oldOrder['order_status_id'] != $input['order_status_id'] && $input['order_status_id'] > 3) {
            Flash::error('No driver has accepted the order yet');
            return redirect(route('orders.index'));
        }

        try {

            $order = $this->orderRepository->update($input, $id);

            if (setting('enable_notifications', false)) {

                $order = $order->fresh();

                // sending notifications to customer
                if ($order['order_status_id'] != $oldOrder['order_status_id']) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                }

                
                // if we're using app drivers
                if ($oldOrder['use_app_drivers'] == false && $order['use_app_drivers'] == true) 
                {
                    $drivers = $order->foodOrders[0]->food->restaurant->drivers;
                    $driverIds = array_map(function($d) { return $d['id']; }, $drivers->toArray());
                    $driversDetails = $this->driverRepository->whereIn('user_id', $driverIds)->get();
 
                    foreach($drivers as $driver) {

                        $details = $driversDetails->firstWhere('user_id', $driver->id);

                        if ($details->available) {
                            // store booking request
                            DB::table('driver_order_requests')->insert(['order_id' => $order->id, 'driver_id' => $driver->id]);
                            
                            // send notifications
                            Notification::send([$driver], new OrderServeRequest($order));

                        }
                    }
                }

                //  sending notifications to driver
                if (isset($order['driver_id']) && $order['order_status_id'] != $oldOrder['order_status_id']) {
                    
                    $driver = $this->userRepository->findWithoutFail($order['driver_id']);

                    if (!empty($driver)) {
                        Notification::send([$driver], new StatusChangedOrderDriver($order));
                    }
                }
            }

            $this->paymentRepository->update(["status" => $input['status']], $order['payment_id']);
            //dd($input['status']);

            event(new OrderChangedEvent($oldStatus, $order));

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $order->customFieldsValues()->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        if (!env('APP_DEMO', false)) {
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
            $order = $this->orderRepository->findWithoutFail($id);

            if (empty($order)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

                return redirect(route('orders.index'));
            }

            $this->orderRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.order')]));


        } else {
            Flash::warning('This is only demo app you can\'t change this section ');
        }

        return redirect(route('orders.index'));
    }

    /**
     * Remove Media of Order
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $order = $this->orderRepository->findWithoutFail($input['id']);
        try {
            if ($order->hasMedia($input['collection'])) {
                $order->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
