<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\RegistrationUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $event;
    public $downloadUrl;

    /**
     * Create a new message instance.
     *
     * @param RegistrationUser $user
     * @param Event $event
     * @return void
     */
    public function __construct(RegistrationUser $user, Event $event)
    {
        $this->user = $user;
        $this->event = $event;
        $this->downloadUrl = route('downloadTicket', ['token' => $user->ticket_token]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Registration Approved - ' . $this->event->title)
                    ->view('emails.registration-approved');
    }
}
