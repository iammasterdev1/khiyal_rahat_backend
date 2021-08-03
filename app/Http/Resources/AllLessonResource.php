<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllLessonResource extends JsonResource
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
            'cover'               =>  $this->cover,
            'responsive_image'    =>  $this->responsive_image,
            'permalink'           =>  $this->permalink,
            'price'               =>  $this->price
        ];
    }
}
