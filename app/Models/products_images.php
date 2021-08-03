<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1, string $string2)
 * @method static find($get)
 */
class products_images extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_path' ,
        'image_alt' ,
        'status'
    ];



    public function imageForProducts (){

        return $this->morphTo();

    }

}
