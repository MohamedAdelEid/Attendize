<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\RegistrationUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $event;
    public $emailSubject;
    public $emailBody;

    /**
     * Create a new message instance.
     *
     * @param RegistrationUser $user
     * @param Event $event
     * @param string $subject
     * @param string $body
     */
    public function __construct(RegistrationUser $user, Event $event, $subject, $body)
    {
        $this->user = $user;
        $this->event = $event;
        $this->emailSubject = $subject;
        $this->emailBody = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Replace placeholders in subject and body
        $subject = $this->replacePlaceholders($this->emailSubject);
        $body = $this->replacePlaceholders($this->emailBody);

        return $this->subject($subject)
                    ->view('Emails.custom-user-email')
                    ->with([
                        'user' => $this->user,
                        'event' => $this->event,
                        'emailBody' => $body,
                    ]);
    }

    /**
     * Replace placeholders in text.
     *
     * @param string $text
     * @return string
     */
    private function replacePlaceholders($text)
    {
        $placeholders = [
            '{name}' => $this->user->first_name . ' ' . $this->user->last_name,
            '{email}' => $this->user->email,
            '{event_name}' => $this->event->title,
            '{registration_code}' => $this->user->unique_code ?? 'Not assigned',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
}
