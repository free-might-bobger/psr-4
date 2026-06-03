<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StoreUserInvitation extends Mailable
{
    use Queueable, SerializesModels;
    public array $storeUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $storeUser)
    {
        $this->storeUser = $storeUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.store-user-invitation');
    }
}