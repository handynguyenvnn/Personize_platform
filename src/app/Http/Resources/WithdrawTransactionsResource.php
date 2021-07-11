<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawTransactionsResource extends JsonResource
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
            'withdraw_request_id' => $this->withdraw_request_id,
            'points' => $this->points,
            'transaction_fee' => $this->transaction_fee,
            'transfer_fee' => $this->transfer_fee,
            'amount_before_fees' => $this->amount_before_fees,
            'amount_after_fees' => $this->amount_after_fees,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
