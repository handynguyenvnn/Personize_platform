<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_id' => $this->type_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'message' => $this->message,
            'notification' => $this->notification,
            'is_action' => $this->is_action,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
