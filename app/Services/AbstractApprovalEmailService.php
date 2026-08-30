<?php

namespace App\Services;

use App\Mail\AbstractSubmissionApproved;
use App\Models\AbstractSubmission;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AbstractApprovalEmailService
{
    public function sendIfNeeded(Event $event, AbstractSubmission $submission): void
    {
        $abstract = $submission->abstractDefinition;
        if (!$abstract || !$submission->email || !$abstract->email_subject) {
            return;
        }

        try {
            if (!$abstract->relationLoaded('templates')) {
                $abstract->load('templates.category');
            }
            $submission->loadMissing('category');

            Mail::to($submission->email)->send(new AbstractSubmissionApproved($event, $abstract, $submission));
        } catch (Exception $e) {
            Log::warning('Abstract approval email failed: ' . $e->getMessage());
        }
    }
}
