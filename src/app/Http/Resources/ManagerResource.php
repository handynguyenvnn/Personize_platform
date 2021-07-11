<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Helper\Constant;
// use Illuminate\Support\Facades\Config;

class ManagerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nick_name' => $this->nick_name,
            'email' => $this->email,
        ];
    }
}
