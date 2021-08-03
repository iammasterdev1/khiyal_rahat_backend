<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class admin_add_available_colors extends FormRequest
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
            'color' => 'required' ,
            'color_code' => 'required'
        ];
    }
}
