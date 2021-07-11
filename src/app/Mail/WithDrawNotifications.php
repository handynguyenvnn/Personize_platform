<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithDrawNotifications extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $user_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('アドミンさんへ'))
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.withdraw_notification')
            ->with([
                'user_name' => $this->user_name,
            ]);
    }
}
