<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addNewAddress extends FormRequest
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
            'firstName' => 'required',
            'lastName' => 'required',
            'phone_number' => 'required',
            'province' => 'required' ,
            'city' => 'required' ,
            'address' => 'required' ,
            'national_code' => 'required' ,
            'postcode' => 'required'
        ];
    }
}
