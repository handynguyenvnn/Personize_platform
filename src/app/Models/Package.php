<?php

namespace App\Models;

use App\Services\FileService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Package extends Model
{
    use HasFactory;

    protected $table = 'package_purchases';

    protected $fillable = [
        'points',
        'value',
        'cover',
        'payment_method',
        'currency'
    ];
    public function getCoverAttribute($value)
    {
        if ($value && (substr($value, 0,4) == 'http' || substr($value, 0,5) == 'https' )) {
            $full_path = $value;
        } else {
            $fileService = new FileService(Config::get('filesystems.type_disks_upload'), '');
            $full_path = $value ? $fileService->getFullFilePath($value) : '';

        }
        return $full_path;
    }
}
