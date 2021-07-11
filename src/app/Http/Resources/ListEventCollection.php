<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ListEventCollection extends ResourceCollection
{
    public $collects = EventResource::class;

    public function toArray($request)
    {
        return [
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'data' => $this->collection,
            'category' => !empty($this->category_type) ? $this->category_type : ''
        ];
    }
}
