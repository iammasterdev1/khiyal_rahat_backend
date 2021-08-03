<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageBannersResource extends JsonResource
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
            'image_path'    =>  $this->image_path,
            'link'          =>  $this->link,
            'image_alt'     =>  $this->image_alt
        ];
    }
}
