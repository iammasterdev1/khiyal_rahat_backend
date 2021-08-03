<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1, string $string2)
 * @method static find($get)
 */
class products_features extends Model
{
    use HasFactory;

    protected $fillable = [
        'index' ,
        'value'
    ];

    public function productFeature (){

        return $this->morphTo();

    }

}
