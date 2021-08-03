<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class blog_add_new_article extends FormRequest
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
            'token'   => 'required' ,
            'title'   => 'required' ,
            'content' => 'required'
        ];
    }
}
