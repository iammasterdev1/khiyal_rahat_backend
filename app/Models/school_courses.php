<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string course_name
 * @property mixed|string course_description
 * @property int|mixed price_irr
 * @property mixed owner
 * @property bool|mixed status
 * @property mixed cat
 * @property int|mixed irr_price_after_off
 * @method static find($courseId)
 */
class school_courses extends Model
{
    use HasFactory;

    const ACTIVE = 1;

    protected $guarded = [];

    protected $hidden = [];


    public function teacher (){
        return $this->belongsTo(
            User::class ,
            'owner' ,
            'id'
        );
    }
    public function courseSections (){
        return $this->hasMany(
            coursed_titles::class ,
            "course_id" ,
            "id"
        );
    }

    public function coursesImages (){

        return $this->morphMany(
            products_images::class ,
            "product"
        );

    }

    public function courseFeatures(){

        return $this->morphMany(
            products_features::class ,
            "product"
        );

    }

    public function courseQuestions(){
        return $this->morphMany(
            questionAndAnswer_questions::class ,
            'product'
        );
    }

    public function courseComments(){
        return $this->morphMany(
            comments::class ,
            'commentable'
        );
    }

    public function archives()
    {
        return $this->morphMany(
            Archive::class ,
            'lessonable'
        );
    }

    public function inCart(){
        $this->morphMany(
            school_baskets::class ,
            'product'
        );
    }
    public function showAllSections(){
        return $this->hasMany(
            coursed_titles::class ,
            'course_id'
        );
    }

    public function scopeActives($q)
    {
        return $q->where('status', self::ACTIVE);
    }
}
