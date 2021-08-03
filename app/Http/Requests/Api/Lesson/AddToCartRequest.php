<?php

namespace App\Http\Requests\Api\Lesson;

use App\Rules\Api\CheckLesson;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
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
            'token'     => 'required' ,
            'lesson'    => ['required', 'exists:lessons,id', new CheckLesson()]
        ];
    }
}
