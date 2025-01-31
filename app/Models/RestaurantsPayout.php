<?php
/**
 * File name: RestaurantsPayout.php
 * Last modified: 2020.04.30 at 08:21:09
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Eloquent as Model;

/**
 * Class RestaurantsPayout
 * @package App\Models
 * @version March 25, 2020, 9:48 am UTC
 *
 * @property \App\Models\Restaurant restaurant
 * @property integer restaurant_id
 * @property string method
 * @property double amount
 * @property dateTime paid_date
 * @property string note
 */
class RestaurantsPayout extends Model
{

    public $table = 'restaurants_payouts';
    
    public $fillable = [
        'restaurant_id',
        'from_date',
        'to_date',
        'orders',
        'gross_revenue',
        'admin_commission',
        'delivery_fee',
        'driver_commission',
        'driver_commission_rate',
        'tax',
        'amount',
        'note',
        'created_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'restaurant_id' => 'integer',
        'from_date' => 'date',
        'to_date' => 'date',
        'orders' => 'integer',
        'gross_revenue' => 'double',
        'driver_commission' => 'double',
        'driver_commission_rate' => 'double',
        'admin_commission' => 'double',
        'tax' => 'double',
        'amount' => 'double',
        'note' => 'string',
        'created_at' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'restaurant_id' => 'required|exists:restaurants,id',
        'from_date' => 'required',
        'to_date' => 'required',
        'amount' => 'required|gt:0',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'restaurant'  
    ];

    public function getRestaurantAttribute()
    {
        return $this->restaurant()->first(['id', 'name', 'address', 'phone']);
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class, 'restaurant_id', 'id');
    }
    
}
