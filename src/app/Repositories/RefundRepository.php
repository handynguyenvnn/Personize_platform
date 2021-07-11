<?php

namespace App\Repositories;

use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return Refund::class;
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
    public function getRefunds($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
}
