<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPayment extends Model
{
    use HasFactory;
    protected $table = 'event_payment';
    protected $fillable = ['id', 'event_id', 'transactions_id', 'target_transactions_id', 'user_id', 'target_user_id', 'points', 'created_at', 'updated_at'];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id')->withTrashed();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function target_user() {
        return $this->belongsTo(User::class, 'target_user_id')->withTrashed();
    }
}
