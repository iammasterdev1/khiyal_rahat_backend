<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageResource extends JsonResource
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
            'id'                  =>  $this->id,
            'title'               =>  $this->title,
            'meta_description'    =>  $this->meta_description,
            'permalink'           =>  $this->permalink,
            'description'         =>  $this->description,
            'banners'             =>  LandingPageBannersResource::collection($this->images)
        ];
    }
}
