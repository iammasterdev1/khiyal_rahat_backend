<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed course_id
 * @property mixed user_id
 * @property false|mixed|resource data
 */
class purchases_courses extends Model
{
    use HasFactory;

    public function teacher (){
        return $this->belongsTo(
            User::class ,
            'owner' ,
            'id'

        );
    }
    public function courseInformation(){
        return $this->belongsTo(
            school_courses::class ,
            'course_id' ,
            'id'
        );
    }
}
