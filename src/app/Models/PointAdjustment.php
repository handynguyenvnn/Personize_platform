<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointAdjustment extends Model
{
    use HasFactory;

    protected $table = 'point_adjustments';

    protected $fillable = [
        'id', 'user_id', 'transactions_id', 'points', 'reason', 'created_at', 'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}

