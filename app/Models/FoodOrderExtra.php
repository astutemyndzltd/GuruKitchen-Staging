<?php
/**
 * File name: FoodOrder.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Models;

use Eloquent as Model;

class FoodOrderExtra extends Model
{

    public $table = 'food_order_extras';
    public $timestamps = false;
    

    public $fillable = [
        'food_order_id',
        'extra_id',
        'price'
    ];

    protected $casts = [
        'food_order_id' => 'integer',
        'extra_id' => 'integer',
        'price' => 'double'
    ];

   
    public function foodOrder() 
    {
        $this->belongsTo(\App\Models\FoodOrder::class, 'food_order_id', 'id');
    }
    
}
