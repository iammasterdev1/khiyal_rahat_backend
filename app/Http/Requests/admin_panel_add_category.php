<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class admin_panel_add_category extends FormRequest
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
            "token" => "required" ,
            "cat_name" => "required",
            "cat_of" => 'required|min:1|max:2' ,
            'image' => 'required|max:5120'

        ];
    }
}
