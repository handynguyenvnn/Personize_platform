<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SubscribeEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\ReportsResource;
use App\Repositories\EventHashtagRepository;
use App\Repositories\EventRepository;
use App\Repositories\HashtagRepository;
use App\Repositories\ReportsRepository;
use App\Repositories\UserEventRepository;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $eventRepository;
    protected $hashtagRepository;
    protected $eventHashtagRepository;

    public function __construct(EventRepository $eventRepository, HashtagRepository $hashtagRepository, EventHashtagRepository $eventHashtagRepository, ReportsRepository $reportsRepository, UserEventRepository $userEventRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->hashtagResponsitory = $hashtagRepository;
        $this->eventHashtagRepository = $eventHashtagRepository;
        $this->reportsRepository = $reportsRepository;
        $this->userEventRepository = $userEventRepository;
    }

    public function create(Request $request)
    {
        try {
            $reqs = $request->all();

            $data = array_merge($reqs, ['user_id' => auth()->user()->id]);
            $data['link_stream'] = env('APP_URL_CONSOLE', 'http://localhost') . "/stream/view/";

            // $res = $reqs['hashtag'] ? $this->hashtagResponsitory->createHashtags($reqs['hashtag']) : null;

            if (isset($request->image)) {

                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_events')
                );

                // $filename = mt_rand() . "_" . microtime(true) . "_" . $request->image->getClientOriginalName();
                // $url = $fileSevice->uploadFile($filename, $request->image);

                $imageDecodeBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
                $filename = mt_rand() . "_" . microtime(true) . '.' . 'png';
                $url = $fileSevice->uploadBase64File($filename, $imageDecodeBase64);

                $data['image'] = $fileSevice->getFilePath($url);
            } else {
                $data['image'] = '';
            }

            if ($request->capacity == null || !isset($request->capacity) || $request->capacity == 0) {
                $data['capacity'] = 0;
            }

            if (auth()->user()->role == null) {
                if (intval($request->points) < 100) {
                    return responseOK([
                        'status' => false,
                        'msg' => "message_server.register_point_limit",
                    ]);
                }
            }

            $event = $this->eventRepository->create($data);
            $data['link_stream'] = env('APP_URL_CONSOLE', 'http://localhost') . "/stream/view/" . $event->id;

            $this->eventRepository->update($event->id, $data);

            if ($event) {
                // create record
                $this->eventRepository->createEventLive($event);
                $this->eventHashtagRepository->updateHashtagEvent($reqs['hashtag'], $event->id);
            }

            return responseOK(new EventResource($event));
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $reqs = $request->all();
            $data = array_merge($reqs);
            // $res = array();
            // if (isset($reqs['hashtag'])) {
            //     $res = $reqs['hashtag'] ? $this->hashtagResponsitory->createHashtags($reqs['hashtag']) : null;
            // }
            if (isset($request->image) && $request->image !== null) {

                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_events')
                );

                if (str_contains($request->image, config('app.app_url'))) {
                    $data['image'] = $this->eventRepository->detail($request->id, $request)->image;
                } else {
                    $imageDecodeBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
                    $filename = mt_rand() . "_" . microtime(true) . '.' . 'png';
                    $url = $fileSevice->uploadBase64File($filename, $imageDecodeBase64);

                    $data['image'] = $fileSevice->getFilePath($url);

                    // delete previous avatar
                    $fileSevice->deleteFile(basename($this->eventRepository->detail($request->id, $request)->image));
                }
            }

            if ($request->capacity == null || !isset($request->capacity)) {
                $data['capacity'] = 0;
            }

            if (auth()->user()->role == null) {
                if (intval($request->points) < 100) {
                    return responseOK([
                        'status' => false,
                        'msg' => "message_server.register_point_limit",
                    ]);
                }
            }
            $event = $this->eventRepository->update($request->id, $data);

            if ($event) {
                $this->eventHashtagRepository->deleteByEventId($request->id);
                $this->eventHashtagRepository->updateHashtagEvent($reqs['hashtag'], $request->id);
            }

            return responseOK(new EventResource($this->eventRepository->detail($request->id, $request)));
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show($id, Request $request)
    {
        try {
            $detail_event = $this->eventRepository->detail($id, $request);
            if ($detail_event && isset(auth()->user()->id)) {
                $eventPermission = $this->eventRepository->checkJoinEventPermission($detail_event);
                $detail_event['join_permission'] = $eventPermission;
                $isHostOrSubhost = $this->eventRepository->checkHostOrSubhost($detail_event);
                $detail_event['is_host_subhost'] = $isHostOrSubhost;
            }

            return responseOK(new EventResource($detail_event));
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function testFunction()
    {
        $current = Carbon::now();
        $limit = $current->addMinute(15);
        $events_coming = Event::where([
            [DB::raw('CONCAT(date,\' \' ,time)'), '<=', $limit],
            [DB::raw('CONCAT(date,\' \' ,time)'), '>', $current], ['status', '==', 1]
        ])
            ->get();
        return responseOK(['events' => $events_coming]);
    }

    public function subscribe(SubscribeEventRequest $request)
    {
        try {
            $result = $this->eventRepository->subscribe($request);
            if (!$result['status']) {
                return responseValidate($result['status'], $result['msg']);
            }
            return responseOK(['message' => __('message.events.subscribe_event')]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function unSubscribe(Request $request)
    {
        try {
            $result = $this->eventRepository->unSubscribe($request);
            if (!$result) {
                return responseValidate(
                    //['errors' => [__('message.events.un_subscribe_event')]]
                    ['errors' => [__('message.events.not_yet_un_subscribe_event')]]
                );
            }
            return responseOK(['message' => __('message.events.un_subscribe_event_success')]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function getBanner(Request $request)
    {
        try {
            $banner = $this->eventRepository->getBanner($request);
            return responseOK($banner);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function detailEvent($event_id)
    {
        try {
            $event = $this->eventRepository->eventDetail($event_id);
            return responseOK($event);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function searchEventOrStreamer(Request $request)
    {
        try {
            $data = $this->eventRepository->searchEventOrStreamer($request);
            return responseOK($data);
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function checkUserAccessEvent(Request $request)
    {
        try {
            $data = $this->eventRepository->checkUserAccessEvent($request);
            return responseOK($data);
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function getListEvent(Request $request)
    {
        try {
            $data = $this->eventRepository->getListEvent($request);
            return responseOK($data);
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function createReport(Request $request)
    {
        try {
            $reqs = $request->all();
            $status = '';
            if (isset($request->events_id) && isset($request->description)) {
                $events_id = $this->eventRepository->checkEventid($request->events_id, $status);
                if ($events_id) {
                    $data = array_merge($reqs, ['user_id' => auth()->user()->id]);
                    $data['events_id'] = intval($request->events_id);

                    $check = $this->reportsRepository->checkExistedReport($request->events_id, auth()->user()->id);
                    if (!isset($check)) {
                        $report = $this->reportsRepository->create($data);
                        return responseOK([
                            'data' => new ReportsResource($report),
                            'msg' => 'message_server.report_success',
                            'status' => true,
                        ]);
                    } else {
                        return [
                            'status' => false,
                            'msg' => "message_server.is_reported",
                            'reportData' => null,
                        ];
                    }
                } else {
                    return [
                        'status' => false,
                        'msg' => "Event haven't not found",
                        'reportData' => null,
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'msg' => 'Event id  and Description is not null',
                    'reportData' => null,
                ];
            }
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function userCancelEvent(Request $request)
    {
        try {
            if (isset($request->event_id) && isset($request->cancel_reason)) {
                $user_id = auth()->user()->id;

                $user_event_issue = $this->userEventRepository->checkUserEvent($request->event_id, $user_id);

                if ($user_event_issue) {
                    $user_event = $this->userEventRepository->cancelEvent($request->event_id, $request->cancel_reason);

                    return [
                        'status' => true,
                        'msg' => 'Cancel Event success',
                        'reportData' => $user_event,
                    ];
                } else {
                    return [
                        'status' => false,
                        'msg' => 'User Event not found or doing cancel',
                        'reportData' => null,
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'msg' => 'event_id or cancel_reason not null',
                    'reportData' => null,
                ];
            }
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
