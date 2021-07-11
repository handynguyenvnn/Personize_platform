<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawTransaction extends Model
{
    use HasFactory;

    protected $table = 'withdraw_transactions';

    protected $fillable = [
        'id', 'user_id', 'transactions_id', 'withdraw_request_id', 
        'transaction_fee', 'transfer_fee', 'points', 
        'amount_before_fees', 'amount_after_fees', 'created_at', 'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function withdrawRequest() {
        return $this->belongsTo(WithdrawRequest::class, 'withdraw_request_id');
    }
}

