<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends BaseModel
{
    use HasFactory;

    const TYPE_EVENT = 1;
    const TYPE_INVITED = 2;
    const IS_READ = 1;

    const IS_ACTION_OK = 1;

    protected $table = 'notifications';

    protected $fillable = [
        'id', 'type', 'type_id', 'user_id', 'is_read', 'action', 'message', 'is_action', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function notification()
    {
        return $this->morphTo('notification', 'type', 'type_id', );
    }
}
