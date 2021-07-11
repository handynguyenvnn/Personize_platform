<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $table = 'hashtags';

    protected $fillable = ['hashtag'];

    public function event() {
        return $this->belongsToMany(Event::class, 'events_hashtags', 'category_id', 'event_id');
    }
}
