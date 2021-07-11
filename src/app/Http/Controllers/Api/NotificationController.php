<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListNotificationCollection;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getNotifications(Request $request)
    {
        try {
            $notifications = $this->notificationRepository->getList($request);
            if ($notifications) {
                $notificationsUnread = $this->notificationRepository->getCountUnReadNotification();
                $notifications->notifications_un_read = $notificationsUnread;
            }
            return responseOK(new ListNotificationCollection($notifications));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function read()
    {
        try {
            $this->notificationRepository->updateClause('user_id', auth()->user()->id, [
                'is_read' => Notification::IS_READ,
                'updated_at' => now()
            ]);
            return responseOK(__("message.read_notification_success"));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function notificationAction(Request $request) {
        try {
            if(isset($request->type) && $request->type == 0) {
                $check = $this->notificationRepository->cancelNotification($request);
                if($check !== null) {
                    return responseOK([
                        'data' => null,
                        'msg' => 'message_server.notification_cancel',
                        'status' => true,
                    ]);
                }
            } 

            if(isset($request->type) && $request->type == 1) {
                $check = $this->notificationRepository->okNotification($request);
                if($check !== null) {
                    return responseOK([
                        'data' => $check,
                        'msg' => 'message_server.notification_ok',
                        'status' => true,
                    ]);
                }
            }
            return responseOK([
                'data' => null,
                'msg' => 'message_server.notification_error',
                'status' => true,
            ]);
            
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
