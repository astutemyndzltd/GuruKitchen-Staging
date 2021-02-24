<?php

namespace App\Http\Controllers;

use App\Criteria\Coupons\CouponsOfManagerCriteria;
use App\Criteria\Coupons\CouponsOfUserCriteria;
use App\Criteria\Foods\FoodsOfUserCriteria;
use App\Criteria\Restaurants\ActiveCriteria;
use App\Criteria\Restaurants\RestaurantsOfUserCriteria;
use App\DataTables\CouponDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Repositories\CouponRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DiscountableRepository;
use App\Repositories\FoodRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\CategoryRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class SalesController extends Controller 
{   
    public function index()
    {
        return "hello";
    }
}