<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class EventPaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event' => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title,
            ] : NULL,
            'transactions_id' => $this->transactions_id,
            'target_transactions_id' => $this->target_transactions_id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'nick_name' => $this->user->nick_name,
            ] : NULL,
            'target_user' => $this->target_user ? [
                'id' => $this->target_user->id,
                'nick_name' => $this->target_user->nick_name,
            ] : NULL,
            'points' => $this->points,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
