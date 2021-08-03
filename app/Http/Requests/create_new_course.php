<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class create_new_course extends FormRequest
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
            "course_name"           => "required" ,
            "token"                 => "required" ,
            "course_description"    => "required" ,
            "price"                 => "required" ,
            'owner'                 => 'required' ,
            'cat_id'                => 'required' ,
            'spot_code'             => 'required' ,
            'image'                 => 'required' ,
            'mobile_banner'         => 'required' ,
            'big_banner'            => 'required' ,
        ];
    }
}
