<?php

namespace App\Repositories;

use App\Models\UserEvent;
use App\Models\Event;
use App\Models\User;
use App\Services\RefundService;
use App\Services\PointAdjustmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;


class UserEventRepository extends BaseRepository
{
    private $refundService;
    private $pointAdjustmentService;

    public function __construct() {
        $this->refundService = new RefundService();
        $this->pointAdjustmentService = new PointAdjustmentService();
    }

    public function getModel()
    {
        return UserEvent::class;
    }

    public function checkUserEvent($event_id,$user_id) {
        return Event::where('id', $event_id)
        ->where('user_id', $user_id)->first();
    }

    public function cancelEvent($event_id, $cancel_reason)
    {
        DB::beginTransaction();

        try {
            $event_info = Event::findOrFail($event_id);
           
            if ($event_info) {
                // when cancel event, return money to user, minus the owner's money
                $number_subscribed_users = UserEvent::where('event_id', $event_id)->count();

                $event_points = $event_info->points;
                // only do refunds and point adjustments if the event has a 'points' value and subscribed users
                if($event_points > 0 && $number_subscribed_users > 0) {
                    // number of points to subtract from event owner's balance
                    $minus_points = $number_subscribed_users * $event_points;
                    $owner_user = User::findOrFail($event_info->user_id);
                    $context = 'イベントのキャンセル - 理由：';
    
                    if($owner_user) {
                        // adjust event owner's balance, create transaction and add row to point adjustment's table
                        $this->pointAdjustmentService->adjustPoints($event_info->user_id, $minus_points, $context . $cancel_reason);
                    }
    
                    // return money to user
                    $subscribed_users = UserEvent::where('event_id', $event_id)->get();

                    if($subscribed_users) {
                        foreach ($subscribed_users as $subscribed_user){

                            $user = User::where('id', $subscribed_user->user_id)->first();
                            Log::debug($user);
    
                            if(isset($user)) {
                                // refund subscribed user, create transaction and add row to point refunds's table
                                $this->refundService->refund($subscribed_user->user_id, $event_points, $context . $cancel_reason);
                            }
                        }
                    }
                }

                $event_info->update([
                    'status' => 4,
                    'cancel_reason' => $cancel_reason
                ]);

                DB::commit();
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
