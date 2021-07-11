<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class PointPurchasesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'nick_name' => $this->user->nick_name,
            ] : NULL,
            'transactions_id' => $this->transactions_id,
            'stripe_transactions_id' => $this->stripe_transactions_id,
            'package_id' => $this->package_id,
            'points' => $this->points,
            'value' => $this->value,
            'payment_type' => $this->payment_type,
            'package' => $this->package ? [
                'id' => $this->package->id,
                'name' => $this->package->nick_name,
            ] : NULL,
            'type' => $this->type,
            'status' => $this->status,
            'other_info' => $this->other_info,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
