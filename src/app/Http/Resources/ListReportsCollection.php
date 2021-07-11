<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ListReportsCollection extends ResourceCollection
{
    public $collects = ReportsResource::class;

    public function toArray($request)
    {
        return [
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'unread' => $this->unread,
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'data' => $this->collection,
        ];
    }
}
