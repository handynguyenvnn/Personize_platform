<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $user;
    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug("call build email");
        try {
            Log::debug(config('app.url_console 1') . config('mail.from.address') . '/verification-email/' . $this->token);
            return $this->view('emails.verification_user')
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject(__('message.verification_email'))
                ->with([
                    'url' => config('app.url_console') . '/verification-email/' . $this->token,
                ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
