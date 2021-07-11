<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    const USER_ROLE_ADMIN = 1;
    const USER_ROLE_MANAGER = 2;
    const USER_LOGIN_FORM = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password', 'role', 'provider_id', 'provider', 'avatar', 'age', 'sex', 'address',
        'balance', 'description', 'email_verified_at', 'nick_name', 'country_id', 'prefecture_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function followMe()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_follow_id', 'user_id')
            ->wherePivot('deleted_at', null);
    }

    public function followOfMe()
    {
        return $this->hasMany(UserFollow::class, 'user_id');
    }

    public function followPeople()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'user_follow_id')
            ->wherePivot('deleted_at', null);
    }

    public function followPeopleOfMe()
    {
        return $this->hasMany(UserFollow::class, 'user_follow_id');
    }

    public function subscribeEvent()
    {
        return $this->belongsToMany(Event::class, 'user_event', 'user_id', 'event_id');
    }

    public function countryUser()
    {
        return $this->belongsToMany(Country::class, 'users', 'id', 'country_id');
    }

    public function prefectureUser()
    {
        return $this->belongsToMany(Prefecture::class, 'users', 'id', 'prefecture_id');
    }

    //protected $appends = ['avatar1'];

    public function getAvatarAttribute($value)
    {

        if ($value && (substr($value, 0, 4) == 'http' || substr($value, 0, 5) == 'https')) {
            $full_path = $value;
        } else {
            $fileService = new FileService(Config::get('filesystems.type_disks_upload'), '');
            $full_path = $value ?
            $fileService->getFullFilePath($value) :
            config('app.url') . "/" . Config::get('filesystems.disks_upload_path_avatar_null');

        }
        return $full_path;
    }
}
