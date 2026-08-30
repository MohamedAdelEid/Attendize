<?php

namespace App\Services;

use App\Mail\AbstractReviewerInvitation;
use App\Models\AbstractReviewer;
use App\Models\AbstractReviewerLoginToken;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AbstractReviewerInvitationService
{
    public function send(Event $event, AbstractReviewer $reviewer, string $plainPassword): void
    {
        try {
            $loginToken = AbstractReviewerLoginToken::createForReviewer($reviewer, 48);
            $loginUrl = route('abstractReviewerWelcomeLogin', [
                'event_id' => $event->id,
                'token' => $loginToken->token,
            ]);
            $portalUrl = route('showAbstractReviewLogin', ['event_id' => $event->id]);

            Mail::to($reviewer->email)->send(new AbstractReviewerInvitation(
                $event,
                $reviewer,
                $plainPassword,
                $portalUrl,
                $loginUrl
            ));
        } catch (Exception $e) {
            Log::warning('Reviewer invitation email failed: ' . $e->getMessage());
        }
    }
}
