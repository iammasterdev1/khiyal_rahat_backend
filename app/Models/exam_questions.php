<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed exam_id
 * @property mixed true_answer
 * @property mixed|string answer_four
 * @property mixed|string answer_three
 * @property mixed|string answer_two
 * @property mixed|string answer_one
 * @property mixed|string question
 */
class exam_questions extends Model
{
    use HasFactory;
}
