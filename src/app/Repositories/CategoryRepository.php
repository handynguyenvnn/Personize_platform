<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryRepository extends BaseRepository
{

    public function getModel()
    {
        return Category::class;
    }

    function list($request) {
        $query = $this->model;
        if ($request->create_stream) {
            if (Auth::check()) {
                if (auth()->user()->role === 1 || auth()->user()->role === 2) {
                    return $query->where('id', '>', 1)->get();
                }
            }
            return $query->where('id', '>', 2)->get();
            // return $query->whereNull('is_admin')->get();
        } else {
            return $query->all();
        }
    }
}
