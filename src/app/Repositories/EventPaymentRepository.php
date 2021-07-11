<?php

namespace App\Repositories;

use App\Models\EventPayment;

class EventPaymentRepository extends BaseRepository
{
    protected $limit = 10;

    public function getModel()
    {
        return EventPayment::class;
    }

    public function getById($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    // for admin
    public function getEventPayments($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
}
