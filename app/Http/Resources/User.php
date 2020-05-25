<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'id'=>$this->id,
            'first'=>$this->first,
            'last'=>$this->last,
            'name' =>$this->name,
            'email' =>$this->email,
            'role' =>$this->role['name'],
            'photo'=>asset($this->profile['photo']),
            'bio' =>$this->bio,
            'created_at' =>$this->created_at->format('Y-m-d H:i:s'),
            'updated_at' =>$this->updated_at->format('Y-M-D H:i:s'),
        ];
    }
}
