<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\EventLive;
use Illuminate\Support\Facades\Auth;

class NotificationRepository extends BaseRepository
{
    protected $perPage = 4;

    public function getModel()
    {
        return Notification::class;
    }

    public function getList($request)
    {
        if (Auth::check()) {
            return $this->model->with('notification.userCreate')
                ->whereUserId(\auth()->user()->id)
                ->orderByDesc('created_at')
                ->paginate($request->limit ? $request->limit : $this->perPage);
        }
        return [];
    }

    public function getCountUnReadNotification()
    {
        return $this->model->whereUserId(\auth()->user()->id)->whereNull('is_read')->count();
    }

    public function okNotification($request) {
        $notification = Notification::findOrFail($request->notiId);
        if($notification) {
            $notification->update(['is_action' => Notification::IS_ACTION_OK]);
            return $notification;
        }
        return null;
    }

    public function cancelNotification($request) {
        if(isset(auth()->user()->id)) {
            $notification = Notification::find($request->notiId);
            $eventLive = EventLive::where('events_id', $notification->type_id)->where('users_id', auth()->user()->id)->delete();
            Notification::where('id', $request->notiId)->delete();
            return true;
        }
        
        return null;
    }

}
