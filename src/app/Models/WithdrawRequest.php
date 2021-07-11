<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    const IS_NOTIFICATION = 1;

    protected $table = 'withdraw_requests';

    protected $fillable = [
        'id', 'user_id', 'code', 'status', 'description', 'point', 'amount', 'is_read', 'created_at', 'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}

