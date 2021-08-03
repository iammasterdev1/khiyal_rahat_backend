<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleLandingProductResource extends JsonResource
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
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'product_name' => $this->product_name,
            'price_irr' => $this->price_irr,
            'price_irr_after_off' => $this->price_irr_after_off,
            'cover_image' => $this->cover_image,
            'stock' => $this->stock,
            'permalink' => $this->permalink
        ];
    }
}
