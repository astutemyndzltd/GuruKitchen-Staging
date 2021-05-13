<?php

namespace App\Http\Controllers\API;


use App\Models\Driver;
use App\Repositories\DriverRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DriversPayoutRepository;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class DriverController
 * @package App\Http\Controllers\API
 */

class DriverAPIController extends Controller
{
    /** @var  DriverRepository */
    private $driverRepository;
    private $driversPayoutRepository;

    public function __construct(DriverRepository $driverRepo, DriversPayoutRepository $driversPayoutRepository)
    {
        $this->driverRepository = $driverRepo;
        $this->driversPayoutRepository = $driversPayoutRepository;
    }

    /**
     * Display a listing of the Driver.
     * GET|HEAD /drivers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->driverRepository->pushCriteria(new RequestCriteria($request));
            $this->driverRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $drivers = $this->driverRepository->all();

        return $this->sendResponse($drivers->toArray(), 'Drivers retrieved successfully');
    }

    /**
     * Display the specified Driver.
     * GET|HEAD /drivers/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Driver $driver */
        if (!empty($this->driverRepository)) {
            $driver = $this->driverRepository->findWithoutFail($id);
        }

        if (empty($driver)) {
            return $this->sendError('Driver not found');
        }

        return $this->sendResponse($driver->toArray(), 'Driver retrieved successfully');
    }

    public function getEarningAndPayout($id) 
    {
        try {
            $statement = "select count(*) orders, sum(p.price) total, sum(o.delivery_fee) delivery_fee 
            from orders o join payments p on o.payment_Id = p.id where o.active = 1 and o.order_status_id = 5 
            and driver_paid_out = 0 and o.driver_id = $id";

            $result = DB::select(DB::raw($statement));

            $dc = setting('driver_commission', 0);
            $commission = ($result->total - $result->delivery_fee) * ($dc / 100);
            $earning = $commission + $result->delivery_fee;

            $payouts = $this->driversPayoutRepository->find($id);
            
            return $this->sendResponse([ 'orders' => $result->orders, 'earning' => getPriceOnly($earning), 'payout' => $payouts->toArray() ], 'Retrieved successfully');
        }
        catch(RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
