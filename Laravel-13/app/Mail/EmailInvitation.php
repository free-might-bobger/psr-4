<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailInvitation extends Mailable
{
    use Queueable, SerializesModels;
    public $inviteInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inviteInfo)
    {
        $this->inviteInfo = $inviteInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invitation');
    }
}