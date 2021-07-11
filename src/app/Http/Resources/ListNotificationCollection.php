<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ListNotificationCollection extends ResourceCollection
{
    public $collects = NotificationResource::class;

    public function toArray($request)
    {
        return [
            'current_page' => $this->count() ? $this->currentPage() : 0,
            'last_page' => $this->count() ? $this->lastPage() : 0,
            'total' => $this->count() ? $this->total() : 0,
            'per_page' => $this->count() ? $this->perPage() : 0,
            'data' => $this->collection,
            'un_read' => $this->count() ? $this->notifications_un_read : 0,
            'auth_check' => Auth::check(),

        ];
    }
}
