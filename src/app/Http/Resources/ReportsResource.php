<?php


namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helper\Constant;
use Illuminate\Support\Facades\Config;

class ReportsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'nick_name' => $this->user->nick_name,
            ] : NULL,
            'event' => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title,
                'status' => $this->event->status,
            ] : NULL,
            'description' => $this->description,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME)
        ];
    }
}
