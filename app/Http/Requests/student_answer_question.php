<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class student_answer_question extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required' ,
            'question_id' => 'required' ,
            'answer' => 'required'
        ];
    }
}
