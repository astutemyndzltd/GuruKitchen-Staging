<?php

namespace App\Http\Controllers;

use App\Criteria\Earnings\EarningOfRestaurantCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\FilterByUserCriteria;
use App\DataTables\DriversPayoutDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateDriversPayoutRequest;
use App\Http\Requests\UpdateDriversPayoutRequest;
use App\Models\Order;
use App\Repositories\DriverRepository;
use App\Repositories\DriversPayoutRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\DB;

class DriversPayoutController extends Controller
{
    /** @var  DriversPayoutRepository */
    private $driversPayoutRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
  * @var UserRepository
  */
private $userRepository;
    /**
     * @var DriverRepository
     */
    private $driverRepository;

    public function __construct(DriversPayoutRepository $driversPayoutRepo, DriverRepository $driverRepository, CustomFieldRepository $customFieldRepo , UserRepository $userRepo)
    {
        parent::__construct();
        $this->driversPayoutRepository = $driversPayoutRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->driverRepository = $driverRepository;
    }

    /**
     * Display a listing of the DriversPayout.
     *
     * @param DriversPayoutDataTable $driversPayoutDataTable
     * @return Response
     */
    public function index(DriversPayoutDataTable $driversPayoutDataTable)
    {
        return $driversPayoutDataTable->render('drivers_payouts.index');
    }

    /**
     * Show the form for creating a new DriversPayout.
     *
     * @return Response
     */
    public function create()
    {
        $this->userRepository->pushCriteria(new DriversCriteria());
        $user = $this->userRepository->pluck('name','id');
        
        $hasCustomField = in_array($this->driversPayoutRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driversPayoutRepository->model());
                $html = generateCustomField($customFields);
            }
        return view('drivers_payouts.create')->with("customFields", isset($html) ? $html : false)->with("user",$user);
    }

    /**
     * Store a newly created DriversPayout in storage.
     *
     * @param CreateDriversPayoutRequest $request
     *
     * @return Response
     */
    public function store(Order $model, Request $request)
    {
        $input = $request->all();
        $driverId = $input['driver_id'];
        $startDate = $input['from_date'];
        $endDate = $input['to_date'];
        $input['driver_commission'] = setting('driver_commission', 0);

        try 
        {
            $statement = "active = 1 and driver_id = $driverId and driver_paid_out = 0
                          and date(created_at) between '$startDate' and '$endDate'";
                          
            $this->driversPayoutRepository->create($input);
            $model->whereRaw($statement)->update([ 'driver_paid_out' => 1 ]);

        }
        catch (ValidatorException $e) 
        {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.drivers_payout')]));

        return redirect(route('driversPayouts.index'));
    }

    /**
     * Display the specified DriversPayout.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $driversPayout = $this->driversPayoutRepository->findWithoutFail($id);

        if (empty($driversPayout)) {
            Flash::error('Drivers Payout not found');

            return redirect(route('driversPayouts.index'));
        }

        return view('drivers_payouts.show')->with('driversPayout', $driversPayout);
    }

    /**
     * Show the form for editing the specified DriversPayout.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $driversPayout = $this->driversPayoutRepository->findWithoutFail($id);
        $user = $this->userRepository->pluck('name','id');
        

        if (empty($driversPayout)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.drivers_payout')]));

            return redirect(route('driversPayouts.index'));
        }
        $customFieldsValues = $driversPayout->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->driversPayoutRepository->model());
        $hasCustomField = in_array($this->driversPayoutRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('drivers_payouts.edit')->with('driversPayout', $driversPayout)->with("customFields", isset($html) ? $html : false)->with("user",$user);
    }

    /**
     * Update the specified DriversPayout in storage.
     *
     * @param  int              $id
     * @param UpdateDriversPayoutRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDriversPayoutRequest $request)
    {
        $driversPayout = $this->driversPayoutRepository->findWithoutFail($id);

        if (empty($driversPayout)) {
            Flash::error('Drivers Payout not found');
            return redirect(route('driversPayouts.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driversPayoutRepository->model());
        try {
            $driversPayout = $this->driversPayoutRepository->update($input, $id);
            
            
            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $driversPayout->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully',['operator' => __('lang.drivers_payout')]));

        return redirect(route('driversPayouts.index'));
    }

    /**
     * Remove the specified DriversPayout from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $driversPayout = $this->driversPayoutRepository->findWithoutFail($id);

        if (empty($driversPayout)) {
            Flash::error('Drivers Payout not found');

            return redirect(route('driversPayouts.index'));
        }

        $this->driversPayoutRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.drivers_payout')]));

        return redirect(route('driversPayouts.index'));
    }

        /**
     * Remove Media of DriversPayout
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $driversPayout = $this->driversPayoutRepository->findWithoutFail($input['id']);
        try {
            if($driversPayout->hasMedia($input['collection'])){
                $driversPayout->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function getLastPaymentPeriod(Request $request) 
    {
        $driverId = $request->input('driverId');

        $statement = "select date(min(created_at)) startdate, date(max(created_at)) enddate from orders 
                      where driver_id = $driverId and active = 1 and driver_paid_out = 0;";

        $result = DB::select(DB::raw($statement));
        
        return response()->json([
            'startdate' => isset($result[0]->startdate) ? date('Y-m-d', strtotime($result[0]->startdate)) : date('Y-m-d', time()), 
            'enddate' => isset($result[0]->enddate) ? date('Y-m-d', strtotime($result[0]->enddate)) : date('Y-m-d', time()), 
        ]);
    }

    public function getPayoutAmount(Request $request) 
    {
        $driverId = $request->input('driverId');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $statement = "select count(*) total, sum(p.price - o.delivery_fee) subtotal, sum(o.delivery_fee) delivery_fee
                      from orders o join payments p on o.payment_id = p.id where o.active = 1 and o.driver_id = $driverId and 
                      o.driver_paid_out = 0 and date(o.created_at) between '$startDate' and '$endDate'";

        $result = DB::select(DB::raw($statement));
        
        $totalOrders = $result[0]->total;
        $subTotal = $result[0]->subtotal;
        file_put_contents('order.txt', $subTotal);
        $deliveryFee = $result[0]->delivery_fee;
        $driverCommission = setting('driver_commission', 0);
        $payoutAmount = $deliveryFee + (($driverCommission / 100) * $subTotal);

        $responseData = [
            'payout_amount' => number_format((float)$payoutAmount, 2, '.', ''),
            'orders' => $totalOrders,
            'delivery_fee' => number_format((float)$deliveryFee, 2, '.', ''),
            'orders_subtotal' => number_format((float)$subTotal, 2, '.', ''),
        ];

        return response()->json($responseData);

    }
}
