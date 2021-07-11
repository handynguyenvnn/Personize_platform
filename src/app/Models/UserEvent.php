<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends BaseModel
{
    use HasFactory;

    const IS_NOTIFICATION = 1;

    protected $table = 'user_event';

    protected $fillable = [
        'id', 'event_id', 'user_id', 'is_notification', 'status', 'created_at', 'updated_at', 'deleted_at'
    ];

}
