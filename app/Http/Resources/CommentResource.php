<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        include_once "jdate.php";
        return [
            'comment' => $this->comment,
            'created' => $this->created_at,
            'user_info' => "{$this->user->firstName} {$this->user->lastName}"
        ];
    }
}
