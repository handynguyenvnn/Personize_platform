<?php

namespace App\Repositories;

use App\Helper\Constant;
use App\Models\Event;
use App\Models\EventLive;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserEvent;
use App\Models\Configuration;
use App\Services\EventPaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventRepository extends BaseRepository
{
    public function getModel()
    {
        return Event::class;
    }

    protected $perPage = 3;
    protected $perPageBanner = 5;
    protected $perPageDetail = 3;

    function list($category, $request) {
        return $this->model
            ->withTrashed()
            ->where('category_id', $category->id)
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->where('status', '<>', Event::STATUS_CANCEL)
            ->when($category->is_admin, function ($query) {
                $query->whereHas('userCreate', function ($query) {
                    $query->whereIn('role', [User::USER_ROLE_ADMIN, User::USER_ROLE_MANAGER]);
                    // $query->whereRole(User::USER_ROLE_ADMIN);
                });
            })
        // ->when(!$category->is_admin, function ($query) use ($category) {
        //     $query->whereCategoryId($category->id)
        //         ->whereHas('userCreate', function ($query) {
        //             $query->whereNull('role');
        //         });
        // })
        // ->orderByDesc('date')
        // ->orderByDesc('time')
            ->where('deleted_at')
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    public function detail($id, $request, $withTrashed = false)
    {
        $query = $withTrashed ? $this->model->withTrashed() : $this->model;

        $event_detail = $query->with(['userCreate'])
            ->when(auth()->user(), function ($query) {
                $query->with(
                    [
                        'userCreate',
                        'userCreate.followMe' => function ($query) {
                            $query->when(auth()->user(), function ($query) {
                                $query->whereUserId(auth()->user()->id);
                            });
                        },
                        // won't load subscribers on the admin page in that way
                        // 'userSubscribeEvent' => function ($query) {
                        //     $query->when(auth()->user(), function ($query) {
                        //         $query->where('users.id', auth()->user()->id);
                        //     });
                        // }
                    ]
                );
            })
            ->withCount('userSubscribeEvent')
            ->with('category')
            ->withCount('userLiveEvent')
            ->findOrFail($id);
        $event_detail->other_event = $query
            ->where('id', '!=', $id)->whereUserId($event_detail->user_id)
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->withCount('userLiveEvent')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->whereNull('deleted_at')
            ->paginate($request->limit ? $request->limit : $this->perPageDetail);
        return $event_detail;
    }

    public function eventDetail($id, $withTrashed = false)
    {
        $query = $withTrashed ? $this->model->withTrashed() : $this->model;

        $event_detail = $query->with(['userCreate'])
            ->when(auth()->user(), function ($query) {
                $query->with(
                    [
                        'userCreate',
                        'userCreate.followMe' => function ($query) {
                            $query->when(auth()->user(), function ($query) {
                                $query->whereUserId(auth()->user()->id);
                            });
                        },
                    ]
                );
            })
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->findOrFail($id);
        return $event_detail;
    }

    public function subscribe($request)
    {
        $event_check = $this->model->withCount('userSubscribeEvent')->withCount('userLiveEvent')->findOrFail($request->event_id);
        if ($event_check->status !== event::STATUS_COMING && $event_check->category_id !== Event::OFFICAL_EVENT) {
            return [
                'status' => false,
                'msg' => 'message_server.events.out_of_register',
            ];
        }
        if ($event_check->userCreate->id == auth()->user()->id) {
            return [
                'status' => false,
                'msg' => 'message_server.events.event_created_by_me',
            ];
        }
        if ($event_check->capacity !== 0 && ($event_check->user_subscribe_event_count >= $event_check->capacity)) {
            return [
                'status' => false,
                'msg' => 'message_server.events.capacity_exceeded',
            ];
        }
        Log::debug("event log" . $event_check->points);
        if ($event_check->points > 0) {
            $current_user = auth()->user();
            if ($current_user->balance < $event_check->points) {
                return [
                    'status' => false,
                    'msg' => 'message_server.events.point_not_enough',
                ];
            }
            Log::debug("=====> " . $request);
            $event_payment_service = new EventPaymentService();
            Log::debug("test");
            $event_payment_service->makePaymentEvent($request);
            auth()->user()->subscribeEvent()->attach($request->event_id);
            return ['status' => true];
        }

        auth()->user()->subscribeEvent()->attach($request->event_id);
        return ['status' => true];
    }
    public function unSubscribe($request)
    {
        DB::beginTransaction();
        $event_check = $this->model->with(['userIdSubscribeEvent' => function ($query) {

        }])
            ->findOrFail($request->event_id);
        if ($event_check->userIdSubscribeEvent) {
            if ($event_check->userIdSubscribeEvent->is_notification) {
                $event_check->notificationEvent()->whereUserId(auth()->user()->id)->update([
                    'deleted_at' => now(),
                ]);
            }
            $event_check->userIdSubscribeEvent()->update([
                'deleted_at' => now(),
            ]);
            DB::commit();
            return true;
        }
        return false;
    }

    public function eventUserSuggestion($is_coming, $request)
    {
        $time_now = Carbon::parse(now())->format(Constant::FORMAT_TIME);
        $date_now = Carbon::parse(now())->format(Constant::FORMAT_DATE);
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return $this->model
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->whereNull('deleted_at')
            ->whereHas('userSubscribeEvent', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->when($is_coming, function ($query) use ($time_now, $date_now, $userId) {
                $query->whereNotIn('status', [Event::STATUS_CANCEL, Event::STATUS_FINNISH])->where(function ($query) use ($time_now, $date_now) {
                    $query->whereDate('date', '>', $date_now)
                        ->orWhere(function ($query) use ($time_now, $date_now) {
                            $query->whereDate('date', '=', $date_now)->whereTime('time', '>', $time_now);
                        });
                })->orWhere('user_id', $userId);
            })
            ->when(!$is_coming, function ($query) use ($time_now, $date_now) {
                $query->whereNotIn('status', [Event::STATUS_COMING, Event::STATUS_CANCEL, Event::STATUS_FINNISH])->where(function ($query) use ($time_now, $date_now) {
                    $query->whereDate('date', '<', $date_now)
                        ->orWhere(function ($query) use ($time_now, $date_now) {
                            $query->whereDate('date', '=', $date_now)->whereTime('time', '<', $time_now);
                        });
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('status')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    public function getEventOfUserSuggestion($request)
    {
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return $this->model
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }
    public function eventUserBanking($request)
    {
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return UserBank::
            where('user_id', $userId)->get();
    }

    public function getConfiguration()
    {
        return Configuration::all();
    }

    public function getEventOfTheDay($all_user = true, $request = null)
    {
        $time_start = Carbon::now()->format(Constant::FORMAT_TIME);
        $time_end = Carbon::now()->addMinutes(Config::get('const.minutes_check_notification'))
            ->format(Constant::FORMAT_TIME);
        Log::debug('timessss' . $time_start . '    ' . $time_end . '        ' . Carbon::now()->format(Constant::FORMAT_DATE));
        // $testquery = $this->model
        //     ->whereTime('time', '>=', $time_start)
        //     ->whereTime('time', '<=', $time_end)
        //     ->whereDate('date', '=', Carbon::now()->format(Constant::FORMAT_DATE))
        //     ->get();
        // Log::debug($testquery);
        $query = $this->model
            ->whereTime('time', '>=', $time_start)
            ->whereTime('time', '<=', $time_end)
            ->whereDate('date', '=', Carbon::now()->format(Constant::FORMAT_DATE));
        if ($all_user) {
            return $query
                ->with('userSubscribeEvent')
                ->whereHas('userSubscribeEvent', function ($query) {
                    $query->where('user_event.is_notification', null);
                })
                ->get();
        } else {
            return $query
                ->whereHas('userNotificationEvent', function ($query) {
                    $query->where('users.id', auth()->user()->id);
                })
                ->with('userCreate')
                ->paginate($request->limit ? $request->limit : $this->perPage);
        }

    }

    public function getBanner($request)
    {
        return $this->model->orderByDesc('updated_at')->take($request->take ? $request->take : $this->perPageBanner)->get();
    }

    public function searchEventOrStreamer($request)
    {
        $query = $this->model;

        if (isset($request->type)) {
            if ($request->type === 'streamer') {
                $streamerData = User::query()
                    ->where('users.name', 'like', '%' . $request->q . '%')
                    ->orWhere('users.nick_name', 'like', '%' . $request->q . '%')
                    ->paginate($request->limit ? $request->limit : $this->perPage);
                return ['data' => $streamerData];
            } else {
                $eventData = $query->with('userCreate')
                    ->withCount('userSubscribeEvent')
                    ->withCount('userLiveEvent')
                    ->where('title', 'like', '%' . $request->q . '%')
                    ->paginate($request->limit ? $request->limit : $this->perPage);
                return ['data' => $eventData];

            }
        } else {

            $eventData = $query->with('userCreate')
                ->withCount('userSubscribeEvent')
                ->withCount('userLiveEvent')
                ->where('title', 'like', '%' . $request->q . '%')
                ->paginate($request->limit ? $request->limit : $this->perPage);

            $streamerData = User::query()
                ->where('users.name', 'like', '%' . $request->q . '%')
                ->orWhere('users.nick_name', 'like', '%' . $request->q . '%')
                ->paginate($request->limit ? $request->limit : $this->perPage);

            return [
                'eventData' => $eventData,
                'streamerData' => $streamerData,
            ];
        }

    }

    public function checkUserAccessEvent($request)
    {
        $query = $this->model;

        $eventAndEvent = $query->where('id', $request->id)
            ->first();

        if (isset($eventAndEvent)) {

            if (!auth()->user()) {
                $eventAndEvent['userChecked'] = UserEvent::where('user_id', 2)
                    ->where('event_id', $request->id)
                    ->first();
            }

            return [
                'status' => 0,
                'msg' => 'Success',
                'eventAndEvent' => $eventAndEvent,
            ];

        } else {
            return [
                'status' => 0,
                'msg' => 'Event not found',
                'eventAndEvent' => null,
            ];
        }
    }

    public function getListEvent($request)
    {
        $query = $this->model;
        if (isset($request->type)) {
            $eventData = $query->with('userCreate')
                ->whereIn('type', [0, 1])
                ->orderByDesc('date')
                ->orderByDesc('date')->get();

            return [
                'eventData' => $eventData,
            ];
        } else {
            return [
                'status' => 0,
                'msg' => 'Type event not found',
                'eventData' => null,
            ];
        }
    }

    public function checkEventid($request, $status)
    {
        if (!empty($status)) {
            return $this->model->where('id', $request)->where('status', $status)->first();
        } else {
            return $this->model->where('id', $request)->first();
        }

    }

    // for client
    public function checkJoinEventPermission($event)
    {

        $permission = false;
        if ($event) {
            $userEvent = UserEvent::where('event_id', $event->id)
                ->where('user_id', auth()->user()->id)
                ->whereNull('deleted_at')
                ->first();

            if ($userEvent || $event->user_id == auth()->user()->id) {
                $permission = true;
            }
        }
        return $permission;
    }

    // for client
    public function checkHostOrSubhost($event)
    {

        $permission = false;
        if ($event) {
            $userEvent = EventLive::where('events_id', $event->id)
                ->where('users_id', auth()->user()->id)
                ->first();

            if ($userEvent || $event->user_id == auth()->user()->id) {
                $permission = true;
            }
        }
        return $permission;
    }

    public function createEventLive($event)
    {
        EventLive::create([
            'events_id' => $event->id,
            'users_id' => $event->user_id,
            'type' => 1,
            'sk_id' => null,
        ]);
    }

    // for admin
    public function getEvents($request)
    {
        return $this->model
            ->withTrashed()
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->where('status', '<>', Event::STATUS_CANCEL)
            ->orderByDesc('created_at')
            ->whereNull('deleted_at')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    // for admin
    public function getEventsByUserId($id, $request)
    {
        return $this->model
            ->with('userCreate')
            ->with('hashtag')
            ->withCount('userSubscribeEvent')
            ->withCount('userLiveEvent')
            ->where('user_id', $id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->paginate($request->limit ? $request->limit : $this->perPage);
    }

    // for admin
    public function deleteEvent($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    // for admin
    public function getDeletedEventById($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

}
