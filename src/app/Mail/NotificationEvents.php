<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationEvents extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $user;
    protected $event;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event, $user)
    {
        $this->event = $event;
        $this->user = $user;
        Log::debug("......." . $event);
        Log::debug("......." . $user);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug("=====zzzz>build" . $this->user->nick_name);
        $this->event['date'] = date('Y') . '年/' . date('m') . '月/' . date('d') . '日';

        return $this->view('emails.notifications_events')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('視聴予約して頂いた' . $this->event->title . 'イベント開始のお知らせ')
            ->with([
                'user_name' => $this->user->nick_name,
                'event_name' => $this->event->title,
                'date_start' => $this->event->date,
                'time_start' => $this->event->time,
                'link' => $this->event->link_stream,
                'contact' => env('ADMIN_EMAIL'),
            ]);
    }
}
