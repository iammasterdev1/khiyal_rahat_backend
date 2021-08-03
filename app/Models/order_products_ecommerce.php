<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed product_id
 * @property mixed order_id
 * @property mixed count
 */
class order_products_ecommerce extends Model
{
    use HasFactory;

    public function productInformation(){
        return $this->belongsTo(
            ecommerce_products::class ,
            'product_id'
        );
    }

}
