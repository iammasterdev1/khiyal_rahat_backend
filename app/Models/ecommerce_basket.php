<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1, $get)
 * @property mixed user_id
 * @property int|mixed product_id
 */
class ecommerce_basket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getProduct (){

        return $this->belongsTo(
            ecommerce_products::class ,
            'product_id' ,
            'id'
        );

    }

}
