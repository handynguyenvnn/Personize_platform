<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventHashtag extends Model
{
    use HasFactory;
    protected $table = 'events_hashtags';

    protected $fillable = ['event_id', 'hashtag_id'];
}
