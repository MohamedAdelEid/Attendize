<?php

namespace App\Mail;

use App\Models\AbstractReviewer;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractReviewerInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $reviewer;
    public $plainPassword;
    public $portalUrl;
    public $oneTimeLoginUrl;

    public function __construct(
        Event $event,
        AbstractReviewer $reviewer,
        string $plainPassword,
        string $portalUrl,
        string $oneTimeLoginUrl
    ) {
        $this->event = $event;
        $this->reviewer = $reviewer;
        $this->plainPassword = $plainPassword;
        $this->portalUrl = $portalUrl;
        $this->oneTimeLoginUrl = $oneTimeLoginUrl;
    }

    public function build()
    {
        return $this->subject(trans('Abstract.reviewer_invitation_subject', ['event' => $this->event->title]))
            ->view('Emails.abstract-reviewer-invitation');
    }
}
