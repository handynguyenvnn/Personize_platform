<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'link_stream' => $this->link_stream,
            'image' => $this->image,
            'image_banner' => $this->image_banner,
            'time' => Carbon::parse($this->time)->format(Constant::FORMAT_TIME),
            'date' => Carbon::parse($this->date)->format(Constant::FORMAT_DATE),
            'points' => $this->points,
            'hashtag' => $this->hashtag,
            'capacity' => $this->capacity,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'user_create' => new UserResource($this->userCreate),
            'other_event' => $this->other_event ? $this->other_event : null,
            'user_subscribe_event_count' => $this->user_subscribe_event_count,
            'user_live_event_count' => $this->user_live_event_count ? $this->user_live_event_count : null,
            'user_subscribe_event' => $this->userSubscribeEvent,
            'category_info' => isset($this->category) ? $this->category : null,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format(Constant::FORMAT_DATETIME) : null,
            'join_permission' => $this->join_permission ? $this->join_permission : null,
            'is_host_subhost' => $this->is_host_subhost ? $this->is_host_subhost : null
        ];
    }
}
