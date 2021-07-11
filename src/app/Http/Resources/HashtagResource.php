<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class HashtagResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_hashtag' => $this->parent_hashtag,
            'hashtag' => $this->hashtag,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
        ];
    }
}
