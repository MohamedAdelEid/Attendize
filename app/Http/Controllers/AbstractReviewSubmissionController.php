<?php

namespace App\Http\Controllers;

use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Services\AbstractApprovalEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AbstractReviewSubmissionController extends Controller
{
    protected $approvalEmail;

    public function __construct(AbstractApprovalEmailService $approvalEmail)
    {
        $this->approvalEmail = $approvalEmail;
    }

    public function index(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $reviewer = Auth::guard('abstract_reviewer')->user();

        $searchQuery = $request->get('q');
        $statusFilter = $request->get('status');
        $abstractFilter = $request->get('abstract_id');

        $accessibleIds = $reviewer->accessibleAbstractIds();
        $abstractOptionsQuery = EventAbstract::where('event_id', $event_id)->orderBy('name');
        if ($accessibleIds !== null) {
            if (empty($accessibleIds)) {
                $abstractOptionsQuery->whereRaw('1 = 0');
            } else {
                $abstractOptionsQuery->whereIn('id', $accessibleIds);
            }
        }
        $abstractOptions = $abstractOptionsQuery->pluck('name', 'id');

        $subQuery = $reviewer->submissionsQuery()
            ->with(['abstractDefinition', 'registrationUser', 'reviewedBy']);

        if ($abstractFilter) {
            if ($reviewer->canAccessAbstract((int) $abstractFilter)) {
                $subQuery->where('abstract_id', $abstractFilter);
            } else {
                $subQuery->whereRaw('1 = 0');
            }
        }
        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
            $subQuery->where('status', $statusFilter);
        }
        if ($searchQuery) {
            $subQuery->where(function ($q) use ($searchQuery) {
                $q->where('full_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%')
                    ->orWhere('phone', 'like', '%' . $searchQuery . '%');
            });
        }

        $submissions = $subQuery->orderBy('submitted_at', 'desc')->paginate(25)->appends($request->query());

        return view('AbstractReview.submissions', compact(
            'event',
            'reviewer',
            'submissions',
            'abstractOptions',
            'searchQuery',
            'statusFilter',
            'abstractFilter'
        ));
    }

    protected function findScopedSubmission($event_id, $submission_id): AbstractSubmission
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();

        return $reviewer->submissionsQuery()
            ->with(['abstractDefinition.templates.category', 'category', 'formFieldValues.field', 'registrationUser', 'reviewedBy'])
            ->findOrFail($submission_id);
    }

    public function show(Request $request, $event_id, $submission_id)
    {
        $event = Event::findOrFail($event_id);
        $submission = $this->findScopedSubmission($event_id, $submission_id);
        $reviewer = Auth::guard('abstract_reviewer')->user();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'html' => view('AbstractReview.partials.submission-detail', compact('event', 'submission', 'reviewer'))->render(),
            ]);
        }

        return view('AbstractReview.partials.submission-detail', compact('event', 'submission', 'reviewer'));
    }

    public function updateStatus(Request $request, $event_id, $submission_id)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();
        if (!$reviewer->can_review) {
            return response()->json(['status' => 'error', 'message' => trans('Abstract.permission_denied')], 403);
        }

        $event = Event::findOrFail($event_id);
        $submission = $this->findScopedSubmission($event_id, $submission_id);

        $status = $request->get('status');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid status.'], 422);
        }

        $previous = $submission->status;
        $submission->status = $status;
        $submission->reviewed_at = Carbon::now();
        $submission->reviewed_by_reviewer_id = $reviewer->id;
        if ($request->has('review_notes')) {
            $submission->review_notes = $request->get('review_notes');
        }
        $submission->save();

        if ($status === 'approved' && $previous !== 'approved') {
            $this->approvalEmail->sendIfNeeded($event, $submission);
        }

        $message = $status === 'approved'
            ? trans('Abstract.submission_approved')
            : ($status === 'rejected' ? trans('Abstract.submission_rejected') : trans('Abstract.status_updated'));

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function update(Request $request, $event_id, $submission_id)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();
        if (!$reviewer->can_edit) {
            return response()->json(['status' => 'error', 'message' => trans('Abstract.permission_denied')], 403);
        }

        $submission = $this->findScopedSubmission($event_id, $submission_id);

        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'authors' => 'nullable|string|max:5000',
            'details' => 'nullable|string|max:10000',
            'domain' => 'nullable|string|max:255',
            'review_notes' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ], 422);
        }

        foreach (['full_name', 'email', 'phone', 'authors', 'details', 'domain', 'review_notes'] as $field) {
            if ($request->has($field)) {
                $submission->{$field} = $request->get($field);
            }
        }

        if ($request->hasFile('file')) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $path = $request->file('file')->store('abstract_submissions/' . $event_id, 'public');
            $submission->file_path = $path;
        }

        $submission->save();

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.submission_updated'),
        ]);
    }

    public function destroy(Request $request, $event_id, $submission_id)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();
        if (!$reviewer->can_delete) {
            return response()->json(['status' => 'error', 'message' => trans('Abstract.permission_denied')], 403);
        }

        $submission = $this->findScopedSubmission($event_id, $submission_id);

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }
        $submission->delete();

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.submission_deleted'),
        ]);
    }

    public function bulk(Request $request, $event_id)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();
        $event = Event::findOrFail($event_id);
        $ids = (array) $request->get('ids', []);
        $action = $request->get('action');

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No submissions selected.'], 422);
        }

        $submissions = $reviewer->submissionsQuery()
            ->with('abstractDefinition.templates.category')
            ->whereIn('id', $ids)
            ->get();

        if ($action === 'delete') {
            if (!$reviewer->can_delete) {
                return response()->json(['status' => 'error', 'message' => trans('Abstract.permission_denied')], 403);
            }
            foreach ($submissions as $submission) {
                if ($submission->file_path) {
                    Storage::disk('public')->delete($submission->file_path);
                }
                $submission->delete();
            }
            return response()->json(['status' => 'success', 'message' => trans('Abstract.bulk_completed')]);
        }

        if (!in_array($action, ['approved', 'rejected', 'pending'], true)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid action.'], 422);
        }

        if (!$reviewer->can_review) {
            return response()->json(['status' => 'error', 'message' => trans('Abstract.permission_denied')], 403);
        }

        foreach ($submissions as $submission) {
            $previous = $submission->status;
            $submission->status = $action;
            $submission->reviewed_at = Carbon::now();
            $submission->reviewed_by_reviewer_id = $reviewer->id;
            $submission->save();

            if ($action === 'approved' && $previous !== 'approved') {
                $this->approvalEmail->sendIfNeeded($event, $submission);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.bulk_completed'),
        ]);
    }
}
