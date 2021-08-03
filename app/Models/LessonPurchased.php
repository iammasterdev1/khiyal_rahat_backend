<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPurchased extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'lessons_purchased';

    public function lessonInformation(){
        return $this->belongsTo(
            Lesson::class ,
            'lesson_id' ,
            'id'
        );
    }
}
