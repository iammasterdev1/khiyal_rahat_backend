<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class student_get_invoice_ecommerce extends FormRequest
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
            'token'             => 'required' ,
            'shipping_method'   => 'required' ,
            'shipping_address'  => 'required'
        ];
    }
}
