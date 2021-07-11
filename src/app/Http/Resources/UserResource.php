<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helper\Constant;
use Illuminate\Support\Facades\Config;

class UserResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'banking'=>$this->banking,
            'email' => $this->email,
            'role' => $this->role,
            'provider_id' => $this->provider_id,
            'provider' => $this->provider,
            'avatar' => $this->avatar,
            'age' => $this->age,
            'sex' => $this->sex,
            'address' => $this->address,
            'description' => $this->description,
            'balance' => $this->balance,
            'country_id' => $this->country_id,
            'prefecture_id' => $this->prefecture_id,
            'follow_me_count' => $this->follow_me_count ? $this->follow_me_count : null,
            'follow_me' => $this->followMe ? $this->followMe : null,
            'follow_people_count' => $this->follow_people_count ? $this->follow_people_count : null,
            'event_comming' => $this->event_comming ? new ListEventCollection($this->event_comming) : null,
            'event_pass' => $this->event_pass ? new ListEventCollection($this->event_pass) : null,
            'my_event' => $this->my_event ? new ListEventCollection($this->my_event) : null,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->format(Constant::FORMAT_DATETIME) : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format(Constant::FORMAT_DATETIME) : null,
            'country_user' => isset($this->countryUser) ? $this->countryUser : null,
            'prefecture_user' => isset($this->prefectureUser) ? $this->prefectureUser : null,
            'configuration' => isset($this->configuration) ? $this->configuration : null,
        ];
    }
}
