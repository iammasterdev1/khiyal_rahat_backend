<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed image
 */
class admin_addProduct extends FormRequest
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
            'product_name' => 'required' ,
            'cat_id' => 'required' ,
            'product_description' => 'required' ,
            'price' => 'required' ,
            'stock' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:5120' ,
            'brand' => 'required'
        ];
    }
}
