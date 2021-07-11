<?php

namespace App\Repositories;

use App\Helper\Constant;
use App\Models\User;
use App\Models\BannerAds;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BannerRepository extends BaseRepository
{
    protected $limit = 10;

    public function getModel()
    {
        return BannerAds::class;
    }

    public function checkEventBanners($request)
    {
        $query = $this->model;
        $bannerData = $query->where('is_activated', 1)
            ->orderByDesc('start_date')
            ->orderByDesc('start_date')->get();

        return [
            'bannerData' => $bannerData,
        ];
    }

    // for admin
    public function getAdvertisements($request)
    {
        return $this->model
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->limit);
    }

    // for admin
    public function getById($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    // for admin
    public function deleteAdvertisement($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    //for client
    public function getAdvertisementTheDay()
    {
        $query = $this->model;
        $bannerDataA = $query->where('is_activated', 1)
            ->where('position', config('const.banner_a'))
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())->whereNull('deleted_at')->get();
        $bannerDataB = $query->where('is_activated', 1)
            ->where('position', config('const.banner_b'))
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())->whereNull('deleted_at')->get();

        $bannerDataC = $query->where('is_activated', 1)
            ->where('position', config('const.banner_c'))
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())->whereNull('deleted_at')->get();
        return [
            'bannerDataA' => $bannerDataA,
            'bannerDataB' => $bannerDataB,
            'bannerDataC' => $bannerDataC,
        ];
    }
}
