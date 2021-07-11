<?php

namespace App\Console\Commands;

use App\Mail\NotificationEvents;
use App\Models\Notification;
use App\Models\UserEvent;
use App\Repositories\EventRepository;
use App\Repositories\UserEventRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $eventRepository = new EventRepository();
            $userEventRepository = new UserEventRepository();
            $userRepository = new UserRepository();
            $events_of_the_day = $eventRepository->getEventOfTheDay();
            $event_id_update = [];
            Log::debug('=>events of the day ' . $events_of_the_day);

            foreach ($events_of_the_day as $event) {
                $user_id_notification = collect($event->userSubscribeEvent)->pluck('id');
                Log::debug($user_id_notification);
                foreach ($user_id_notification as $us) {
                    // Log::debug('ussss' . print_r($us, true));
                    $user = $userRepository->getById($us);
                    // Log::debug(".=====>" . print_r($event, true));
                    if ($user && $event) {
                        Mail::to($user->email)->send(new NotificationEvents($event, $user));
                    }
                }
                if (count($user_id_notification)) {
                    array_push($event_id_update, $event->id);
                }

                $user_id_notification = collect($user_id_notification)->map(function ($item, $key) {
                    return [
                        'type' => Notification::TYPE_EVENT,
                        'user_id' => $item,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                // Log::debug("sssszzzz" . print_r($user_id_notification));
                $event->userNotificationEvent()->sync($user_id_notification);
            }
            Log::debug('$eve   ' . print_r($event_id_update, true) . '    ' . 'UserEvent::IS_NOTIFICATION' . UserEvent::IS_NOTIFICATION);
            // $userEventRepository->bulkUpdate($event_id_update, ['is_notification' => UserEvent::IS_NOTIFICATION], 'event_id');
            UserEvent::whereIn('event_id', $event_id_update)->update(['is_notification' => UserEvent::IS_NOTIFICATION]);

            Log::info(__('message.notification_success'));
            DB::commit();
            return 0;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

    }
}
