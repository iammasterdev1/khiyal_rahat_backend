<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string exam_name
 * @property mixed course_id
 * @method static find($get)
 * @method static where(string $string, string $string1, int $int)
 */
class exams extends Model
{
    use HasFactory;

    public function showAllQuestions(){
        return $this->hasMany(
            exam_questions::class ,
            'exam_id' ,
            'id'
        );
    }

}
