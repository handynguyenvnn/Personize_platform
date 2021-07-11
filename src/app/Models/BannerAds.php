<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class BannerAds extends BaseModel
{
    use HasFactory;

    protected $table = 'banner_ads';

    protected $fillable = [
        'id', 'position', 'link', 'image', 'start_date', 'end_date', 'is_activated', 'created_at', 'updated_at'
    ];

    public function getImageAttribute($value) {
        if ($value && (substr($value, 0,4) == 'http' || substr($value, 0,5) == 'https' )) {
            $full_path = $value;
        } else {
            $fileService = new FileService(Config::get('filesystems.type_disks_upload'), '');
            $full_path = $value ? $fileService->getFullFilePath($value) : '';
        }
        return $full_path;
    }
}
