<?php

namespace App\Http\Resources;

use App\Helper\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'position' => $this->position,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at->format(Constant::FORMAT_DATETIME),
            'updated_at' => $this->updated_at->format(Constant::FORMAT_DATETIME),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format(Constant::FORMAT_DATETIME) : null,
            'subTitle' => $this->subTitle,
        ];
    }
}
