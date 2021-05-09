<?php
/**
 * File name: DriversPayout.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Eloquent as Model;

/**
 * Class DriversPayout
 * @package App\Models
 * @version March 25, 2020, 9:48 am UTC
 *
 * @property \App\Models\User user
 * @property integer user_id
 * @property string method
 * @property double amount
 * @property dateTime paid_date
 * @property string note
 */
class DriversPayout extends Model
{

    public $table = 'drivers_payouts';
    
    public $fillable = [
        'driver_id',
        'from_date',
        'to_date',
        'orders',
        'subtotal',
        'delivery_fee',
        'driver_commission',
        'amount',
        'note'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'driver_id' => 'integer',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'orders' => 'integer',
        'subtotal' => 'double',
        'delivery_fee' => 'double',
        'driver_commission' => 'double',
        'amount' => 'double',
        'note' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'driver_id' => 'required|exists:users,id',
        'amount' => 'required|min:0.01',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',    
    ];

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
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'user_id', 'user_id');
    }
    
}
