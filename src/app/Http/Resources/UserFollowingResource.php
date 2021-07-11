<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helper\Constant;
use Illuminate\Support\Facades\Config;

class UserFollowingResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'email' => $this->email,
            'role' => $this->role,
            'provider_id' => $this->provider_id,
            'provider' => $this->provider,
            'avatar' => $this->avatar,
            'age' => $this->age,
            'sex' => $this->sex,
            'address' => $this->address,
            'description' => $this->description,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
