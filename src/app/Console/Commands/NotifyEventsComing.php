<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Repositories\UserEventRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotifyEventsComing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:coming';

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
        //find events coming
        // $current = Carbon::now();
        // $limit = Carbon::now()->addMinute(15);
        $userEventRepository = new UserEventRepository();

        $current = '2021-06-29 09:45:00';
        $limit = '2021-06-29 10:00:00';
        Log::debug('time ' . $current . ' ' . $limit);
        $events_coming = Event::where([[DB::raw('CONCAT(date,\' \' ,time)'), '<=', $limit],
            [DB::raw('CONCAT(date,\' \' ,time)'), '>', $current], ['status', '=', 1], ['isNotification', '=', 0]])
            ->get();

        Log::debug('events' . $events_coming);
        DB::enableQueryLog(); // Enable query log

        // Your Eloquent query executed by using get()

        dd(DB::getQueryLog()); // Show results of log
        return 0;
    }
}
