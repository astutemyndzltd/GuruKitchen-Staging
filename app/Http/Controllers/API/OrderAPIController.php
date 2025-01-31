<?php

/**
 * File name: OrderAPIController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API;

use App\Criteria\Orders\OrderRequestOfDriverCriteria;
use App\Criteria\Orders\OrdersOfStatusesCriteria;
use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Events\OrderChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\AssignedOrder;
use App\Notifications\NewOrder;
use App\Notifications\OrderAccepted;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CartRepository;
use App\Repositories\FoodOrderRepository;
use App\Repositories\FoodRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Config;
use Cartalyst\Stripe\Stripe;
use Exception;
use Illuminate\Support\Facades\DB;


//use Stripe\Token;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */
class OrderAPIController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;
    /** @var  FoodOrderRepository */
    private $foodOrderRepository;
    /** @var  CartRepository */
    private $cartRepository;
    /** @var  UserRepository */
    private $userRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;
    private $foodRepository;

    /**
     * OrderAPIController constructor.
     * @param OrderRepository $orderRepo
     * @param FoodOrderRepository $foodOrderRepository
     * @param CartRepository $cartRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     * @param UserRepository $userRepository
     */
    
    public function __construct(FoodRepository $foodRepository, OrderRepository $orderRepo, FoodOrderRepository $foodOrderRepository, CartRepository $cartRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        //date_default_timezone_set('Europe/London');
        $this->orderRepository = $orderRepo;
        $this->foodOrderRepository = $foodOrderRepository;
        $this->cartRepository = $cartRepo;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
        $this->foodRepository = $foodRepository;
    }

    /**
     * Display a listing of the Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } 
        catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        $orders = $this->orderRepository->all();

        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }

    public function acceptOrder($id, Request $request) 
    {
        try {

            $driverId = $request->input('driver_id');
            $count = DB::table('driver_order_requests')->where('order_id', $id)->count();

            if ($count > 0) {
                $order = $this->orderRepository->update(['driver_id' => $driverId], $id);
                DB::table('driver_order_requests')->where('order_id', $id)->delete();
                $order = $order->fresh();
                $details = $this->userRepository->find($driverId);
                Notification::send($order->foodOrders[0]->food->restaurant->users, new OrderAccepted($order, $details->name));
                return $this->sendResponse([], 'Order accepted');
            }
            else {
                return $this->sendError('Order already accepted');
            }
        }
        catch(Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getOrderRequests(Request $request) 
    {
        $inputs = $request->all();
        
        try {
            $driverId = $inputs['driver_id'];
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new OrderRequestOfDriverCriteria($driverId));
            $orders = $this->orderRepository->all();
            return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
        }
        catch(RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Display the specified Order.
     * GET|HEAD /orders/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Order $order */
        if (!empty($this->orderRepository)) {
            try {
                $this->orderRepository->pushCriteria(new RequestCriteria($request));
                $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $order = $this->orderRepository->findWithoutFail($id);
        }

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        return $this->sendResponse($order->toArray(), 'Order retrieved successfully');
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $requestData = $request->all();

        if ($requestData['note'] == null) {
            $request->merge(['note' => '']);
        }

        $payment = $request->only('payment');
        
        if (isset($payment['payment']) && $payment['payment']['method']) {
            if ($payment['payment']['method'] == "Credit Card") {
                return $this->stripePaymentNew($request);
            } else {
                return $this->cashPayment($request);
            }
        }
    }

    private function isValidForOrder($input) 
    {
        $foodOrders = $input['foods'];
        $foodIds = array_map(function($fo) { return $fo['food_id']; }, $foodOrders);
        $foods = $this->foodRepository->findMany($foodIds);
        $restaurant = $foods[0]->restaurant;

        // stock validation 

        foreach($foods as $food) {
            if ($food->out_of_stock) {
                return false;
            }
        }

        // restaurant validation

        $orderType = $input['order_type'];
 
        if ($orderType == 'Delivery') {
            if(!$restaurant->available_for_delivery) {
                return false;
            }
        }

        if ($orderType == 'Pickup') {
            if(!$restaurant->available_for_pickup) {
                return false;
            }
        }


        $preorderInfo = $input['preorder_info'];
        $isPreorder = $preorderInfo != null && $preorderInfo != '';


        if ($isPreorder) {
            // pre-order
            if (!$restaurant->available_for_preorder) return false;
            $forToday = !(strpos($preorderInfo, ',') !== false);
            $openingTimes = $restaurant->opening_times;
            if (!isset($openingTimes)) return false;
            
            if ($forToday) {

                if ($restaurant->closed) return false;
                $today = strtolower(date('l'));
                $slotsForToday = $openingTimes[$today];
                if (!isset($slotsForToday)) return false;

                $time = strtotime($preorderInfo);
                $fallsInAny = false;


                foreach ($slotsForToday as $slot) {
                    $opensAt = strtotime($slot['opens_at']);
                    $closesAt = strtotime($slot['closes_at']);

                    if ($time >= $opensAt && $time <= $closesAt) {
                        $fallsInAny = true;
                        break;
                    }
                }

                if (!$fallsInAny) return false;

            }
            else {

                $info = explode(", ", $preorderInfo);
                $preorderDate = $info[0];
                $preorderTime = $info[1];
                $preorderDay = strtolower(date('l', strtotime($preorderDate)));
                $slotsForTheDay = $openingTimes[$preorderDay];
                if(!isset($slotsForTheDay)) return false;

                $time = strtotime($preorderTime);
                $fallsInAny = false;
                
                foreach ($slotsForTheDay as $slot) {
                    $opensAt = strtotime($slot['opens_at']);
                    $closesAt = strtotime($slot['closes_at']);
    
                    if ($time >= $opensAt && $time <= $closesAt) {
                        $fallsInAny = true;
                        break;
                    }
                }
    
                if (!$fallsInAny) return false;

            }
            
        }
        else {
            // instant order
            if ($restaurant->closed) return false;
            $openingTimes = $restaurant->opening_times;
            if (!isset($openingTimes)) return false;
            $today = strtolower(date('l'));
            $slotsForToday = $openingTimes[$today];
            if(!isset($slotsForToday)) return false;

            $time = strtotime(date('h:i A'));
            $fallsInAny = false;


            foreach ($slotsForToday as $slot) {
                $opensAt = strtotime($slot['opens_at']);
                $closesAt = strtotime($slot['closes_at']);

                if ($time >= $opensAt && $time <= $closesAt) {
                    $fallsInAny = true;
                    break;
                }
            }

            if (!$fallsInAny) return false;

        }

        return true;

    }



    private function stripePaymentNew(Request $request)
    {
        $input = $request->all();

        if (!$this->isValidForOrder($input))  {
            return $this->sendError('validation error');
        }
        
        $stripe = Stripe::make(Config::get('services.stripe.secret'));
        $paymentMethodId = isset($input['payment_method_id']) ? $input['payment_method_id'] : null;
        $paymentIntentId = isset($input['payment_intent_id']) ? $input['payment_intent_id'] : null;
        $paymentIntent = null;


        try {
                   
            if ($paymentIntentId != null) {
                $paymentIntent = $stripe->paymentIntents()->find($paymentIntentId);
            } 
            else {

                $options = [
                    'amount' => $input['order_amount'],
                    'currency' => 'gbp',
                    'payment_method' => $paymentMethodId
                ];

                $paymentIntent = $stripe->paymentIntents()->create($options);
                $paymentIntent = $stripe->paymentIntents()->confirm($paymentIntent['id']);
            }

            if ($paymentIntent['status'] == 'succeeded') {

                $user = $this->userRepository->findWithoutFail($input['user_id']);
                if (empty($user)) return $this->sendError('User not found');

                $order = null;

                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint', 'order_type', 'note', 'preorder_info')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint', 'order_type', 'note', 'preorder_info')
                    );
                }

                foreach ($input['foods'] as $foodOrder) {

                    $extras = $foodOrder['extras'];
                    unset($foodOrder['extras']);
                    $foodOrder['order_id'] = $order->id;
                    $fd = $this->foodOrderRepository->create($foodOrder);

                    foreach($extras as $extra) {
                        $fd->orderExtras()->create(['price' => $extra['price'], 'extra_id' => $extra['id'] ]);
                    }
                }
                
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $input['order_amount'],
                    "status" => 'Succeded', 
                    "method" => $input['card_brand'] . ' ' . substr($input['stripe_number'], strlen($input['stripe_number']) - 4),
                    "transaction_id" => "Stripe (" . $paymentIntent['id'] . ")"
                ]);
               
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);
                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);
                $stripe->paymentIntents()->update($paymentIntent['id'], ['metadata' => [ 'Order Id' => $order->id ]]);

                Notification::send($order->foodOrders[0]->food->restaurant->users, new NewOrder($order));
                
                return $this->sendResponse($order->toArray(), 'succeeded');
            } 
            else if ($paymentIntent['status'] == 'requires_source_action') {
                return $this->sendResponse(['client_secret' => $paymentIntent['client_secret']], 'requires action');
            } 
            else {
                return $this->sendError('invalid status');
            }

        } 
        catch (Exception $e) {
            return $this->sendError('invalid status');
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    /*private function stripPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        
        try {
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }
            $stripeToken = Token::create(array(
                "card" => array(
                    "number" => $input['stripe_number'],
                    "exp_month" => $input['stripe_exp_month'],
                    "exp_year" => $input['stripe_exp_year'],
                    "cvc" => $input['stripe_cvc'],
                    "name" => $user->name,
                )
            ));
            if ($stripeToken->created > 0) {
                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint', 'order_type', 'note', 'preorder_info')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint', 'order_type', 'note', 'preorder_info')
                    );
                }
                foreach ($input['foods'] as $foodOrder) {
                    $foodOrder['order_id'] = $order->id;
                    $amount += $foodOrder['price'] * $foodOrder['quantity'];
                    $this->foodOrderRepository->create($foodOrder);
                }
                $amount += $order->delivery_fee;
                $amountWithTax = $amount + ($amount * $order->tax / 100);
                $charge = $user->charge((int)($amountWithTax * 100), ['source' => $stripeToken, 'metadata' => ['order-id' => $order->id]]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment']['method'],
                ]);
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

                Notification::send($order->foodOrders[0]->food->restaurant->users, new NewOrder($order));
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }*/


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {

            $order = $this->orderRepository->create(
                $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint', 'order_type')
            );

            foreach ($input['foods'] as $foodOrder) {
                $foodOrder['order_id'] = $order->id;
                $amount += $foodOrder['price'] * $foodOrder['quantity'];
                $this->foodOrderRepository->create($foodOrder);
            }
            $amount += $order->delivery_fee;
            $amountWithTax = $amount + ($amount * $order->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $amountWithTax,
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

            Notification::send($order->foodOrders[0]->food->restaurant->users, new NewOrder($order));
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }


    public function changeOrderStatus($id, Request $request) 
    {        
        
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        $input = $request->all();

        try {
            $order = $this->orderRepository->update(['order_status_id' => $input['order_status_id']], $order->id);
            return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
        } 
        catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

    }


    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {        
        
        $oldOrder = $this->orderRepository->findWithoutFail($id);

        if (empty($oldOrder)) {
            return $this->sendError('Order not found');
        }

        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();

        try {

            $order = $this->orderRepository->update($input, $id);

            if (isset($input['order_status_id']) && $input['order_status_id'] == 5 && !empty($order)) {
                $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            }

            event(new OrderChangedEvent($oldStatus, $order));

            if (setting('enable_notifications', false)) {
                
                if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }
}