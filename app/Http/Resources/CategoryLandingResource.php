<?php

namespace App\Http\Resources;

use App\Models\ecommerce_products;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryLandingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'cat'       => $this->cat,
            'permalink' => $this->permalink,
            'cat_image' => $this->cat_image,
            'products'  => SingleLandingProductResource::collection(ecommerce_products::
            select('ecommerce_products.*')
                ->leftJoin('category_product', 'ecommerce_products.id', '=', 'category_product.ecommerce_product_id')
                ->where('category_product.category_id', $this->id)
                ->where('ecommerce_products.status', ecommerce_products::ACCEPTED)
                ->where('ecommerce_products.landing_id', $request->landing->id)
                ->distinct()
                ->get())
        ];
    }
}
