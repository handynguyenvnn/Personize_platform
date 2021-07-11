<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ListWithdrawRequestCollection extends ResourceCollection
{
    public $collects = WithdrawRequestResource::class;

    public function toArray($request)
    {
        return [
            'current_page' => $this->count() ? $this->currentPage() : 0,
            'last_page' => $this->count() ? $this->lastPage() : 0,
            'unread' => $this->unread,
            'total' => $this->count() ? $this->total() : 0,
            'per_page' => $this->count() ? $this->perPage() : 0,
            'data' => $this->collection,
            'auth_check' => Auth::check(),
        ];
    }
}
