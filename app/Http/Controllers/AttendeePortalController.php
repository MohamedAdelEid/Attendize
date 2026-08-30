<?php

namespace App\Http\Controllers;

use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttendeePortalController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    protected function user(Request $request)
    {
        return $request->attributes->get('attendee_user');
    }

    public function dashboard(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request);

        $submissions = AbstractSubmission::where(function ($q) use ($user) {
            $q->where('registration_user_id', $user->id)
                ->orWhere('email', $user->email);
        })->whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
            ->with(['abstractDefinition', 'category'])
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        return view('AttendeePortal.dashboard', compact('event', 'user', 'submissions'));
    }

    public function registration(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request)->load(['registration', 'category', 'formFieldValues.field']);

        return view('AttendeePortal.registration', compact('event', 'user'));
    }

    public function ticket(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request);

        if ($user->status === 'approved' && !$user->ticket_token) {
            app(TicketService::class)->processApproval($user);
            $user->refresh();
        }

        $ticketViewUrl = $user->ticket_token
            ? route('viewTicket', ['locale' => app()->getLocale(), 'token' => $user->ticket_token])
            : null;
        $ticketDownloadUrl = $user->ticket_token
            ? route('downloadTicket', ['locale' => app()->getLocale(), 'token' => $user->ticket_token])
            : null;

        return view('AttendeePortal.ticket', compact(
            'event',
            'user',
            'ticketViewUrl',
            'ticketDownloadUrl'
        ));
    }

    public function abstracts(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request);

        $submissions = AbstractSubmission::where(function ($q) use ($user) {
            $q->where('registration_user_id', $user->id)
                ->orWhere('email', $user->email);
        })->whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->with(['abstractDefinition', 'category'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        return view('AttendeePortal.abstracts', compact('event', 'user', 'submissions'));
    }

    public function showAbstractUpload(Request $request, $event_id, $submission_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request);

        $submission = $this->findUserSubmission($user, $event_id, $submission_id);

        if ($submission->status !== 'approved') {
            return redirect()->route('showAttendeePortalAbstracts', ['event_id' => $event_id])
                ->with('error', trans('AttendeePortal.abstract_not_approved'));
        }

        return view('AttendeePortal.abstract-upload', compact('event', 'user', 'submission'));
    }

    public function downloadFinalFile(Request $request, $event_id, $submission_id)
    {
        $user = $this->user($request);
        $submission = $this->findUserSubmission($user, $event_id, $submission_id);

        if ($submission->status !== 'approved' || !$submission->final_file_path) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($submission->final_file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $submission->final_file_path,
            basename($submission->final_file_path)
        );
    }

    public function postAbstractUpload(Request $request, $event_id, $submission_id)
    {
        $user = $this->user($request);
        $submission = $this->findUserSubmission($user, $event_id, $submission_id);

        if ($submission->status !== 'approved') {
            return redirect()->back()->with('error', trans('AttendeePortal.abstract_not_approved'));
        }

        $validator = Validator::make($request->all(), [
            'final_file' => 'required|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if ($submission->final_file_path) {
            Storage::disk('public')->delete($submission->final_file_path);
        }

        $submission->final_file_path = $request->file('final_file')->store('abstract-final-submissions', 'public');
        $submission->final_submitted_at = Carbon::now();
        $submission->save();

        return redirect()->route('showAttendeePortalAbstracts', ['event_id' => $event_id])
            ->with('success', trans('AttendeePortal.final_upload_success'));
    }

    public function profile(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $user = $this->user($request);

        return view('AttendeePortal.profile', compact('event', 'user'));
    }

    public function updateProfile(Request $request, $event_id)
    {
        $user = $this->user($request);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->phone = $request->get('phone');
        $user->title = $request->get('title');
        $user->bio = $request->get('bio');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('registration-avatars', 'public');
        }

        $user->save();

        return redirect()->route('showAttendeePortalProfile', ['event_id' => $event_id])
            ->with('success', trans('AttendeePortal.profile_saved'));
    }

    protected function findUserSubmission($user, $event_id, $submission_id): AbstractSubmission
    {
        return AbstractSubmission::where('id', $submission_id)
            ->where(function ($q) use ($user) {
                $q->where('registration_user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->whereHas('abstractDefinition', function ($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })
            ->with(['abstractDefinition', 'category'])
            ->firstOrFail();
    }
}
