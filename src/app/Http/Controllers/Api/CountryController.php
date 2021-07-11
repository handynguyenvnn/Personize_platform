<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CountryRepository;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function getList(Request $request) {
        try {
          $data = $this->countryRepository->getList($request);
          return responseOK($data);
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
