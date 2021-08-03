<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\order_products_ecommerce;
use App\Models\addresses;

/**
 * @property mixed user_id
 * @property int|mixed total_price
 * @property int|mixed final_price
 */
class orders extends Model
{
    use HasFactory;

    public function products (){
        return $this->hasMany(
            order_products_ecommerce::class ,
            'order_id'
        );
    }

    public function user_info (){
        return $this->belongsTo(
            User::class ,
            'user_id'
        );
    }

    public function address_info (){
        return $this->belongsTo(
            addresses::class ,
            'address'
        );
    }

}
