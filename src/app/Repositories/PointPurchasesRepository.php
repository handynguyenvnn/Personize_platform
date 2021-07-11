<?php

namespace App\Repositories;

use App\Models\PointPurchases;
use Illuminate\Support\Facades\Auth;

class PointPurchasesRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return PointPurchases::class;
    }

    public function getList($request)
    {
        if (Auth::check()) {
            return $this->model
                ->whereUserId(\auth()->user()->id)
                ->orderByDesc('created_at')
                ->paginate($request->limit ? $request->limit : $this->perPage);
        }
        return [];
    }

    // for admin
    public function getPointPurchases($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
}
