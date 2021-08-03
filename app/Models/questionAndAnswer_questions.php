<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed user_id
 * @property mixed|string question
 * @method static find($get)
 */
class questionAndAnswer_questions extends Model
{
    use HasFactory;

    public function productsQuestions(){
        return $this->morphTo(
            'product'
        );
    }

}
