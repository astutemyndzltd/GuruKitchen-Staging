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
class MixedCriteria implements CriteriaInterface
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
        if ($this->request->has(['myLon', 'myLat'])) {

            $myLat = $this->request->get('myLat');
            $myLon = $this->request->get('myLon');
            $model = $model->selectRaw("*,(get_distance(latitude, longitude, $myLat, $myLon) / 1000) distance_km");
        }

        $whereClauses = [];

        if ($this->request->has('cuisines')) {

            $cuisines = $this->request->get('cuisines');

            if (!in_array('0', $cuisines)) {
                array_push($whereClauses, "id in (select distinct restaurant_id from restaurant_cuisines where cuisine_id in (" . join(",", $cuisines) . "))");
            }
        }

        if ($this->request->has('categories')) {

            $categories = $this->request->get('categories');

            if (!in_array('0', $categories)) {
                array_push($whereClauses, "id in (select distinct restaurant_id from foods where category_id in (" . join(",", $categories) . "))");
            }
        }


        if(count($whereClauses) > 0) {
            $model = $model->whereRaw('('. join(' or ', $whereClauses) . ')');
        }

        $model = $model->whereRaw("active = 1");

        if ($this->request->has(['myLon', 'myLat'])) {
            $model = $model->havingRaw("distance_km <= delivery_range")->orderBy("distance_km");
        }

        //file_put_contents('order.txt', $model->toSql());

        return $model;
    }
}
