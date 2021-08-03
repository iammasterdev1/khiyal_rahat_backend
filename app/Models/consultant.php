<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class consultant extends Model {

    use HasFactory;
    protected $table = 'consultant';

    public function consultant_features (){
        return $this->hasMany(
            consultant_features::class ,
            'user_id' ,
            'user_id'
        );
    }

    public function consultant_frequently_asked_questions (){
        return $this->hasMany(
            consultant_frequently_asked_questions::class ,
            'user_id' ,
            'user_id'
        );
    }

    public function consultant_previous_students (){
        return $this->hasMany(
            consultant_previous_students::class ,
            'user_id' ,
            'user_id'
        );
    }

    public function consultant_description (){
        return $this->hasMany(
            consultant_previous_students::class ,
            'user_id' ,
            'user_id'
        );
    }

    public function important_features (){
        return $this->hasMany(
            users_public_resume_items::class ,
            'user_id' ,
            'user_id'
        );
    }

    public function articles (){
        return $this->hasMany(
            articles::class ,
            'user_id' ,
            'user_id'
        );
    }

}
