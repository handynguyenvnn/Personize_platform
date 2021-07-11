<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends BaseModel
{
    use HasFactory;

    protected $table = 'user_follow';
}
