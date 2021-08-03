<?php

namespace App\Http\Requests\Api\Search\Product;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'q'          => 'nullable|string',
            'categories' => 'nullable',
            'brands'     => 'nullable'
        ];
    }
}
