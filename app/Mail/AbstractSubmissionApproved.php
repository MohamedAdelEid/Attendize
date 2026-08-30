<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Services\AbstractEmailPlaceholderService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AbstractSubmissionApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $abstract;
    public $submission;
    public $emailSubject;
    public $emailBody;
    public $finalUploadUrl;

    public function __construct(Event $event, EventAbstract $abstract, AbstractSubmission $submission)
    {
        $this->event = $event;
        $this->abstract = $abstract->loadMissing('templates.category');
        $this->submission = $submission->loadMissing('category');

        $service = new AbstractEmailPlaceholderService();
        $this->emailSubject = $service->replace(
            $abstract->email_subject ?: ('Abstract Submission | ' . $event->title),
            $event,
            $abstract,
            $submission
        );
        $this->emailBody = $service->replace(
            $abstract->email_body ?: '<p>Dear {full_name},</p><p>Your abstract has been approved. Please upload your final presentation file using the button below.</p>',
            $event,
            $abstract,
            $submission
        );
        $this->finalUploadUrl = route('showAttendeePortalAbstractUpload', [
            'event_id' => $event->id,
            'submission_id' => $submission->id,
        ]);
    }

    public function build()
    {
        $mail = $this->subject($this->emailSubject)
            ->view('Emails.abstract-submission-approved')
            ->with([
                'event' => $this->event,
                'abstract' => $this->abstract,
                'submission' => $this->submission,
                'emailBody' => $this->emailBody,
                'finalUploadUrl' => $this->finalUploadUrl,
            ]);

        if ($this->abstract->email_attach_template && $this->submission->abstract_category_id) {
            $template = $this->abstract->templates
                ->firstWhere('abstract_category_id', $this->submission->abstract_category_id);

            if ($template && $template->template_path
                && Storage::disk('public')->exists($template->template_path)) {
                $name = optional($template->category)->name
                    ? optional($template->category)->name . '-' . basename($template->template_path)
                    : basename($template->template_path);
                $mail->attach(
                    Storage::disk('public')->path($template->template_path),
                    ['as' => $name]
                );
            }
        }

        return $mail;
    }
}
