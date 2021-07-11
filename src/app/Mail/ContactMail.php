<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->data['date'] = date('Y') . '年/' . date('m') . '月/' . date('d') . '日';
        return $this->markdown('emails.contact_support')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->data['subject'])
            ->with([
                'data' => $this->data,
            ]);
    }
}
