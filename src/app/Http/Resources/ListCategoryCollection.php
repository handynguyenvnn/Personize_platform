<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ListCategoryCollection extends ResourceCollection
{
    public $collects = CategoryResource::class;

    public function toArray($request)
    {
        return $this->collection;
    }
}
