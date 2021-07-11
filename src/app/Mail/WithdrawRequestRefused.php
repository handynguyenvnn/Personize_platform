<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestRefused extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('出金依頼は拒否されました')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.withdrawRequests.refuse')
            ->with([
                'user_name' => $this->user->nick_name,
            ]);
    }
}
