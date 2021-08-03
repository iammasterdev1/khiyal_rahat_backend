<?php

namespace App\Http\Resources;

use App\Models\ecommerce_products;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingResource extends JsonResource
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
            'title'             => $this->title,
            'permalink'         => $this->permalink,
            'meta_description'  => $this->meta_description,
            'description'       => $this->description,
//            'categories'        => CategoryLandingResource::collection($this->categories),
            'products'  => new ProductCollection(ecommerce_products::
            select('ecommerce_products.*')
                ->leftJoin('category_product', 'ecommerce_products.id', '=', 'category_product.ecommerce_product_id')
                ->whereIn('category_product.category_id', $request->landing->categories()->get()->pluck('id'))
                ->where('ecommerce_products.status', ecommerce_products::ACCEPTED)
                ->where('ecommerce_products.landing_id', $request->landing->id)
                ->groupBy('ecommerce_products.id')
                ->paginate(2))
        ];
    }
}
