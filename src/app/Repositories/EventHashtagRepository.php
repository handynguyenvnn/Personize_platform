<?php

namespace App\Repositories;

use App\Models\Hashtag;
use App\Models\EventHashtag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventHashtagRepository extends BaseRepository
{

    public function getModel()
    {
        return EventHashtag::class;
    }

    public function list($request)
    {
        $query = $this->model;
        if ($request->create_stream) {
            return $query->whereNull('is_admin')->get();
        } else {
            return $query->all();
        }
    }

    public function updateHashtagEvent($hashtagIdsStr, $eventId)
    {
        try {
            DB::beginTransaction();
            $hashtagIds = explode(',', $hashtagIdsStr);
            $currentUser = auth()->user()->id;

            foreach ($hashtagIds as $hashtagId) {
                $arg = [
                    'user_id' => $currentUser,
                    'event_id' => $eventId,
                    'hashtag_id' => intval($hashtagId),
                    'created_at' => now()
                ];
                $this->create($arg);
            }
            DB::commit();

            return [
                'status' => true,
            ];
        } catch (\Exception $exception) {

            dd($exception);
            return [
                'status' => false,
            ];
        }
    }

    public function deleteByEventId($eventId)
    {
        $this->model->where('event_id', $eventId)->delete();
    }
}
