<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Event extends BaseModel
{
    use HasFactory, SoftDeletes;

    const STATUS_CANCEL = 4;
    const STATUS_COMING = 1;
    const STATUS_FINNISH = 3;

    const OFFICAL_EVENT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'events';

    protected $fillable = [
        'title', 'description', 'type', 'link_stream', 'image', 'image_banner', 'time', 'date', 'capacity', 'points', 'category_id', 'user_id', 'status'
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function userSubscribeEvent()
    {
        return $this->belongsToMany(User::class, 'user_event', 'event_id', 'user_id')
            ->wherePivot('deleted_at', null);
    }

    public function userLiveEvent()
    {
        return $this->belongsToMany(User::class, 'events_live', 'events_id', 'users_id' )
            ->wherePivot('events.deleted_at', null);
    }

    public function userIdSubscribeEvent()
    {
        return $this->hasOne(UserEvent::class, 'event_id')->whereUserId(auth()->user()->id);
    }

    public function notificationEvent()
    {
        return $this->hasMany(Notification::class, 'type_id')->whereType(Notification::TYPE_EVENT);
    }

    public function userNotificationEvent()
    {
        return $this->belongsToMany(User::class, 'notifications', 'type_id', 'user_id')
            ->wherePivot('type', Notification::TYPE_EVENT)
            ->wherePivot('deleted_at', null);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function hashtag()
    {
        return $this->belongsToMany(Hashtag::class, 'events_hashtags', 'event_id', 'hashtag_id');
    }

    public function getImageAttribute($value)
    {
        if ($value && (substr($value, 0, 4) == 'http' || substr($value, 0, 5) == 'https')) {
            $full_path = $value;
        } else {
            $fileService = new FileService(Config::get('filesystems.type_disks_upload'), '');
            $full_path = $value ? $fileService->getFullFilePath($value) : '';

        }
        return $full_path;
    }

    public function getImageBannerAttribute($value)
    {
        if ($value && (substr($value, 0, 4) == 'http' || substr($value, 0, 5) == 'https')) {
            $full_path = $value;
        } else {
            $fileService = new FileService(Config::get('filesystems.type_disks_upload'), '');
            $full_path = $value ? $fileService->getFullFilePath($value) : '';

        }
        return $full_path;
    }
}
