<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class TransactionCollection extends ResourceCollection
{
    public $collects = TransactionResource::class;

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
