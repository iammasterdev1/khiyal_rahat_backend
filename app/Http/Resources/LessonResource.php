<?php

namespace App\Http\Resources;

use App\Models\Poster;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'id'             =>  $this->id,
            'title'          =>  $this->title,
            'description'    =>  $this->description,
            'cover'          =>  $this->cover,
            'price'          =>  $this->price,
            'permalink'      =>  $this->permalink,
            'responsive_image'      =>  $this->responsive_image,
            'small_banner'      =>  $this->small_banner,
            'comments'      =>  CommentResource::collection($this->comments),
            'images'         =>  ImageResource::collection($this->images),
            'topics'         =>  TopicResource::collection($this->topics),
            'monetary'       =>  PosterResource::collection($this->posters->where('status', Poster::ACTIVE)->where('free', Poster::MONETARY)),
            'free'           =>  PosterResource::collection($this->posters->where('status', Poster::ACTIVE)->where('free', Poster::FREE)),
        ];
    }
}
