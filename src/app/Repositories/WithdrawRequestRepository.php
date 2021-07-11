<?php

namespace App\Repositories;

use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawRequestRepository extends BaseRepository
{
    protected $perPage = 10;

    public function getModel()
    {
        return WithdrawRequest::class;
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
    public function getWithdrawRequests($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    // for admin
    public function getAmountUnreadWithdrawRequests() {
        return [
            "unread" => $this->model->where('is_read', FALSE)->count(),
            "total" => $this->model->count()
        ];
    }

    // for admin
    public function setWithdrawRequestsReadStatus($request) {
        DB::beginTransaction();
        try {
            foreach ($request->all() as $item) {
                $this->model
                    ->findOrFail($item['id'])
                    ->update(['is_read' => $item['is_read']]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // for admin
    public function getWithdrawRequestById($id) {
        return $this->model
            // ->withTrashed()
            ->findOrFail($id);
    }

    // for admin
    public function getWithdrawRequestsByUserId($id) {
        return $this->model
            // ->withTrashed()
            ->whereUserId($id)
            ->orderByDesc('created_at')
            ->get();
    }
}
