<?php
/**
 * File name: ProximityCriteria.php
 * Last modified: 2020.05.03 at 10:15:14
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Criteria\Restaurants;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ProximityCriteria.
 *
 * @package namespace App\Criteria\Restaurants;
 */
class ProximityCriteria implements CriteriaInterface
{

    /**
     * @var array
     */
    private $request;

    /**
     * ProximityCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        
        if($this->request->has(['myLon', 'myLat'])) {

            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');

            $subQuery = $model->selectRaw("*, (get_distance(latitude, longitude, $myLat, $myLon) / 1000) as distance_km");

            $query = DB::table(DB::raw("({$subQuery->toSql()}) as rest"))
                    ->mergeBindings($subQuery->getQuery())
                    ->whereRaw('distance_km <= delivery_range')
                    ->orderBy('distance_km');

            return $query;

        }

        return $model;
        
    }
}
