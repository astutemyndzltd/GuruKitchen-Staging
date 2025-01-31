<?php

namespace App\Http\Controllers;

use App\Criteria\Earnings\EarningOfRestaurantCriteria;
use App\Criteria\Restaurants\RestaurantsOfManagerCriteria;
use App\DataTables\RestaurantsPayoutDataTable;
use App\Http\Requests\CreateRestaurantsPayoutRequest;
use App\Http\Requests\UpdateRestaurantsPayoutRequest;
use App\Models\Order;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EarningRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\RestaurantsPayoutRepository;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\DB;

class RestaurantsPayoutController extends Controller
{
    /** @var  RestaurantsPayoutRepository */
    private $restaurantsPayoutRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;
    /**
     * @var EarningRepository
     */
    private $earningRepository;

    public function __construct(RestaurantsPayoutRepository $restaurantsPayoutRepo, CustomFieldRepository $customFieldRepo, RestaurantRepository $restaurantRepo, EarningRepository $earningRepository)
    {
        parent::__construct();
        date_default_timezone_set('Europe/London');
        $this->restaurantsPayoutRepository = $restaurantsPayoutRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->restaurantRepository = $restaurantRepo;
        $this->earningRepository = $earningRepository;
    }

    /**
     * Display a listing of the RestaurantsPayout.
     *
     * @param RestaurantsPayoutDataTable $restaurantsPayoutDataTable
     * @return Response
     */
    public function index(RestaurantsPayoutDataTable $restaurantsPayoutDataTable)
    {
        return $restaurantsPayoutDataTable->render('restaurants_payouts.index');
    }

    /**
     * Show the form for creating a new RestaurantsPayout.
     *
     * @return Response
     */
    public function create()
    {
        if (auth()->user()->hasRole('manager')) {
            $this->restaurantRepository->pushCriteria(new RestaurantsOfManagerCriteria(auth()->id()));
        }

        $restaurant = $this->restaurantRepository->pluck('name', 'id');

        $hasCustomField = in_array($this->restaurantsPayoutRepository->model(), setting('custom_field_models', []));

        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->restaurantsPayoutRepository->model());
            $html = generateCustomField($customFields);
        }

        return view('restaurants_payouts.create')->with("customFields", isset($html) ? $html : false)->with("restaurant", $restaurant);
    }

    /**
     * Store a newly created RestaurantsPayout in storage.
     *
     * @param CreateRestaurantsPayoutRequest $request
     *
     * @return Response
     */
    public function store(Order $model, CreateRestaurantsPayoutRequest $request)
    {
        $input = $request->all();
        $startDate = $input['from_date'];
        $endDate = $input['to_date'];
        $restaurantId = $input['restaurant_id'];

        try 
        {
            $this->restaurantsPayoutRepository->create($input);
            $model->whereRaw("orders.id in (select distinct o.id from orders o 
                            join food_orders fo on o.id = fo.order_id 
                            join foods f on f.id = fo.food_id
                            where f.restaurant_id = $restaurantId and 
                            date(o.created_at) between '$startDate' and '$endDate')")
                    ->update([ 'paid_out' => 1 ]);
        } 
        catch (ValidatorException $e) 
        {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.restaurants_payout')]));

        return redirect(route('restaurantsPayouts.index'));
    }

    /**
     * Display the specified RestaurantsPayout.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $restaurantsPayout = $this->restaurantsPayoutRepository->findWithoutFail($id);

        if (empty($restaurantsPayout)) {
            Flash::error('Restaurants Payout not found');

            return redirect(route('restaurantsPayouts.index'));
        }

        return view('restaurants_payouts.show')->with('restaurantsPayout', $restaurantsPayout);
    }

    /**
     * Show the form for editing the specified RestaurantsPayout.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $restaurantsPayout = $this->restaurantsPayoutRepository->findWithoutFail($id);
        $restaurant = $this->restaurantRepository->pluck('name', 'id');


        if (empty($restaurantsPayout)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.restaurants_payout')]));

            return redirect(route('restaurantsPayouts.index'));
        }
        $customFieldsValues = $restaurantsPayout->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->restaurantsPayoutRepository->model());
        $hasCustomField = in_array($this->restaurantsPayoutRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('restaurants_payouts.edit')->with('restaurantsPayout', $restaurantsPayout)->with("customFields", isset($html) ? $html : false)->with("restaurant", $restaurant);
    }

    /**
     * Update the specified RestaurantsPayout in storage.
     *
     * @param int $id
     * @param UpdateRestaurantsPayoutRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRestaurantsPayoutRequest $request)
    {
        $restaurantsPayout = $this->restaurantsPayoutRepository->findWithoutFail($id);

        if (empty($restaurantsPayout)) {
            Flash::error('Restaurants Payout not found');
            return redirect(route('restaurantsPayouts.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->restaurantsPayoutRepository->model());
        try {
            $restaurantsPayout = $this->restaurantsPayoutRepository->update($input, $id);


            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $restaurantsPayout->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.restaurants_payout')]));

        return redirect(route('restaurantsPayouts.index'));
    }

    /**
     * Remove the specified RestaurantsPayout from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $restaurantsPayout = $this->restaurantsPayoutRepository->findWithoutFail($id);

        if (empty($restaurantsPayout)) {
            Flash::error('Restaurants Payout not found');

            return redirect(route('restaurantsPayouts.index'));
        }

        $this->restaurantsPayoutRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.restaurants_payout')]));

        return redirect(route('restaurantsPayouts.index'));
    }

    /**
     * Remove Media of RestaurantsPayout
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $restaurantsPayout = $this->restaurantsPayoutRepository->findWithoutFail($input['id']);
        try {
            if ($restaurantsPayout->hasMedia($input['collection'])) {
                $restaurantsPayout->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function getTotalOrderAmount(Order $model, Request $request) {

        $restaurantId = $request->input('restaurantId');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $restaurant = $this->restaurantRepository->find($restaurantId);
        $adminCommission = $restaurant->admin_commission;

        $tax = setting('default_tax', 0);

        $driverComRate = setting('driver_commission', 0);

        $data =  $model->newQuery()->join('payments', 'orders.payment_id', '=', 'payments.id')
                            ->whereRaw("date(orders.created_at) between '$startDate' and '$endDate' 
                                        and orders.active = 1 and orders.paid_out = 0 and orders.order_status_id = 5 
                                        and orders.id in (select distinct fo.order_id from food_orders fo join foods f on 
                                        fo.food_id = f.id join restaurants r on r.id = f.restaurant_id 
                                        and f.restaurant_id = $restaurantId)")
                            ->selectRaw("sum(payments.price) total, count(*) orders,
                                        sum(if(orders.use_app_drivers = 1, orders.delivery_fee, 0)) delivery_fee,
                                        sum((if(orders.use_app_drivers = 1, $driverComRate, 0) / 100 * (payments.price - orders.delivery_fee))) driver_commission")->get();


        $totalOrderValue = $data[0]['total'];
        $deliveryFee = $data[0]['delivery_fee'];
        $driverCommission = round($data[0]['driver_commission'], 2);
        $orders = $data[0]['orders'];
        $commission = round($totalOrderValue * ($adminCommission / 100), 2);
        $taxAmount = round(($commission + $driverCommission) * ($tax / 100), 2);
        $net = $totalOrderValue - ($commission + $taxAmount + $deliveryFee + $driverCommission);
        
        $responseData = [
            'amount' => number_format((float)$net, 2, '.', ''),
            'orders' => $orders,
            'gross_revenue' => number_format((float)$totalOrderValue, 2, '.', ''),
            'admin_commission' => number_format((float)$adminCommission, 2, '.', ''),
            'driver_commission' => number_format((float)$driverCommission, 2, '.', ''),
            'driver_commission_rate' => $driverComRate,
            'delivery_fee' => number_format((float)$deliveryFee, 2, '.', ''),
            'tax' => number_format((float)$tax, 2, '.', '')
        ];

        return response()->json($responseData);
    }


    public function getLastPayoutDate(Request $request) 
    {
        $restaurantId = $request->input('restaurantId');

        $statement = "select date(min(created_at)) startdate, date(max(created_at)) enddate from orders o join 
        (select fo.order_id, f.restaurant_id from food_orders fo join foods f 
        on fo.food_id = f.id group by fo.order_id) fr on o.id = fr.order_id 
        where paid_out = 0 and active = 1 and restaurant_id = $restaurantId";

        $result = DB::select(DB::raw($statement));
        
        return response()->json([
            'startdate' => isset($result[0]->startdate) ? date('Y-m-d', strtotime($result[0]->startdate)) : date('Y-m-d', time()), 
            'enddate' => isset($result[0]->enddate) ? date('Y-m-d', strtotime($result[0]->enddate)) : date('Y-m-d', time()), 
        ]);
    }

    

}
