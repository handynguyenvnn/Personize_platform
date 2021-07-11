<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLive extends Model
{
    use HasFactory;

    const IS_NOTIFICATION = 1;

    protected $table = 'events_live';
    public $timestamps = false;

    protected $fillable = [
        'id', 'events_id', 'users_id', 'type', 'token', 'sk_id', 'isPin', 'created_at'
    ];

}
