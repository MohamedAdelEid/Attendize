<?php

namespace App\Http\Controllers;

use App\Attendize\Utils;
use App\Attendize\PaymentUtils;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationPending;
use App\Models\Affiliate;
use App\Models\Category;
use App\Models\Conference;
use App\Models\Country;
use App\Models\DynamicFormFieldValue;
use App\Models\Event;
use App\Models\EventAccessCodes;
use App\Models\EventMember;
use App\Models\EventMemberData;
use App\Models\EventMemberField;
use App\Models\EventMemberFieldMapping;
use App\Models\EventStats;
use App\Models\Registration;
use App\Models\RegistrationUserMemberData;
use App\Models\RegistrationPayment;
use App\Models\RegistrationUser;
use App\Models\UserType;
use App\Models\UserTypeOption;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Services\Captcha\Factory;
use Services\PaymentGateway\Factory as PaymentGatewayFactory;
use Auth;
use Cookie;
use Mail;
use Redirect;
use Validator;

class EventViewController extends Controller
{
    protected $captchaService;
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
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

        // Get the first active registration for this event
        $registration = $event->registrations()->with('dynamicFormFields')->where('status', 'active')->first();
        $countries = Country::all();

        if (!Utils::userOwns($event) && !$event->is_live) {
            return view('Public.ViewEvent.EventNotLivePage');
        }

        $data = [
            'event' => $event,
            'tickets' => $event->tickets()->orderBy('sort_order', 'asc')->get(),
            'is_embedded' => 0,
            'registration' => $registration,
            'countries' => $countries,
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
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showEventHomePreview(Request $request, $event_id)
    {
        return $this->showEventHome($request, $event_id, '', true);
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
        $event = Event::findOrFail($event_id);
        $registration = $event->registrations()->with('dynamicFormFields')->findOrFail($registration_id);

        if ($registration->category) {
            $registration->category->load([
                'conferences' => function ($query) {
                    $query->withPivot('price');
                },
                'conferences.professions'
            ]);
        }

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

            // Check if registration is active and not expired (null end_date = no expiry)
            $isExpired = $registration->end_date && \Carbon\Carbon::parse($registration->end_date)->isPast();
            if ($isExpired || $registration->status === 'inactive') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'This registration option is no longer available.'
                    ], 400);
                }
                return redirect()
                    ->route('showEventPage', ['event_id' => $event_id, 'event_slug' => $event->slug])
                    ->with('error', 'This registration option is no longer available.');
            }

            // Validate basic fields with English messages
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:registration_users,email',
                'phone' => 'nullable|string|max:20|unique:registration_users,phone',
            ];

            $messages = [
                'first_name.required' => 'First name is required.',
                'first_name.string' => 'First name must be text.',
                'first_name.max' => 'First name may not exceed 255 characters.',
                'last_name.required' => 'Last name is required.',
                'last_name.string' => 'Last name must be text.',
                'last_name.max' => 'Last name may not exceed 255 characters.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email may not exceed 255 characters.',
                'email.unique' => 'This email is already registered.',
                'phone.string' => 'Phone must be text.',
                'phone.max' => 'Phone may not exceed 20 characters.',
                'phone.unique' => 'This phone number is already registered.',
            ];

            // Extract conference_id and profession_id from dynamic fields
            $conferenceField = $registration->dynamicFormFields->where('type', 'conference')->first();
            $professionField = $registration->dynamicFormFields->where('type', 'profession')->first();
            $conferenceId = null;
            $professionId = null;

            if ($conferenceField && $request->has('fields.' . $conferenceField->id)) {
                $conferenceId = $request->input('fields.' . $conferenceField->id);
            }

            if ($professionField && $request->has('fields.' . $professionField->id)) {
                $professionId = $request->input('fields.' . $professionField->id);
            }

            // Add conference validation if applicable
            if ($registration->category && $registration->category->conferences && $registration->category->conferences->where('status', 'active')->count() > 0) {
                if ($conferenceField) {
                    $rules['fields.' . $conferenceField->id] = 'required|exists:conferences,id';
                    $messages['fields.' . $conferenceField->id . '.required'] = 'Please select a conference.';
                    $messages['fields.' . $conferenceField->id . '.exists'] = 'The selected conference is invalid.';
                }

                if ($professionField) {
                    $rules['fields.' . $professionField->id] = 'required|exists:professions,id';
                    $messages['fields.' . $professionField->id . '.required'] = 'Please select a profession.';
                    $messages['fields.' . $professionField->id . '.exists'] = 'The selected profession is invalid.';
                }

                // Verify the conference is active
                if ($conferenceId) {
                    $conference = $registration->category->conferences->where('id', $conferenceId)->first();

                    if (!$conference || $conference->status != 'active') {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'The selected conference is no longer available.'
                            ], 400);
                        }
                        return redirect()
                            ->back()
                            ->with('error', 'The selected conference is no longer available.')
                            ->withInput();
                    }
                }
            }

            // Calculate total price (for external_payment validation)
            $totalPriceForValidation = 0;
            if ($professionId && $registration->category && $registration->category->conferences) {
                foreach ($registration->category->conferences as $conf) {
                    $prof = $conf->professions->where('id', $professionId)->first();
                    if ($prof) {
                        $totalPriceForValidation = $conf->getPriceForCategory($registration->category_id);
                        break;
                    }
                }
            }

            // Add validation rules for dynamic form fields
            foreach ($registration->dynamicFormFields as $field) {
                $fieldRules = [];

                if ($field->type == 'external_payment') {
                    if ($totalPriceForValidation > 0) {
                        $fieldRules[] = 'required';
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'max:10240';
                        $messages["fields.{$field->id}.required"] = 'Please upload your bank transfer receipt.';
                        $messages["fields.{$field->id}.file"] = "The {$field->label} field must be a file.";
                        $messages["fields.{$field->id}.max"] = "File size may not exceed 10 MB.";
                    } else {
                        $fieldRules[] = 'nullable';
                    }
                } else {
                    if ($field->is_required) {
                        $fieldRules[] = 'required';
                        $messages["fields.{$field->id}.required"] = "The {$field->label} field is required.";
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    if ($field->type == 'file') {
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'max:10240';
                        $messages["fields.{$field->id}.file"] = "The {$field->label} field must be a file.";
                        $messages["fields.{$field->id}.max"] = "File size may not exceed 10 MB.";
                    } elseif ($field->type == 'email') {
                        $fieldRules[] = 'email';
                        $messages["fields.{$field->id}.email"] = "The {$field->label} field must be a valid email address.";
                    } elseif ($field->type == 'date') {
                        $fieldRules[] = 'date';
                        $messages["fields.{$field->id}.date"] = "The {$field->label} field must be a valid date.";
                    }
                }

                $rules['fields.' . $field->id] = implode('|', $fieldRules);
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Please correct the errors in the form.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }


            // Calculate total price if conference is selected
            $totalPrice = 0;
            if ($conferenceId && $registration->category) {
                $conference = $registration->category->conferences->where('id', $conferenceId)->first();
                if ($conference) {
                    $totalPrice = $conference->getPriceForCategory($registration->category_id);
                }
            }

            // Check if external payment field exists - if yes, skip online payment and register directly
            $hasExternalPaymentField = $registration->dynamicFormFields->where('type', 'external_payment')->isNotEmpty();
            
            // If payment is required and NO external payment field, store data in session and redirect to payment
            if ($totalPrice > 0 && !$hasExternalPaymentField) {
                // Store registration data in session
                $registrationData = [
                    'event_id' => $event_id,
                    'registration_id' => $registration_id,
                    'category_id' => $registration->category_id,
                    'conference_id' => $conferenceId,
                    'profession_id' => $professionId,
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'fields' => $request->input('fields', []),
                    'total_price' => $totalPrice,
                    'order_started' => time(),
                    'expires' => time() + (30 * 60), // 30 minutes
                    'approval_status' => $registration->approval_status,
                ];

                // Store file paths if any
                if ($request->hasFile('fields')) {
                    foreach ($request->file('fields') as $fieldId => $file) {
                        $field = $registration->dynamicFormFields()->find($fieldId);
                        if ($field && $field->type == 'file') {
                            $path = $file->store('form-uploads-temp', 'public');
                            $registrationData['files'][$fieldId] = $path;
                        }
                    }
                }

                session()->put('registration_order_' . $event_id, $registrationData);
                DB::rollBack(); // Don't commit yet, wait for payment

                // Create HyperPay checkout session for COPYandPAY widget
                $checkoutId = null;
                $integrity = null;
                $widgetUrl = null;
                $testMode = config('attendize.enable_test_payments');
                $errorDetails = [];

                $event = Event::findOrFail($event_id);
                $activeAccountPaymentGateway = $event->account->getGateway($event->account->payment_gateway_id);

                if (empty($activeAccountPaymentGateway)) {
                    $errorDetails[] = 'No payment gateway is enabled for this account.';
                } else {
                    $paymentGateway = $activeAccountPaymentGateway->payment_gateway;

                    if ($paymentGateway->name != 'HyperPay') {
                        $errorDetails[] = 'The enabled gateway is not HyperPay: ' . $paymentGateway->name;
                    } else {
                        // Check config
                        $config = $activeAccountPaymentGateway->config ?? [];
                        if (empty($config['accessToken'])) {
                            $errorDetails[] = 'Access Token is missing in settings.';
                        }
                        if (empty($config['entityId'])) {
                            $errorDetails[] = 'Entity ID is missing in settings.';
                        }

                        if (!empty($config['accessToken']) && !empty($config['entityId'])) {
                            try {
                                $configTestMode = $config['testMode'] ?? false;
                                $finalTestMode = $testMode || $configTestMode;

                                $payment_gateway_config = $config + [
                                    'testMode' => $finalTestMode
                                ];

                                \Log::info('HyperPay Config', [
                                    'env_test_mode' => $testMode,
                                    'config_test_mode' => $configTestMode,
                                    'final_test_mode' => $finalTestMode
                                ]);

                                $payment_gateway_factory = new PaymentGatewayFactory();
                                $gateway = $payment_gateway_factory->create($paymentGateway->name, $payment_gateway_config);

                                $order_total = $totalPrice;
                                $order_email = $request->input('email');

                                $returnUrl = route('checkRegistrationPaymentStatus', [
                                    'event_id' => $event_id,
                                ]);

                                $response = $gateway->startTransaction($order_total, $order_email, $event, $returnUrl);
                                $responseData = $response->getData();

                                \Log::info('HyperPay Response Data: ', $responseData);

                                if ($response->isSuccessful() || $response->isRedirect()) {
                                    $transactionData = $gateway->getTransactionData();

                                    if (isset($responseData['id'])) {
                                        $checkoutId = $responseData['id'];
                                        $integrity = $response->getIntegrity();

                                        // Get the actual base URL used by HyperPay gateway
                                        $gatewayBaseUrl = $gateway->getBaseUrl();
                                        $isTestEnvironment = strpos($gatewayBaseUrl, 'test') !== false || strpos($gatewayBaseUrl, 'eu-test') !== false;

                                        $widgetUrl = $isTestEnvironment
                                            ? 'https://eu-test.oppwa.com/v1/paymentWidgets.js'
                                            : 'https://eu-prod.oppwa.com/v1/paymentWidgets.js';

                                        \Log::info('HyperPay Checkout Created', [
                                            'checkout_id' => $checkoutId,
                                            'widget_url' => $widgetUrl,
                                            'gateway_base_url' => $gatewayBaseUrl,
                                            'is_test_environment' => $isTestEnvironment,
                                            'final_test_mode' => $finalTestMode
                                        ]);

                                        session()->put('hyperpay_checkout_id_' . $event_id, $checkoutId);
                                        session()->put('registration_order_' . $event_id . '.transaction_data', $transactionData);
                                    } else {
                                        $errorDetails[] = 'Checkout ID was not created. Response: ' . json_encode($responseData);
                                    }
                                } else {
                                    $resultCode = $responseData['result']['code'] ?? 'UNKNOWN';
                                    $resultDesc = $responseData['result']['description'] ?? 'No description';
                                    $errorDetails[] = "Failed to create payment session. Code: {$resultCode}, Description: {$resultDesc}";
                                    \Log::error('HyperPay API Error: ', $responseData);
                                }
                            } catch (\Exception $e) {
                                $errorDetails[] = 'Exception: ' . $e->getMessage();
                                $errorDetails[] = 'File: ' . $e->getFile() . ':' . $e->getLine();
                                \Log::error('HyperPay checkout creation error: ' . $e->getMessage(), [
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                        }
                    }
                }

                if (!$checkoutId) {
                    $errorMessage = 'Failed to create payment session. Please check that the payment gateway is set up correctly.';
                    if (!empty($errorDetails)) {
                        $errorMessage .= ' Details: ' . implode(' | ', $errorDetails);
                    }

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $errorMessage,
                            'debug' => config('app.debug') ? $errorDetails : null
                        ], 500);
                    }
                    return redirect()
                        ->back()
                        ->with('error', $errorMessage)
                        ->withInput();
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Registration details saved.',
                        'requires_payment' => true,
                        'total_price' => $totalPrice,
                        'checkout_id' => $checkoutId,
                        'integrity' => $integrity,
                        'widget_url' => $widgetUrl
                    ]);
                }

                return redirect()->route('showRegistrationPayment', ['event_id' => $event_id]);
            }

            // No payment required, proceed with registration (reuses transaction started at top of try)
            // Create the registration user (default user type: Delegate)
            $registrationUser = RegistrationUser::create([
                'category_id' => $registration->category_id,
                'conference_id' => $conferenceId,
                'profession_id' => $professionId,
                'registration_id' => $registration->id,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'status' => 'pending',
                'is_new' => true,
            ]);
            $defaultTypeId = $this->getDefaultUserTypeIdForEvent($event->id);
            if ($defaultTypeId) {
                $registrationUser->userTypes()->sync([$defaultTypeId]);
            }

            // Save form responses (skip conference and profession as they're already saved)
            if ($request->has('fields')) {
                foreach ($request->input('fields') as $fieldId => $value) {
                    $field = $registration->dynamicFormFields()->find($fieldId);

                    if ($field && $field->type != 'conference' && $field->type != 'profession') {
                        // Handle file uploads (file type and external_payment receipt)
                        if (($field->type == 'file' || $field->type == 'external_payment') && $request->hasFile('fields.' . $fieldId)) {
                            $file = $request->file('fields.' . $fieldId);
                            $path = $file->store('form-uploads', 'public');
                            $value = $path;
                        }
                        // Skip saving empty value for external_payment (no receipt uploaded)
                        if ($field->type == 'external_payment' && (empty($value) || !$request->hasFile('fields.' . $fieldId))) {
                            continue;
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

            // File inputs are not in $request->input('fields') – save file-only fields to dynamic_form_field_values
            if ($request->hasFile('fields')) {
                foreach ($request->file('fields') as $fieldId => $file) {
                    $field = $registration->dynamicFormFields()->find($fieldId);
                    if (!$field || ($field->type !== 'file' && $field->type !== 'external_payment')) {
                        continue;
                    }
                    // Avoid duplicate: only save if we didn't already save this field in the input('fields') loop
                    $exists = DynamicFormFieldValue::where('registration_user_id', $registrationUser->id)
                        ->where('dynamic_form_field_id', $fieldId)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    $path = $file->store('form-uploads', 'public');
                    $formFieldValue = new DynamicFormFieldValue();
                    $formFieldValue->registration_user_id = $registrationUser->id;
                    $formFieldValue->dynamic_form_field_id = (int) $fieldId;
                    $formFieldValue->value = $path;
                    $formFieldValue->save();
                }
            }

            // Always set to pending for both Non-Member and Member (manual approval)
            $registrationUser->status = 'pending';
            $registrationUser->save();

            try {
                $recipient = $registrationUser->email;
                \Log::info('Registration pending email: attempting to send', [
                    'to' => $recipient,
                    'event_id' => $event->id,
                    'registration_user_id' => $registrationUser->id,
                    'mail_driver' => config('mail.driver'),
                    'mail_host' => config('mail.host'),
                    'mail_port' => config('mail.port'),
                    'mail_username_set' => !empty(config('mail.username')),
                ]);
                Mail::to($recipient)->send(new RegistrationPending($registrationUser, $event));
                \Log::info('Registration pending email: sent successfully', ['to' => $recipient]);
            } catch (\Exception $mailEx) {
                $msg = $mailEx->getMessage();
                $isAuth = (stripos($msg, 'auth') !== false || stripos($msg, 'login') !== false || stripos($msg, 'credential') !== false || stripos($msg, 'password') !== false || stripos($msg, 'authentication') !== false);
                \Log::error('Registration pending email: FAILED', [
                    'to' => $registrationUser->email ?? 'unknown',
                    'error' => $msg,
                    'possible_credentials_issue' => $isAuth,
                    'exception_class' => get_class($mailEx),
                    'file' => $mailEx->getFile(),
                    'line' => $mailEx->getLine(),
                    'trace' => $mailEx->getTraceAsString(),
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                $payload = [
                    'status' => 'success',
                    'message' => 'Registration successful',
                    'requires_payment' => false,
                ];
                if (!$registration->is_members_form) {
                    $payload['redirect_url'] = route('showRegistrationConfirmation', ['event_id' => $event_id]);
                }
                return response()->json($payload);
            }

            return redirect()->route('showRegistrationConfirmation', ['event_id' => $event_id]);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while processing your registration. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'An error occurred while processing your registration. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the registration payment page
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showRegistrationPayment(Request $request, $event_id)
    {
        $registrationOrder = session()->get('registration_order_' . $event_id);

        if (!$registrationOrder || $registrationOrder['expires'] < time()) {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id])
                ->with('error', 'Your registration session has expired. Please try again.');
        }

        $event = Event::findOrFail($event_id);
        $activeAccountPaymentGateway = $event->account->getGateway($event->account->payment_gateway_id);

        if (empty($activeAccountPaymentGateway)) {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id])
                ->with('error', 'No payment gateway is available.');
        }

        $paymentGateway = $activeAccountPaymentGateway->payment_gateway;

        // Create payment checkout session if HyperPay
        $checkoutId = null;
        if ($paymentGateway->name == 'HyperPay') {
            try {
                $payment_gateway_config = $activeAccountPaymentGateway->config + [
                    'testMode' => config('attendize.enable_test_payments')
                ];

                $payment_gateway_factory = new PaymentGatewayFactory();
                $gateway = $payment_gateway_factory->create($paymentGateway->name, $payment_gateway_config);

                $order_total = $registrationOrder['total_price'];
                $order_email = $registrationOrder['email'];

                // Use registration payment return URL
                $returnUrl = route('showRegistrationPaymentReturn', [
                    'event_id' => $event_id,
                ]);

                $response = $gateway->startTransaction($order_total, $order_email, $event, $returnUrl);

                if ($response->isSuccessful() || $response->isRedirect()) {
                    $transactionData = $gateway->getTransactionData();
                    if (isset($transactionData['id'])) {
                        $checkoutId = $transactionData['id'];
                        session()->put('hyperpay_checkout_id_' . $event_id, $checkoutId);
                        session()->put('registration_order_' . $event_id . '.transaction_data', $transactionData);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('HyperPay checkout creation error: ' . $e->getMessage());
            }
        }

        $viewData = [
            'event' => $event,
            'registration_order' => $registrationOrder,
            'order_total' => $registrationOrder['total_price'],
            'account_payment_gateway' => $activeAccountPaymentGateway,
            'payment_gateway' => $paymentGateway,
            'payment_failed' => $request->get('is_payment_failed') ? 1 : 0,
            'checkout_id' => $checkoutId,
        ];

        return view('Public.ViewEvent.RegistrationPayment', $viewData);
    }

    /**
     * Handle payment return for registration
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showRegistrationPaymentReturn(Request $request, $event_id)
    {
        $registrationOrder = session()->get('registration_order_' . $event_id);

        if (!$registrationOrder) {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id])
                ->with('error', 'No registration data is available.');
        }

        $event = Event::findOrFail($event_id);
        $activeAccountPaymentGateway = $event->account->getGateway($event->account->payment_gateway_id);

        if (empty($activeAccountPaymentGateway)) {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id])
                ->with('error', 'No payment gateway is available.');
        }

        $payment_gateway_config = $activeAccountPaymentGateway->config + [
            'testMode' => config('attendize.enable_test_payments')
        ];

        $payment_gateway_factory = new PaymentGatewayFactory();
        $gateway = $payment_gateway_factory->create($activeAccountPaymentGateway->payment_gateway->name, $payment_gateway_config);
        $gateway->extractRequestParameters($request);

        // For HyperPay, we need to get transaction data from session or request
        $transactionData = [];
        if ($request->has('id')) {
            $transactionData['id'] = $request->get('id');
        }
        if ($request->has('resourcePath')) {
            $transactionData['resourcePath'] = $request->get('resourcePath');
        }

        // Try to get from session if not in request
        if (empty($transactionData) && isset($registrationOrder['transaction_data'])) {
            $transactionData = $registrationOrder['transaction_data'];
        }

        $response = $gateway->completeTransaction($transactionData);

        if ($response->isSuccessful()) {
            // Payment successful, complete registration
            return $this->completeRegistration($request, $event_id);
        } else {
            // Payment failed
            return redirect()
                ->route('showRegistrationPayment', ['event_id' => $event_id, 'is_payment_failed' => 1])
                ->with('error', $response->getMessage() ?? 'Payment failed. Please try again.');
        }
    }

    /**
     * Complete registration after payment success
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function completeRegistration(Request $request, $event_id)
    {
        $registrationOrder = session()->get('registration_order_' . $event_id);

        if (!$registrationOrder) {
            return redirect()
                ->route('showEventPage', ['event_id' => $event_id])
                ->with('error', 'No registration data is available.');
        }

        try {
            DB::beginTransaction();

            $event = Event::findOrFail($event_id);
            $registration = $event->registrations()->findOrFail($registrationOrder['registration_id']);

            // Create the registration user (default user type: Delegate)
            $registrationUser = RegistrationUser::create([
                'category_id' => $registrationOrder['category_id'],
                'conference_id' => $registrationOrder['conference_id'],
                'profession_id' => $registrationOrder['profession_id'],
                'registration_id' => $registrationOrder['registration_id'],
                'first_name' => $registrationOrder['first_name'],
                'last_name' => $registrationOrder['last_name'],
                'email' => $registrationOrder['email'],
                'phone' => $registrationOrder['phone'],
                'status' => 'pending',
                'is_new' => true,
            ]);
            $defaultTypeId = $this->getDefaultUserTypeIdForEvent($event_id);
            if ($defaultTypeId) {
                $registrationUser->userTypes()->sync([$defaultTypeId]);
            }

            // Save form responses
            if (isset($registrationOrder['fields'])) {
                foreach ($registrationOrder['fields'] as $fieldId => $value) {
                    $field = $registration->dynamicFormFields()->find($fieldId);

                    if ($field && $field->type != 'conference' && $field->type != 'profession') {
                        // Handle file paths from temp storage
                        if ($field->type == 'file' && isset($registrationOrder['files'][$fieldId])) {
                            $tempPath = $registrationOrder['files'][$fieldId];
                            $newPath = str_replace('form-uploads-temp', 'form-uploads', $tempPath);
                            // Move file if needed (or just use the path)
                            $value = $newPath;
                        }

                        $formFieldValue = new DynamicFormFieldValue();
                        $formFieldValue->registration_user_id = $registrationUser->id;
                        $formFieldValue->dynamic_form_field_id = $fieldId;
                        $formFieldValue->value = $value;
                        $formFieldValue->save();
                    }
                }
            }

            if ($registrationOrder['approval_status'] === 'automatic') {
                $registrationUser->status = 'approved';
                $registrationUser->save();
                $this->ticketService->processApproval($registrationUser);
            } else {
                $registrationUser->status = 'pending';
                $registrationUser->save();
            }

            DB::commit();

            // Clear session
            session()->forget('registration_order_' . $event_id);
            session()->forget('hyperpay_checkout_id_' . $event_id);

            return redirect()
                ->route('showRegistrationConfirmation', ['event_id' => $event_id])
                ->with('success', 'Registration and payment completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('showRegistrationPayment', ['event_id' => $event_id, 'is_payment_failed' => 1])
                ->with('error', 'An error occurred while saving your registration. Please try again.');
        }
    }

    /**
     * Show the registration confirmation page
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showRegistrationConfirmation(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        return view('ViewEvent.partials.registration-confirmation', [
            'event' => $event
        ]);
    }

    /**
     * Check payment status after HyperPay widget submission (AJAX callback)
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRegistrationPaymentStatus(Request $request, $event_id)
    {
        $registrationOrder = session()->get('registration_order_' . $event_id);

        if (!$registrationOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'No registration data is available.'
            ], 400);
        }

        $event = Event::findOrFail($event_id);
        $activeAccountPaymentGateway = $event->account->getGateway($event->account->payment_gateway_id);

        if (empty($activeAccountPaymentGateway)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No payment gateway is available.'
            ], 400);
        }

        $paymentGateway = $activeAccountPaymentGateway->payment_gateway;

        if ($paymentGateway->name != 'HyperPay') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway is not supported.'
            ], 400);
        }

        try {
            $payment_gateway_config = $activeAccountPaymentGateway->config + [
                'testMode' => config('attendize.enable_test_payments')
            ];

            $payment_gateway_factory = new PaymentGatewayFactory();
            $gateway = $payment_gateway_factory->create($paymentGateway->name, $payment_gateway_config);

            // Extract resourcePath from request (HyperPay widget sends it as a parameter)
            $resourcePath = $request->get('resourcePath');
            $checkoutId = $request->get('id');

            if (!$resourcePath && !$checkoutId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payment parameters.'
                ], 400);
            }

            // Set options for completeTransaction
            $gateway->extractRequestParameters($request);

            // Complete the transaction
            $response = $gateway->completeTransaction([
                'resourcePath' => $resourcePath,
                'id' => $checkoutId
            ]);

            if ($response->isSuccessful()) {
                // Payment successful, proceed to complete registration
                try {
                    DB::beginTransaction();

                    $registration = Registration::findOrFail($registrationOrder['registration_id']);
                    $conferenceId = $registrationOrder['conference_id'] ?? null;
                    $professionId = $registrationOrder['profession_id'] ?? null;

                    // Create the registration user (default user type: Delegate)
                    $registrationUser = RegistrationUser::create([
                        'category_id' => $registrationOrder['category_id'],
                        'conference_id' => $conferenceId,
                        'profession_id' => $professionId,
                        'registration_id' => $registration->id,
                        'first_name' => $registrationOrder['first_name'],
                        'last_name' => $registrationOrder['last_name'],
                        'email' => $registrationOrder['email'],
                        'phone' => $registrationOrder['phone'],
                        'status' => 'pending',
                        'is_new' => true,
                    ]);
                    $defaultTypeId = $this->getDefaultUserTypeIdForEvent($event->id);
                    if ($defaultTypeId) {
                        $registrationUser->userTypes()->sync([$defaultTypeId]);
                    }

                    // Save form responses (including files from temporary storage)
                    if (!empty($registrationOrder['fields'])) {
                        foreach ($registrationOrder['fields'] as $fieldId => $value) {
                            $field = $registration->dynamicFormFields()->find($fieldId);

                            if ($field && $field->type != 'conference' && $field->type != 'profession') {
                                // Handle file uploads from temporary storage
                                if ($field->type == 'file' && isset($registrationOrder['files'][$fieldId])) {
                                    $tempPath = $registrationOrder['files'][$fieldId];
                                    $finalPath = str_replace('form-uploads-temp', 'form-uploads', $tempPath);

                                    // Move file from temp to final location
                                    if (Storage::disk('public')->exists($tempPath)) {
                                        Storage::disk('public')->move($tempPath, $finalPath);
                                        $value = $finalPath;
                                    }
                                }

                                $formFieldValue = new DynamicFormFieldValue();
                                $formFieldValue->registration_user_id = $registrationUser->id;
                                $formFieldValue->dynamic_form_field_id = $fieldId;
                                $formFieldValue->value = $value;
                                $formFieldValue->save();
                            }
                        }
                    }

                    $responseData = $response->getData();

                    RegistrationPayment::create([
                        'registration_user_id' => $registrationUser->id,
                        'payment_gateway' => 'HyperPay',
                        'transaction_id' => $response->getTransactionReference(),
                        'checkout_id' => $checkoutId,
                        'amount' => $registrationOrder['total_price'],
                        'currency' => $event->currency->code ?? 'SAR',
                        'status' => 'completed',
                        'payment_method' => $responseData['paymentBrand'] ?? null,
                        'payment_response' => $responseData,
                        'resource_path' => $resourcePath,
                    ]);

                    if ($registration->approval_status === 'automatic') {
                        $registrationUser->status = 'approved';
                        $registrationUser->save();
                        $this->ticketService->processApproval($registrationUser);
                    } else {
                        $registrationUser->status = 'pending';
                        $registrationUser->save();
                    }

                    DB::commit();

                    session()->forget('registration_order_' . $event_id);
                    session()->forget('hyperpay_checkout_id_' . $event_id);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment and registration completed successfully!',
                        'redirect_url' => route('showRegistrationConfirmation', ['event_id' => $event_id])
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Registration completion error: ' . $e->getMessage());

                    return response()->json([
                        'status' => 'error',
                        'message' => 'An error occurred while completing your registration.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response->getMessage() ?? 'Payment failed. Please try again.'
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('HyperPay payment status check error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking payment status.'
            ], 500);
        }
    }

    /**
     * Show symposium landing page on root URL (/). Uses event_id from config or defaults to 2.
     */
    public function showSymposiumRoot(Request $request)
    {
        $event_id = config('attendize.default_symposium_event_id', 2);
        return $this->showSymposium($request, $event_id);
    }

    /**
     * Serve favicon for symposium/show page: SGSS logo (local image).
     */
    public function symposiumFavicon()
    {
        $logoPath = public_path('images/sgss-logo.png');
        $size = 64;

        try {
            if (file_exists($logoPath)) {
                $logo = Image::make($logoPath);
                $logo->resize($size - 8, $size - 8);
                $canvas = Image::canvas($size, $size, '#ffffff');
                $canvas->insert($logo, 'center');
                return $canvas->response('png')->header('Cache-Control', 'public, max-age=86400');
            }
        } catch (\Exception $e) {
            \Log::warning('Symposium favicon generation failed: ' . $e->getMessage());
        }
        $canvas = Image::canvas($size, $size, '#ffffff');
        return $canvas->response('png')->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Show speakers page on root URL (/speakers). Uses event_id from config or defaults to 2.
     */
    public function showSpeakersRoot(Request $request)
    {
        $event_id = config('attendize.default_symposium_event_id', 2);
        return $this->showSpeakers($request, $event_id);
    }

    /**
     * Show symposium landing page (navy/gold style) with landing + members registration forms.
     */
    public function showSymposium(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $landingRegistration = Registration::where('event_id', $event_id)->where('show_on_landing', true)->with(['dynamicFormFields', 'category.conferences.professions'])->first();
        $membersRegistration = Registration::where('event_id', $event_id)->where('is_members_form', true)->with(['dynamicFormFields', 'category.conferences.professions'])->first();
        $event->load('eventMemberFields');
        $displaySearchFields = $event->eventMemberFields->where('is_unique', true)->values();
        $countries = Country::all();
        $landingUserTypes = \App\Models\UserType::where('event_id', $event_id)->where('show_on_landing', true)->with('options')->orderBy('name')->get();

        return view('ViewEvent.show-symposium', compact('event', 'landingRegistration', 'membersRegistration', 'displaySearchFields', 'countries', 'landingUserTypes'));
    }

    /**
     * Show speakers page with same theme as symposium.
     */
    public function showSpeakers(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        return view('ViewEvent.show-speakers', compact('event'));
    }

    /**
     * Show user type page: big title + grid of users (same theme as landing).
     * URL: /e/{event_id}/type/{user_type_slug} or .../type/{user_type_slug}/{option_slug}
     */
    public function showEventUserType(Request $request, $event_id, $user_type_slug, $option_slug = null)
    {
        $event = Event::findOrFail($event_id);
        $userType = UserType::where('event_id', $event_id)->where('slug', $user_type_slug)->with('options')->firstOrFail();
        $option = null;
        if ($option_slug !== null && $option_slug !== '') {
            $option = UserTypeOption::where('user_type_id', $userType->id)->where('slug', $option_slug)->firstOrFail();
        }

        $pageTitle = $option ? $option->name : $userType->name;
        $usersQuery = RegistrationUser::whereHas('registration', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->whereHas('userTypes', function ($q) use ($userType, $option) {
            $q->where('user_types.id', $userType->id);
            if ($option !== null) {
                $q->where('registration_user_user_type.user_type_option_id', $option->id);
            }
        })->with(['userTypes' => function ($q) use ($userType) {
            $q->where('user_types.id', $userType->id);
        }]);

        $users = $usersQuery->get()->sortBy(function ($u) use ($userType) {
            $pivot = $u->userTypes->first();
            $pos = $pivot && isset($pivot->pivot->position) && $pivot->pivot->position !== null
                ? (int) $pivot->pivot->position
                : 999999;
            return [$pos, $u->first_name, $u->last_name];
        })->values();
        $landingUserTypes = UserType::where('event_id', $event_id)->where('show_on_landing', true)->with('options')->orderBy('name')->get();

        return view('ViewEvent.user-type', compact('event', 'userType', 'option', 'pageTitle', 'users', 'landingUserTypes'));
    }

    /**
     * Show program page (Coming soon).
     */
    public function showEventProgram(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $landingUserTypes = UserType::where('event_id', $event_id)->where('show_on_landing', true)->with('options')->orderBy('name')->get();

        return view('ViewEvent.program', compact('event', 'landingUserTypes'));
    }

    /**
     * API: Look up member by value. Searches in all "display & search" (is_unique) fields — value can match any of them (OR).
     */
    public function memberLookup(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $value = trim((string) $request->input('value', ''));
        if ($value === '') {
            return response()->json(['status' => 'error', 'message' => 'Value is required.'], 422);
        }

        $displaySearchFields = EventMemberField::where('event_id', $event_id)->where('is_unique', true)->get();
        if ($displaySearchFields->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No display & search fields configured.'], 422);
        }

        $memberDataRow = null;
        foreach ($displaySearchFields as $field) {
            $memberDataRow = EventMemberData::where('field_key', $field->field_key)->where('value', $value)
                ->whereHas('eventMember', function ($q) use ($event_id) {
                    $q->where('event_id', $event_id);
                })->with('eventMember.data')->first();
            if ($memberDataRow && $memberDataRow->eventMember) {
                break;
            }
        }
        if (!$memberDataRow || !$memberDataRow->eventMember) {
            return response()->json(['status' => 'not_found', 'message' => 'Member not found.']);
        }

        $member = $memberDataRow->eventMember;
        $dataByKey = $member->getDataByKey();
        $fullName = $dataByKey->get('full_name') ?? trim(($dataByKey->get('first_name') ?? '') . ' ' . ($dataByKey->get('last_name') ?? ''));
        $parts = explode(' ', $fullName, 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';
        $email = $dataByKey->get('email') ?? '';

        $fields = [];
        $expirationValue = null;
        foreach ($event->eventMemberFields as $f) {
            $val = $dataByKey->get($f->field_key) ?? ($f->field_key === 'full_name' ? $fullName : '');
            $fields[] = [
                'field_key' => $f->field_key,
                'label' => $f->label,
                'value' => $val,
            ];
            if (stripos($f->field_key, 'expir') !== false && $val) {
                $expirationValue = trim((string) $val);
            }
        }

        $expired = false;
        if ($expirationValue !== null) {
            $expirationDate = $this->parseExpirationDate($expirationValue);
            if ($expirationDate && $expirationDate->isPast()) {
                $expired = true;
            }
        }

        $renewalLink = 'https://forms.gle/MSUgd1H8Hw2wnvu68';
        $memberPayload = [
            'event_member_id' => $member->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'fields' => $fields,
        ];

        $mappedFormData = null;
        $membersRegistration = Registration::where('event_id', $event_id)->where('is_members_form', true)->with('dynamicFormFields')->first();
        if ($membersRegistration && !$expired) {
            $mappings = EventMemberFieldMapping::where('event_id', $event_id)->where('registration_id', $membersRegistration->id)->get();
            $mappedFirstName = '';
            $mappedLastName = '';
            $mappedEmail = $email;
            $mappedPhone = '';
            $mappedFields = [];
            foreach ($mappings as $m) {
                $val = $dataByKey->get($m->member_field_key);
                if ($val === null || $val === '')
                    continue;
                $val = trim((string) $val);
                if ($m->target_type === EventMemberFieldMapping::TARGET_FIRST_NAME) {
                    $mappedFirstName = $val !== '' ? trim(explode(' ', $val, 2)[0] ?? $val) : '';
                } elseif ($m->target_type === EventMemberFieldMapping::TARGET_LAST_NAME) {
                    $mappedLastName = $val !== '' ? trim(explode(' ', $val, 2)[1] ?? '') : '';
                } elseif ($m->target_type === EventMemberFieldMapping::TARGET_EMAIL) {
                    $mappedEmail = $val;
                } elseif ($m->target_type === EventMemberFieldMapping::TARGET_PHONE) {
                    $mappedPhone = $val;
                } elseif ($m->target_type === EventMemberFieldMapping::TARGET_DYNAMIC_FIELD && $m->target_dynamic_form_field_id) {
                    $mappedFields[(string) $m->target_dynamic_form_field_id] = $val;
                }
            }
            if ($mappedFirstName === '' && $mappedLastName === '' && $fullName !== '') {
                $parts = explode(' ', $fullName, 2);
                $mappedFirstName = $parts[0] ?? '';
                $mappedLastName = $parts[1] ?? '';
            }
            $memberPayload['mapped_form_data'] = [
                'first_name' => $mappedFirstName,
                'last_name' => $mappedLastName,
                'email' => $mappedEmail,
                'phone' => $mappedPhone,
                'fields' => $mappedFields,
            ];
        }

        if ($expired) {
            return response()->json([
                'status' => 'expired',
                'message' => 'Your membership has expired. You cannot register from here. Please renew or contact us.',
                'renewal_link' => $renewalLink,
                'member' => $memberPayload,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'member' => $memberPayload,
        ]);
    }

    /**
     * Register as member: create RegistrationUser from EventMember using field mappings.
     * POST: event_id, registration_id (members form), event_member_id.
     */
    public function registerAsMember(Request $request, $event_id)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'event_member_id' => 'required|exists:event_members,id',
        ]);

        $event = Event::findOrFail($event_id);
        $registration = Registration::where('event_id', $event_id)->where('id', $request->registration_id)->where('is_members_form', true)->with('dynamicFormFields')->firstOrFail();
        $member = EventMember::where('event_id', $event_id)->where('id', $request->event_member_id)->with('data')->firstOrFail();
        $dataByKey = $member->getDataByKey();

        $mappings = EventMemberFieldMapping::where('event_id', $event_id)->where('registration_id', $registration->id)->get();
        $firstName = '';
        $lastName = '';
        $email = '';
        $phone = '';
        $fields = [];

        foreach ($mappings as $m) {
            $val = $dataByKey->get($m->member_field_key);
            if ($val === null || $val === '') {
                continue;
            }
            $val = trim((string) $val);
            if ($m->target_type === EventMemberFieldMapping::TARGET_FIRST_NAME) {
                // When mapping e.g. full_name to First Name: use first word only
                $firstName = $val !== '' ? trim(explode(' ', $val, 2)[0] ?? $val) : '';
            } elseif ($m->target_type === EventMemberFieldMapping::TARGET_LAST_NAME) {
                // When mapping e.g. full_name to Last Name: use rest of string (after first space)
                $lastName = $val !== '' ? trim(explode(' ', $val, 2)[1] ?? '') : '';
            } elseif ($m->target_type === EventMemberFieldMapping::TARGET_EMAIL) {
                $email = $val;
            } elseif ($m->target_type === EventMemberFieldMapping::TARGET_PHONE) {
                $phone = $val;
            } elseif ($m->target_type === EventMemberFieldMapping::TARGET_DYNAMIC_FIELD && $m->target_dynamic_form_field_id) {
                $fields[$m->target_dynamic_form_field_id] = $val;
            }
        }

        if ($email === '') {
            $email = 'member-' . $member->id . '@import.local';
        }
        $fullName = $dataByKey->get('full_name') ?: trim($firstName . ' ' . $lastName);
        if ($firstName === '' && $lastName === '' && $fullName !== '') {
            $parts = explode(' ', $fullName, 2);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
        }

        if (RegistrationUser::where('registration_id', $registration->id)->where('email', $email)->exists()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'This email is already registered for this event.'], 422);
            }
            return redirect()->back()->with('error', 'This email is already registered for this event.');
        }

        try {
            DB::beginTransaction();
            $registrationUser = RegistrationUser::create([
                'category_id' => $registration->category_id,
                'conference_id' => null,
                'profession_id' => null,
                'registration_id' => $registration->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone ?: null,
                'status' => 'pending',
                'is_new' => false,
            ]);
            $defaultTypeId = $this->getDefaultUserTypeIdForEvent($registration->event_id);
            if ($defaultTypeId) {
                $registrationUser->userTypes()->sync([$defaultTypeId]);
            }

            foreach ($fields as $fieldId => $value) {
                $field = $registration->dynamicFormFields->find($fieldId);
                if ($field && $field->type !== 'conference' && $field->type !== 'profession') {
                    $formFieldValue = new DynamicFormFieldValue();
                    $formFieldValue->registration_user_id = $registrationUser->id;
                    $formFieldValue->dynamic_form_field_id = $fieldId;
                    $formFieldValue->value = $value;
                    $formFieldValue->save();
                }
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration successful',
                ]);
            }
            return redirect()->back()->with('success', 'Registration successful');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Parse expiration date from member data. Supports:
     * - "Jan-30" = beginning of January 2030 (1 Jan 2030; month-year, 2-digit year)
     * - "3/31/2030" or "31/3/2030" = specific date (m/d/Y or d/m/Y)
     *
     * @param string $value
     * @return \Carbon\Carbon|null
     */
    protected function parseExpirationDate($value)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        try {
            if (preg_match('/^([A-Za-z]{3})-(\d{2})$/', $value, $m)) {
                $month = $m[1];
                $year = (int) $m[2];
                $year = $year >= 0 && $year <= 99 ? 2000 + $year : $year;
                $d = Carbon::createFromFormat('M Y', $month . ' ' . $year);
                return $d->startOfMonth()->startOfDay();
            }
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
                $a = (int) $m[1];
                $b = (int) $m[2];
                $y = (int) $m[3];
                $d = Carbon::createFromDate($y, $a, $b);
                if ($d->isValid()) {
                    return $d->startOfDay();
                }
                $d = Carbon::createFromDate($y, $b, $a);
                return $d->isValid() ? $d->startOfDay() : null;
            }
            $d = Carbon::parse($value);
            return $d->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get or create the default user type "Delegate" for an event. Used for all new registrations when no user type is selected.
     *
     * @param int $event_id
     * @return int|null User type id, or null if creation failed
     */
    protected function getDefaultUserTypeIdForEvent($event_id)
    {
        $userType = UserType::firstOrCreate(
            ['event_id' => $event_id, 'name' => 'Delegate'],
            ['event_id' => $event_id, 'name' => 'Delegate']
        );
        return $userType ? $userType->id : null;
    }
}
