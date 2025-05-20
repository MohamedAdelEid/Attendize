<?php

namespace App\Http\Controllers;

use App\Attendize\Utils;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationPending;
use App\Models\Affiliate;
use App\Models\Country;
use App\Models\DynamicFormFieldValue;
use App\Models\Event;
use App\Models\EventAccessCodes;
use App\Models\EventStats;
use App\Models\RegistrationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Services\Captcha\Factory;
use Auth;
use Cookie;
use Mail;
use Redirect;
use Validator;

class EventViewController extends Controller
{
    protected $captchaService;

    public function __construct()
    {
        $captchaConfig = config('attendize.captcha');
        if ($captchaConfig['captcha_is_on']) {
            $this->captchaService = Factory::create($captchaConfig);
        }
    }

    /**
     * Show the homepage for an event
     *
     * @param Request $request
     * @param $event_id
     * @param string $slug
     * @param bool $preview
     * @return mixed
     */
    public function showEventHome(Request $request, $event_id, $slug = '', $preview = false)
    {
        $event = Event::with('registrations.category.conferences.professions')->findOrFail($event_id);

        if (!Utils::userOwns($event) && !$event->is_live) {
            return view('Public.ViewEvent.EventNotLivePage');
        }

        $data = [
            'event' => $event,
            'tickets' => $event->tickets()->orderBy('sort_order', 'asc')->get(),
            'is_embedded' => 0,
        ];

        /*
         * Don't record stats if we're previewing the event page from the backend or if we own the event.
         */
        if (!$preview && !Auth::check()) {
            $event_stats = new EventStats();
            $event_stats->updateViewCount($event_id);
        }

        /*
         * See if there is an affiliate referral in the URL
         */
        if ($affiliate_ref = $request->get('ref')) {
            $affiliate_ref = preg_replace('/\W|_/', '', $affiliate_ref);

            if ($affiliate_ref) {
                $affiliate = Affiliate::firstOrNew([
                    'name' => $request->get('ref'),
                    'event_id' => $event_id,
                    'account_id' => $event->account_id,
                ]);

                ++$affiliate->visits;

                $affiliate->save();

                Cookie::queue('affiliate_' . $event_id, $affiliate_ref, 60 * 24 * 60);
            }
        }

        return view('ViewEvent.show', $data);
    }

    /**
     * Show preview of event homepage / used for backend previewing
     *
     * @param $event_id
     * @return mixed
     */
    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, true);
    }

    /**
     * Sends a message to the organiser
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postContactOrganiser(Request $request, $event_id)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if (is_object($this->captchaService)) {
            if (!$this->captchaService->isHuman($request)) {
                return Redirect::back()
                    ->with(['message' => trans('Controllers.incorrect_captcha'), 'failed' => true])
                    ->withInput();
            }
        }

        $event = Event::findOrFail($event_id);

        $data = [
            'sender_name' => $request->get('name'),
            'sender_email' => $request->get('email'),
            'message_content' => clean($request->get('message')),
            'event' => $event,
        ];

        Mail::send(Lang::locale() . '.Emails.messageReceived', $data, function ($message) use ($event, $data) {
            $message
                ->to($event->organiser->email, $event->organiser->name)
                ->from(config('attendize.outgoing_email_noreply'), $data['sender_name'])
                ->replyTo($data['sender_email'], $data['sender_name'])
                ->subject(trans('Email.message_regarding_event', ['event' => $event->title]));
        });

        return response()->json([
            'status' => 'success',
            'message' => trans('Controllers.message_successfully_sent'),
        ]);
    }

    public function showCalendarIcs(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $icsContent = $event->getIcsForEvent();

        return response()->make($icsContent, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="event.ics'
        ]);
    }

    /**
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postShowHiddenTickets(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $accessCode = strtoupper($request->get('access_code'));
        if (!$accessCode) {
            return response()->json([
                'status' => 'error',
                'message' => trans('AccessCodes.valid_code_required'),
            ]);
        }

        $unlockedHiddenTickets = $event
            ->tickets()
            ->where('is_hidden', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->filter(function ($ticket) use ($accessCode) {
                // Only return the hidden tickets that match the access code
                return ($ticket->event_access_codes()->where('code', $accessCode)->get()->count() > 0);
            });

        if ($unlockedHiddenTickets->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => trans('AccessCodes.no_tickets_matched'),
            ]);
        }

        // Bump usage count
        EventAccessCodes::logUsage($event_id, $accessCode);

        return view('Public.ViewEvent.Partials.EventHiddenTicketsSelection', [
            'event' => $event,
            'tickets' => $unlockedHiddenTickets,
            'is_embedded' => 0,
        ]);
    }

    /**
     * Show the registration form for an event
     *
     * @param Request $request
     * @param $event_id
     * @param $event_slug
     * @param $registration_id
     * @return mixed
     */
    public function showEventRegistrationForm(Request $request, $event_id, $event_slug, $registration_id)
    {
        $event = Event::with('registrations.category.conferences.professions')->findOrFail($event_id);
        $registration = $event->registrations()->with('dynamicFormFields')->findOrFail($registration_id);
        $countries = Country::all();
        if (!$event->is_live) {
            return view('Public.ViewEvent.EventNotLivePage');
        }

        if ($registration->end_date < now() || $registration->status == 'inactive') {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id, 'event_slug' => $event_slug])
                ->with('error', 'Registration period has ended for this option.');
        }

        $data = [
            'event' => $event,
            'registration' => $registration,
            'countries' => $countries,
        ];

        return view('Public.ViewEvent.Partials.EventRegistrationForm', $data);
    }

    /**
     * Process the registration form submission
     *
     * @param Request $request
     * @param $event_id
     * @param $registration_id
     * @return mixed
     */
    public function postEventRegistration(Request $request, $event_id, $registration_id)
    {
        try {
            DB::beginTransaction();

            $event = Event::findOrFail($event_id);
            $registration = $event->registrations()->with('dynamicFormFields')->findOrFail($registration_id);

            // Check if registration is active and not expired
            if ($registration->end_date < now() || $registration->status == 'inactive') {
                return redirect()
                    ->route('showEventPage', ['event_id' => $event_id, 'event_slug' => $event->slug])
                    ->with('error', 'This registration option is no longer available.');
            }

            // Validate basic fields
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
            ];

            // Add conference validation if applicable
            if ($registration->category && $registration->category->conferences && $registration->category->conferences->where('status', 'active')->count() > 0) {
                $rules['conference_id'] = 'required|exists:conferences,id';
                $rules['profession_id'] = 'required|exists:professions,id';

                // Verify the conference is active
                $conferenceId = $request->input('conference_id');
                $conference = $registration->category->conferences->where('id', $conferenceId)->first();

                if (!$conference || $conference->status != 'active') {
                    return redirect()
                        ->back()
                        ->with('error', 'The selected conference is no longer available.')
                        ->withInput();
                }
            }

            // Add validation rules for dynamic form fields
            foreach ($registration->dynamicFormFields as $field) {
                $fieldRules = [];

                if ($field->is_required) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                if ($field->type == 'file') {
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:10240';  // 10MB max file size
                } elseif ($field->type == 'email') {
                    $fieldRules[] = 'email';
                } elseif ($field->type == 'date') {
                    $fieldRules[] = 'date';
                }

                $rules['fields.' . $field->id] = implode('|', $fieldRules);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Create the registration user
            $registrationUser = RegistrationUser::create([
                'category_id' => $registration->category_id,
                'conference_id' => $request->input('conference_id'),
                'profession_id' => $request->input('profession_id'),
                'registration_id' => $registration->id,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'status' => 'pending',
                'is_new' => true,
            ]);

            // Save form responses
            if ($request->has('fields')) {
                foreach ($request->input('fields') as $fieldId => $value) {
                    $field = $registration->dynamicFormFields()->find($fieldId);

                    // dd($request->all());
                    if ($field) {
                        // Handle file uploads
                        if ($field->type == 'file' && $request->hasFile('fields.' . $fieldId)) {
                            $file = $request->file('fields.' . $fieldId);
                            $path = $file->store('form-uploads', 'public');
                            $value = $path;
                        }

                        // Create form response using DynamicFormFieldValue
                        $formFieldValue = new DynamicFormFieldValue();
                        $formFieldValue->registration_user_id = $registrationUser->id;
                        $formFieldValue->dynamic_form_field_id = $fieldId;
                        $formFieldValue->value = $value;
                        $formFieldValue->save();
                    }
                }
            }

            if ($registration->approval_status === 'automatic') {
                $registrationUser->status = 'approved';
                $registrationUser->save();
                // Mail::to($registrationUser->email)->send(new RegistrationApproved($registrationUser, $event));
            } else {
                $registrationUser->status = 'pending';
                $registrationUser->save();
                // Mail::to($registrationUser->email)->send(new RegistrationPending($registrationUser, $event));
            }

            DB::commit();

            return redirect()
                ->route('showEventPage', ['event_id' => $event_id, 'event_slug' => $event->slug])
                ->with('success', 'Your registration has been submitted successfully. You will receive a confirmation email shortly.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', `There was a problem processing your registration. Please try again. | ${$e->getMessage()}`)
                ->withInput();
        }
    }
}
