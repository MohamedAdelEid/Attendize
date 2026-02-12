<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
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
        // Load form field values with field labels for the email
        $this->registrationUser->load(['formFieldValues.field', 'profession', 'conference', 'registration.dynamicFormFields']);
    }

    public function build()
    {
        return $this
            ->subject('Registration Received – Pending Review | ' . $this->event->title)
            ->view('Emails.registration-pending');
    }
}
