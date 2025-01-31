<?php
/**
 * File name: UserAPIController.php
 * Last modified: 2020.05.21 at 17:25:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API\Driver;

use App\Events\UserRoleChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DriverRepository;
use App\Repositories\DriversPayoutRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\DB;

class UserAPIController extends Controller
{
    private $userRepository;
    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;
    private $driversPayoutRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DriverRepository $driverRepository, UserRepository $userRepository, 
    UploadRepository $uploadRepository, RoleRepository $roleRepository, 
    CustomFieldRepository $customFieldRepo, DriversPayoutRepository $driversPayoutRepository)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->driverRepository = $driverRepository;
        $this->customFieldRepository = $customFieldRepo;
        $this->driversPayoutRepository = $driversPayoutRepository;
    }

    function login(Request $request)
    {
        try {
            
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                // Authentication passed...
                $user = auth()->user();
                $user->device_token = $request->input('device_token', '');
                $user->save();

                if ($user->hasRole('driver')) {
                    $user->info = $this->driverRepository->where('user_id', '=', $user->id)->first();
                }

                return $this->sendResponse($user, 'User retrieved successfully');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required',
            ]);
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->device_token = $request->input('device_token', '');
            $user->password = Hash::make($request->input('password'));
            $user->api_token = str_random(60);
            $user->save();

            $user->assignRole('driver');

            event(new UserRoleChangedEvent($user));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }


        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function logout(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
        if (!$user) {
            return $this->sendError('User not found', 401);
        }
        try {
            auth()->logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
        return $this->sendResponse($user['name'], 'User logout successfully');

    }

    function user(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->sendError('User not found', 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }


    function settings(Request $request)
    {
        $settings = setting()->all();
        $settings = array_intersect_key($settings,
            [
                'default_tax' => '',
                'default_currency' => '',
                'default_currency_decimal_digits' => '',
                'app_name' => '',
                'currency_right' => '',
                'enable_paypal' => '',
                'enable_stripe' => '',
                'enable_razorpay' => '',
                'main_color' => '',
                'main_dark_color' => '',
                'second_color' => '',
                'second_dark_color' => '',
                'accent_color' => '',
                'accent_dark_color' => '',
                'scaffold_dark_color' => '',
                'scaffold_color' => '',
                'google_maps_key' => '',
                'fcm_key' => '',
                'mobile_language' => '',
                'app_version' => '',
                'enable_version' => '',
                'distance_unit' => '',
                'stripe_key' => ''
            ]
        );

        if (!$settings) {
            return $this->sendError('Settings not found', 401);
        }

        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function update($id, Request $request)
    {
        
        
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse(['error' => true, 'code' => 404, ], 'User not found');
        }

        $input = $request->except(['password', 'api_token']);

        try {

            if ($request->has('device_token')) {
                $user = $this->userRepository->update($request->only('device_token'), $id);
            } 
            else {
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
                $user = $this->userRepository->update($input, $id);

                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $user->customFieldsValues()->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }

            if ($user->hasRole('driver')) {
                $user->info = $this->driverRepository->where('user_id', '=', $user->id)->first();
            }

        } 
        catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }


    function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(true, 'Reset link was sent successfully');
        } 
        else {
            return $this->sendError([
                'error' => 'Reset link not sent',
                'code' => 401,
            ], 'Reset link not sent');
        }

    }

    function setAvailable($id, Request $request) 
    {
        try {
            $user = $this->userRepository->findWithoutFail($id);
            $info = $this->driverRepository->where('user_id', '=', $id)->first();
            $info->available = $request->input('value');
            $info->save();
            $user->info = $info;
            return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
        }
        catch(Exception $e) {
            return $this->sendError('Settings not found', 401);
        }
    }

    function getAvailability($id) {

        try {     
            $driverInfo = $this->driverRepository->where('user_id', '=', $id)->first();
            return $this->sendResponse($driverInfo, __('lang.updated_successfully', ['operator' => __('lang.user')]));
        }
        catch(Excepion $e) {
            return $this->sendError('Not found', 401);
        }

    }

    function resetAvailability() {
        DB::table('drivers')->update(['available' => false]);
    }

    public function getEarningAndPayout($id) 
    {
        try {
            $statement = "select count(*) orders, sum(p.price) total, sum(o.delivery_fee) delivery_fee 
            from orders o join payments p on o.payment_Id = p.id where o.active = 1 and o.order_status_id = 5 
            and driver_paid_out = 0 and o.driver_id = $id";

            $result = DB::select(DB::raw($statement));

            $dc = setting('driver_commission', 0);
            $commission = ($result[0]->total - $result[0]->delivery_fee) * ($dc / 100);
            $earning = $commission + $result[0]->delivery_fee;

            $payouts = $this->driversPayoutRepository->where('driver_id', $id)->get();
            
            return $this->sendResponse([ 'orders' => $result[0]->orders, 'earning' => getPriceOnly($earning), 'payout' => $payouts ], 'Retrieved successfully');
        }
        catch(RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
