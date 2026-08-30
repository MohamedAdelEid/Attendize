<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\RegistrationUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendeePortalLoginCode extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $code;

    public function __construct(Event $event, RegistrationUser $user, string $code)
    {
        $this->event = $event;
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject(trans('AttendeePortal.login_code_subject', ['event' => $this->event->title]))
            ->view('Emails.attendee-portal-login-code');
    }
}
