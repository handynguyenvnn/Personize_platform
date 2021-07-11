<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListCategoryCollection;
use App\Http\Resources\ListEventCollection;
use App\Repositories\CategoryRepository;
use App\Repositories\EventRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    //
    protected $categoryRepository;
    protected $eventRepository;

    public function __construct(CategoryRepository $categoryRepository, EventRepository $eventRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->eventRepository = $eventRepository;
    }

    public function getCategory(Request $request)
    {

        try {
            $categories = $this->categoryRepository->list($request);
            return responseOK(new ListCategoryCollection($categories));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function getCategoryMenu(Request $request)
    {

        try {
            // $categories = $this->categoryRepository->list($request);
            $categories = DB::table('categories')->get();
            $arr_categories = array(
                array(
                    'id' => 1,
                    'name' => '全て',
                    'icon' => 'icons/time.svg',
                    'is_admin' => 1,
                    'color' => '#1ca653',
                    'position' => 0,
                    'subTitle' => '',
                ),
            );
            foreach ($categories as $key => $category) {
                $arr_categories[$key + 1] = array(
                    'id' => $category->id + 1,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'is_admin' => $category->is_admin,
                    'color' => $category->color,
                    'position' => $category->position,
                    'subTitle' => $category->subTitle,
                )
                ;
            }
            return responseOK($arr_categories);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function listEvent($id, Request $request)
    {
        try {
            $categories = $this->categoryRepository->findOrFail($id);
            if ($id == 1) {
                $listEvent = $this->eventRepository->getEvents($request);
            } else {
                $listEvent = $this->eventRepository->list($categories, $request);
            }
            $listEvent->category_type = $categories;
            return responseOK(new ListEventCollection($listEvent));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
