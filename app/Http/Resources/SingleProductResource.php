<?php

namespace App\Http\Resources;

use App\Models\comments;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductResource extends JsonResource
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
            'product_description' => $this->product_description,
            'stock' => $this->stock,
            'price_irr' => $this->price_irr,
            'price_irr_after_off' => $this->price_irr_after_off,
            'cover_image' => $this->cover_image,
            'permalink' => $this->permalink,
            'en_title' => $this->en_title,
            'meta_description' => $this->meta_description,
            'brand' => new SingleBrandProductResource($this->brand),
            'category' => SingleCategoryProductResource::collection($this->allCategories),
            'gallery' => $this->productImages,
            'important_features' => SingleImportantFeatureResource::collection($this->important_features),
            'comments' => CommentResource::collection($this->comments->where('status', comments::ACTIVE)),
            'technicals' => SingleTechnicalProductResource::collection($this->technicals),
            'type' => $this->type == 0 ? 'Color' : 'Size',
            'types' => $this->type == 0 ? SingleColorProductResource::collection($this->colors) : SingleSizeProductResource::collection($this->sizes)
        ];
    }
}
