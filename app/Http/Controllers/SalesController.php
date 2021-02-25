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
    /** @var  CouponRepository */
    private $couponRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var FoodRepository
     */
    private $foodRepository;
    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var DiscountableRepository
     */
    private $discountableRepository;

    public function __construct(CouponRepository $couponRepo, CustomFieldRepository $customFieldRepo, FoodRepository $foodRepo
        , RestaurantRepository $restaurantRepo
        , CategoryRepository $categoryRepo , DiscountableRepository $discountableRepository)
    {
        parent::__construct();
        $this->couponRepository = $couponRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->foodRepository = $foodRepo;
        $this->restaurantRepository = $restaurantRepo;
        $this->categoryRepository = $categoryRepo;
        $this->discountableRepository = $discountableRepository;
    }

    /**
     * Display a listing of the Coupon.
     *
     * @param CouponDataTable $couponDataTable
     * @return Response
     */
    public function index(CouponDataTable $couponDataTable)
    {
        return $couponDataTable->render('coupons.index');
    }

}
