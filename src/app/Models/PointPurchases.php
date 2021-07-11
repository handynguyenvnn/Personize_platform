<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointPurchases extends Model
{
    use HasFactory;

    protected $table = 'point_purchases';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'stripe_transaction_id',
        'package_id',
        'points',
        'payment_type',
        'type',
        'status',
        'other_info',
        'created_at',
        'updated_at',
    ];

    public function package()
    {
        return $this->hasOne('App\Models\Package', 'id', 'package_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
