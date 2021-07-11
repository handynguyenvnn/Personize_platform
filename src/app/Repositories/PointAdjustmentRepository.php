<?php

namespace App\Repositories;

use App\Models\PointAdjustment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointAdjustmentRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return PointAdjustment::class;
    }

    public function getList($request)
    {
        if (Auth::check()) {
            return $this->model
                ->where('user_id', auth()->user()->id)
                ->orderByDesc('created_at')
                ->paginate($request->limit ? $request->limit : $this->perPage);
        }
        return [];
    }

    // for admin
    public function getPointAdjustments($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
}
