<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BannerRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class BannerController extends Controller
{
    protected $bannerRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    public function checkEventBanners(Request $request) {
        try {
          $data = $this->bannerRepository->checkEventBanners($request);
          return responseOK($data);
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

}
