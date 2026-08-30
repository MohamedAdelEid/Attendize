<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\AttendeePortalAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendeePortalAuthController extends Controller
{
    protected $auth;

    public function __construct(AttendeePortalAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function showLogin(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        if ($this->auth->currentUser($request, (int) $event_id)) {
            return redirect()->route('showAttendeePortalDashboard', ['event_id' => $event_id]);
        }

        return view('AttendeePortal.login', compact('event'));
    }

    public function requestCode(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $identifier = trim((string) $request->get('identifier'));
        $user = $this->auth->findUserForEvent((int) $event_id, $identifier);
        if (!$user) {
            return redirect()->back()
                ->withErrors(['identifier' => trans('AttendeePortal.user_not_found')])
                ->withInput();
        }

        $otp = $this->auth->sendLoginCode($user, (int) $event_id);
        if ($otp === null) {
            return redirect()->back()
                ->withErrors(['identifier' => trans('AttendeePortal.code_send_failed')])
                ->withInput();
        }

        $request->session()->put('attendee_portal_pending_identifier', $identifier);

        $successMessage = trans('AttendeePortal.code_sent');
        if (app()->environment('local') && config('app.debug')) {
            $successMessage .= ' [DEV code: ' . $otp . ']';
        }

        return redirect()->route('showAttendeePortalVerify', ['event_id' => $event_id])
            ->with('success', $successMessage);
    }

    public function showVerify(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $identifier = $request->session()->get('attendee_portal_pending_identifier')
            ?: old('identifier');

        if (!$identifier) {
            return redirect()->route('showAttendeePortalLogin', ['event_id' => $event_id]);
        }

        // Ensure the pending identifier still belongs to a real registration for this event.
        if (!$this->auth->findUserForEvent((int) $event_id, $identifier)) {
            $request->session()->forget('attendee_portal_pending_identifier');

            return redirect()->route('showAttendeePortalLogin', ['event_id' => $event_id])
                ->withErrors(['identifier' => trans('AttendeePortal.user_not_found')]);
        }

        return view('AttendeePortal.verify', compact('event', 'identifier'));
    }

    public function verifyCode(Request $request, $event_id)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string|max:255',
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = $this->auth->verifyCode(
            (int) $event_id,
            trim((string) $request->get('identifier')),
            $request->get('code')
        );

        if (!$user) {
            return redirect()->back()
                ->withErrors(['code' => trans('AttendeePortal.invalid_code')])
                ->withInput();
        }

        $request->session()->forget('attendee_portal_pending_identifier');
        $this->auth->login($request, $user, (int) $event_id, $request->has('remember'));

        return redirect()->route('showAttendeePortalDashboard', ['event_id' => $event_id])
            ->with('success', trans('AttendeePortal.welcome', ['name' => $user->full_name]));
    }

    public function logout(Request $request, $event_id)
    {
        $this->auth->logout($request);

        return redirect()->route('showAttendeePortalLogin', ['event_id' => $event_id]);
    }
}
