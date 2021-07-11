<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    use HasFactory;

    const CATEGORY_ADMIN = 1;

    public function getIconAttribute($value)
    {
        return config('app.url')."/".$value;
    }
}
