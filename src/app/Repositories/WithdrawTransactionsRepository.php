<?php

namespace App\Repositories;

use App\Models\WithdrawTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawTransactionsRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return WithdrawTransaction::class;
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
    public function getWithdrawTransactions($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
}
