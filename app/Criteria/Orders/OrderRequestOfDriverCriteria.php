<?php 

namespace App\Criteria\Orders;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class OrderRequestOfDriverCriteria implements CriteriaInterface 
{
    private $driverId;

    public function __construct($driverId) 
    {
        $this->driverId = $driverId;
    }

    public function apply($model, RepositoryInterface $repository) 
    {
        return $model->join('driver_order_requests', 'orders.id', '=', 'driver_order_requests.order_id')
                     ->where('driver_order_requests.driver_id', $this->driverId)
                     ->where('orders.active', 1)
                     ->where('orders.order_status_id', '<', 5)
                     ->where('orders.use_app_drivers', 1)
                     ->groupBy('orders.id')
                     ->select('orders.*');
    }
}