<?php

namespace App\Http\Controllers;

use App\Models\AbstractDynamicFormFieldValue;
use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Models\RegistrationUser;
use App\Services\AbstractApprovalEmailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventAbstractViewController extends Controller
{
    protected $approvalEmail;

    public function __construct(AbstractApprovalEmailService $approvalEmail)
    {
        $this->approvalEmail = $approvalEmail;
    }

    public function showAbstractForm(Request $request, $event_id, $event_slug, $slug)
    {
        $event = Event::findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)
            ->where('slug', $slug)
            ->with(['templates.category', 'dynamicFormFields' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        if ($abstract->status !== 'published') {
            return view('Public.ViewEvent.AbstractFormClosed', [
                'event' => $event,
                'abstract' => $abstract,
                'message' => trans('Abstract.abstract_not_published'),
            ]);
        }

        if (!$abstract->isOpen()) {
            return view('Public.ViewEvent.AbstractFormClosed', [
                'event' => $event,
                'abstract' => $abstract,
                'message' => trans('Abstract.abstract_closed'),
            ]);
        }

        return view('Public.ViewEvent.AbstractForm', [
            'event' => $event,
            'abstract' => $abstract,
            'isRegisteredOnly' => $abstract->isRegisteredOnly(),
        ]);
    }

    public function verifyRegistration(Request $request, $event_id, $slug)
    {
        $event = Event::findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)->where('slug', $slug)->firstOrFail();

        if ($abstract->status !== 'published' || !$abstract->isOpen() || !$abstract->isRegisteredOnly()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('Abstract.abstract_closed'),
            ], 422);
        }

        $identifier = trim((string) $request->input('identifier', ''));
        if ($identifier === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or registration code is required.',
            ], 422);
        }

        $user = $this->findEligibleRegistrationUser($abstract, $event_id, $identifier);

        if (!$user) {
            return response()->json([
                'status' => 'not_found',
                'message' => trans('Abstract.not_found'),
            ]);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'status' => 'pending',
                'message' => trans('Abstract.registration_pending'),
            ]);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'status' => 'rejected',
                'message' => trans('Abstract.registration_rejected'),
            ]);
        }

        if ($user->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => trans('Abstract.registration_pending'),
            ]);
        }

        if ($this->hasReachedMaxSubmissions($abstract, $user->email, $user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => trans('Abstract.max_reached'),
            ], 422);
        }

        $sessionKey = 'abstract_verified_' . $abstract->id;
        session([
            $sessionKey => [
                'registration_user_id' => $user->id,
                'verified_at' => now()->timestamp,
            ],
        ]);

        return response()->json([
            'status' => 'approved',
            'message' => trans('Abstract.registration_approved'),
            'user' => [
                'id' => $user->id,
                'full_name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'registration_user_id' => $user->id,
        ]);
    }

    public function postAbstractSubmission(Request $request, $event_id, $slug)
    {
        $event = Event::findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)
            ->where('slug', $slug)
            ->with(['templates.category', 'dynamicFormFields'])
            ->firstOrFail();

        if ($abstract->status !== 'published' || !$abstract->isOpen()) {
            return $this->submissionErrorResponse($request, trans('Abstract.abstract_closed'), 403);
        }

        try {
            if ($abstract->isRegisteredOnly()) {
                return $this->handleRegisteredSubmission($request, $event, $abstract);
            }

            return $this->handleOpenSubmission($request, $event, $abstract);
        } catch (Exception $e) {
            return $this->submissionErrorResponse($request, 'Something went wrong. Please try again.', 500, $e->getMessage());
        }
    }

    protected function handleOpenSubmission(Request $request, Event $event, EventAbstract $abstract)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'authors' => 'nullable|string|max:5000',
            'details' => 'nullable|string|max:10000',
            'domain' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
            'abstract_category_id' => $this->categoryValidationRules($abstract, $event->id),
        ];

        foreach ($abstract->dynamicFormFields as $field) {
            if (!$field->is_active) {
                continue;
            }
            $key = 'fields.' . $field->id;
            $base = $field->is_required ? 'required' : 'nullable';
            if ($field->type === 'file') {
                $rules[$key] = $base . '|file|max:10240';
            } elseif ($field->type === 'email') {
                $rules[$key] = $base . '|email';
            } elseif ($field->type === 'checkbox') {
                $rules[$key] = $base . '|array';
            } else {
                $rules[$key] = $base . '|string';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($this->hasReachedMaxSubmissions($abstract, $request->get('email'))) {
            return $this->submissionErrorResponse($request, trans('Abstract.max_reached'), 422);
        }

        DB::beginTransaction();
        try {
            $filePath = $request->file('file')->store('abstract-submissions', 'public');

            $status = $abstract->approval_mode === 'automatic' ? 'approved' : 'pending';

            $submission = AbstractSubmission::create([
                'abstract_id' => $abstract->id,
                'abstract_category_id' => $request->get('abstract_category_id'),
                'full_name' => $request->get('full_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'authors' => $request->get('authors'),
                'details' => $request->get('details'),
                'domain' => $request->get('domain'),
                'file_path' => $filePath,
                'status' => $status,
                'submitted_at' => Carbon::now(),
                'reviewed_at' => $status === 'approved' ? Carbon::now() : null,
            ]);

            $this->saveDynamicFieldValues($submission, $abstract, $request);

            DB::commit();

            if ($status === 'approved') {
                $submission->load('category');
                $this->approvalEmail->sendIfNeeded($event, $submission);
            }

            $message = $status === 'approved'
                ? trans('Abstract.submission_success')
                : trans('Abstract.submission_pending_review');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $message]);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function handleRegisteredSubmission(Request $request, Event $event, EventAbstract $abstract)
    {
        $sessionKey = 'abstract_verified_' . $abstract->id;
        $verified = session($sessionKey);

        if (!$verified || empty($verified['registration_user_id'])) {
            return $this->submissionErrorResponse($request, 'Verification required. Please verify your registration first.', 403);
        }

        // Session expires after 60 minutes
        if (!empty($verified['verified_at']) && (now()->timestamp - (int) $verified['verified_at']) > 3600) {
            session()->forget($sessionKey);
            return $this->submissionErrorResponse($request, 'Verification expired. Please verify again.', 403);
        }

        $registrationUserId = (int) $verified['registration_user_id'];
        $user = RegistrationUser::with('registration')->find($registrationUserId);
        if (!$user || $user->status !== 'approved') {
            return $this->submissionErrorResponse($request, trans('Abstract.not_found'), 403);
        }

        if (!$this->isUserAllowedForAbstract($abstract, $user)) {
            return $this->submissionErrorResponse($request, trans('Abstract.not_found'), 403);
        }

        $validator = Validator::make($request->all(), [
            'abstract_category_id' => $this->categoryValidationRules($abstract, $event->id),
            'file' => 'required|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($this->hasReachedMaxSubmissions($abstract, $user->email, $user->id)) {
            return $this->submissionErrorResponse($request, trans('Abstract.max_reached'), 422);
        }

        DB::beginTransaction();
        try {
            $filePath = $request->file('file')->store('abstract-submissions', 'public');
            $status = $abstract->approval_mode === 'automatic' ? 'approved' : 'pending';

            $submission = AbstractSubmission::create([
                'abstract_id' => $abstract->id,
                'abstract_category_id' => $request->get('abstract_category_id'),
                'registration_user_id' => $user->id,
                'full_name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'phone' => $user->phone,
                'authors' => null,
                'details' => null,
                'domain' => null,
                'file_path' => $filePath,
                'status' => $status,
                'submitted_at' => Carbon::now(),
                'reviewed_at' => $status === 'approved' ? Carbon::now() : null,
            ]);

            DB::commit();
            session()->forget($sessionKey);

            if ($status === 'approved') {
                $submission->load('category');
                $this->approvalEmail->sendIfNeeded($event, $submission);
            }

            $message = $status === 'approved'
                ? trans('Abstract.submission_success')
                : trans('Abstract.submission_pending_review');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $message]);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function findEligibleRegistrationUser(EventAbstract $abstract, $event_id, string $identifier)
    {
        $query = RegistrationUser::where(function ($q) use ($identifier) {
            $q->where('email', $identifier)
                ->orWhere('unique_code', $identifier);
        })->whereHas('registration', function ($q) use ($event_id, $abstract) {
            $q->where('event_id', $event_id);
            if (!$abstract->all_event_registrations) {
                $ids = $abstract->registrations()->pluck('registrations.id');
                $q->whereIn('id', $ids);
            }
        });

        return $query->orderByRaw("CASE status WHEN 'approved' THEN 1 WHEN 'pending' THEN 2 WHEN 'rejected' THEN 3 ELSE 4 END")->first();
    }

    protected function isUserAllowedForAbstract(EventAbstract $abstract, RegistrationUser $user): bool
    {
        if (!$user->registration || $user->registration->event_id != $abstract->event_id) {
            return false;
        }

        if ($abstract->all_event_registrations) {
            return true;
        }

        return $abstract->registrations()->where('registrations.id', $user->registration_id)->exists();
    }

    protected function hasReachedMaxSubmissions(EventAbstract $abstract, ?string $email, ?int $registrationUserId = null): bool
    {
        if (!$abstract->max_submissions_per_user) {
            return false;
        }

        $query = AbstractSubmission::where('abstract_id', $abstract->id);

        if ($registrationUserId) {
            $query->where(function ($q) use ($registrationUserId, $email) {
                $q->where('registration_user_id', $registrationUserId);
                if ($email) {
                    $q->orWhere('email', $email);
                }
            });
        } else {
            $query->where('email', $email);
        }

        return $query->count() >= $abstract->max_submissions_per_user;
    }

    protected function saveDynamicFieldValues(AbstractSubmission $submission, EventAbstract $abstract, Request $request)
    {
        $fields = $request->input('fields', []);
        foreach ($abstract->dynamicFormFields as $field) {
            if (!$field->is_active) {
                continue;
            }

            $value = $fields[$field->id] ?? null;

            if ($field->type === 'file' && $request->hasFile('fields.' . $field->id)) {
                $value = $request->file('fields.' . $field->id)->store('abstract-field-uploads', 'public');
            } elseif (is_array($value)) {
                $value = implode(', ', $value);
            }

            AbstractDynamicFormFieldValue::create([
                'abstract_submission_id' => $submission->id,
                'abstract_dynamic_form_field_id' => $field->id,
                'value' => $value,
            ]);
        }
    }

    protected function categoryValidationRules(EventAbstract $abstract, int $eventId): array
    {
        $allowed = $abstract->allowedCategoryIds();
        if (empty($allowed)) {
            return ['nullable', 'integer'];
        }

        return [
            'required',
            'integer',
            Rule::exists('abstract_categories', 'id')->where('event_id', $eventId)->where('is_active', true),
            Rule::in($allowed),
        ];
    }

    protected function submissionErrorResponse(Request $request, string $message, int $code = 422, $error = null)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'error' => $error,
            ], $code);
        }

        return back()->with('error', $message)->withInput();
    }
}
