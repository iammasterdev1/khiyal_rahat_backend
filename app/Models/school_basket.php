<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1, $id)
 * @property mixed user_id
 * @property int|mixed course_id
 */
class school_basket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function coursesInCart(){
        return $this->morphTo();
    }

}
