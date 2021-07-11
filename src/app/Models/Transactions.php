<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'created_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
