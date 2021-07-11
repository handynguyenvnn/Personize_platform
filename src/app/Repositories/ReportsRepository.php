<?php

namespace App\Repositories;

use App\Models\Reports;
use Illuminate\Support\Facades\DB;

class ReportsRepository extends BaseRepository
{
    protected $limit = 10;

    public function getModel()
    {
        return Reports::class;
    }

    // for admin
    public function getReports($request) {
        return $this->model
            // ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->limit);
    }

    // for admin
    public function getAmountUnreadReports() {
        return [
            "unread" => $this->model->where('is_read', FALSE)->count(),
            "total" => $this->model->count()
        ];
    }

    // for admin
    public function setEventReportsReadStatus($request) {
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
    public function getReportById($id) {
        return $this->model->findOrFail($id);
    }

    //for client
    public function checkExistedReport($eventId, $userId) {
        return $this->model->where('events_id', $eventId)->where('user_id', $userId)->first();
    }
}
