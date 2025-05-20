<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationPending extends Mailable
{
    use Queueable, SerializesModels;

    public $registrationUser;
    public $event;

    public function __construct($registrationUser, $event)
    {
        $this->registrationUser = $registrationUser;
        $this->event = $event;
    }

    public function build()
    {
        return $this
            ->subject('Registration Pending Approval - ' . $this->event->name)
            ->view('Emails.registration-pending');
    }
}
