<?php

namespace App\Repositories;

use App\Helper\Constant;
use App\Models\User;
use App\Models\Country;
use App\Models\Prefecture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CountryRepository extends BaseRepository
{
    protected $limit = 10;


    public function getModel()
    {
        return Country::class;
    }

    public function getList($request) {
        $countries = $prefectures = [];
        $query = $this->model;

        if($request->id) {
            $prefectures = Prefecture::where('country_id', $request->id)->get();
        } else {
            $prefectures = Prefecture::all();
        }
        $countries = $query->all();
            return  [
                'countries' => $countries,
                'prefectures' => $prefectures
            ];
    }
}
