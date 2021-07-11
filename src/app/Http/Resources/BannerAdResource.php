<?php


namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helper\Constant;
use Illuminate\Support\Facades\Config;

class BannerAdResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'link' => $this->link,
            'image' => $this->image,
            'start_date' => Carbon::parse($this->start_date)->format(Constant::FORMAT_DATE),
            'end_date' => Carbon::parse($this->end_date)->format(Constant::FORMAT_DATE),
            'is_activated' => $this->is_activated,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format(Constant::FORMAT_DATETIME) : null
        ];
    }
}
