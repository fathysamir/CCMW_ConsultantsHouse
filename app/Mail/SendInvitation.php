<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public $re_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitation, $re_email)
    {
        $this->code = $invitation->code;
        $this->re_email = $re_email;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.inv2')
            ->subject('Welcome to CCMW - Sign-In Instructions')
            ->with([
                'code' => $this->code,
                'email' => $this->re_email,

            ]);
    }
}
