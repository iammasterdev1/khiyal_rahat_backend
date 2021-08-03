<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class teacher_addQuestionToExam extends FormRequest
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
            'exam_id' => 'required' ,
            'q1' => 'required' ,
            'q2' => 'required' ,
            'q3' => 'required' ,
            'q4' => 'required' ,
            'answer_number' => 'required' ,
            'question' => 'required'
        ];
    }
}
