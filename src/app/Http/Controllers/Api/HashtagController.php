<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\HashtagRepository;
use App\Services\FileService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\ListHashtagCollection;
class HashtagController extends Controller
{
    protected $hashtagReponsitory;

    public function __construct(HashtagRepository $hashtagReponsitory)
    {
        $this->hashtagReponsitory = $hashtagReponsitory;
    }

    public function searchHashtag(Request $request) {
        try {
        $listHashtag = $this->hashtagReponsitory->searchHashtag($request);
        return responseOK(new ListHashtagCollection($listHashtag));
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        
    }
}
