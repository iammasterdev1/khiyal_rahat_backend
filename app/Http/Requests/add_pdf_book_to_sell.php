<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed book
 */
class add_pdf_book_to_sell extends FormRequest
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
            "book" => "mimes:pdf|required" ,
            'title' => 'required' ,
            'description' => 'required' ,
            'irr_price' => 'required' ,
            'cat_id' => 'required'
        ];
    }
}
