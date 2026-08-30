<?php

namespace App\Http\Controllers;

use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Services\AbstractApprovalEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbstractSubmissionsController extends MyBaseController
{
    protected $approvalEmail;

    public function __construct(AbstractApprovalEmailService $approvalEmail)
    {
        parent::__construct();
        $this->approvalEmail = $approvalEmail;
    }

    public function showSubmissions(Request $request, $event_id)
    {
        return redirect()->route('showEventAbstracts', array_merge(
            ['event_id' => $event_id, 'tab' => 'submissions'],
            $request->only(['q', 'status', 'abstract_id'])
        ));
    }

    public function showSubmissionDetails(Request $request, $event_id, $submission_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $submission = AbstractSubmission::whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->with(['abstractDefinition.templates.category', 'category', 'formFieldValues.field', 'registrationUser'])
            ->findOrFail($submission_id);

        return view('ManageEvent.Modals.AbstractSubmissionDetails', [
            'event' => $event,
            'submission' => $submission,
        ]);
    }

    public function updateSubmissionStatus(Request $request, $event_id, $submission_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $submission = AbstractSubmission::whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->with(['abstractDefinition.templates.category', 'category'])->findOrFail($submission_id);

        $status = $request->get('status');
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid status.'], 422);
        }

        $previous = $submission->status;
        $submission->status = $status;
        $submission->reviewed_at = Carbon::now();
        $submission->review_notes = $request->get('review_notes');
        $submission->save();

        if ($status === 'approved' && $previous !== 'approved') {
            $this->approvalEmail->sendIfNeeded($event, $submission);
        }

        $message = $status === 'approved'
            ? trans('Abstract.submission_approved')
            : ($status === 'rejected' ? trans('Abstract.submission_rejected') : 'Status updated.');

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function bulkUpdateSubmissions(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $ids = (array) $request->get('ids', []);
        $action = $request->get('action');

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No submissions selected.'], 422);
        }

        $submissions = AbstractSubmission::whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->with('abstractDefinition.templates.category')->whereIn('id', $ids)->get();

        if ($action === 'delete') {
            foreach ($submissions as $submission) {
                if ($submission->file_path) {
                    Storage::disk('public')->delete($submission->file_path);
                }
                $submission->delete();
            }
            return response()->json(['status' => 'success', 'message' => 'Selected submissions deleted.']);
        }

        if (!in_array($action, ['approved', 'rejected', 'pending'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid action.'], 422);
        }

        foreach ($submissions as $submission) {
            $previous = $submission->status;
            $submission->status = $action;
            $submission->reviewed_at = Carbon::now();
            $submission->save();

            if ($action === 'approved' && $previous !== 'approved') {
                $this->approvalEmail->sendIfNeeded($event, $submission);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk update completed.',
        ]);
    }

    public function deleteSubmission(Request $request, $event_id, $submission_id)
    {
        Event::scope()->findOrFail($event_id);
        $submission = AbstractSubmission::whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->findOrFail($submission_id);

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }
        $submission->delete();

        return response()->json(['status' => 'success', 'message' => 'Submission deleted.']);
    }
}
