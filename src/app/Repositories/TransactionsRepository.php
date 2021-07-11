<?php

namespace App\Repositories;

use App\Models\Transactions;
use Illuminate\Support\Facades\Auth;

class TransactionsRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return Transactions::class;
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
    public function getTransactions($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    // for admin
    public function getTransactionById($id) {
        return $this->model
            // ->withTrashed()
            ->findOrFail($id);
    }

    // for admin
    public function getTransactionsByUserId($id) {
        return $this->model
            // ->withTrashed()
            ->whereUserId($id)
            ->orderByDesc('created_at')
            ->get();
    }
}
