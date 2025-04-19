<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $sender_name;
    public $account_name;
   

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitation,$sender,$account)
    {
        $this->code = $invitation->code;
        $this->sender_name = $sender;
        $this->account_name = $account;
      
    }


  /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invitation')
                    ->subject('CCMW - Invitation')
                    ->with([
                        'code' => $this->code,
                        'sender_name' => $this->sender_name,
                        'account_name' => $this->account_name,
                       
                    ]);
    }
}
