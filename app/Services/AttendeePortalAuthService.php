<?php

namespace App\Services;

use App\Mail\AttendeePortalLoginCode;
use App\Models\Event;
use App\Models\RegistrationUser;
use App\Models\RegistrationUserLoginCode;
use App\Models\RegistrationUserRememberToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AttendeePortalAuthService
{
    public const SESSION_USER_KEY = 'attendee_portal_user_id';
    public const SESSION_EVENT_KEY = 'attendee_portal_event_id';
    public const REMEMBER_COOKIE = 'attendee_portal_remember';

    public function findUserForEvent(int $eventId, string $identifier): ?RegistrationUser
    {
        return RegistrationUser::findForEvent($eventId, $identifier);
    }

    /**
     * Send OTP only for an existing registration user belonging to this event.
     * Returns the OTP string on success, or null if sending failed.
     */
    public function sendLoginCode(RegistrationUser $user, int $eventId): ?string
    {
        $user->loadMissing('registration');

        if (!$user->email || !$user->registration || (int) $user->registration->event_id !== $eventId) {
            return null;
        }

        $code = RegistrationUserLoginCode::createForUser($user, $eventId);

        try {
            $event = Event::findOrFail($eventId);
            Mail::to($user->email)->send(new AttendeePortalLoginCode($event, $user, $code->code));
            return $code->code;
        } catch (Exception $e) {
            Log::warning('Attendee portal OTP email failed: ' . $e->getMessage());

            // Local/dev: keep the code so login can still be tested without SMTP.
            if (app()->environment('local')) {
                Log::info('Attendee portal OTP (local fallback) for ' . $user->email . ': ' . $code->code);
                return $code->code;
            }

            $code->delete();
            return null;
        }
    }

    public function verifyCode(int $eventId, string $identifier, string $code): ?RegistrationUser
    {
        $code = preg_replace('/\D/', '', (string) $code);
        if (strlen($code) !== 6) {
            return null;
        }

        $user = $this->findUserForEvent($eventId, $identifier);
        if (!$user) {
            return null;
        }

        $record = RegistrationUserLoginCode::where('registration_user_id', $user->id)
            ->where('event_id', $eventId)
            ->where('code', $code)
            ->whereNull('used_at')
            ->orderBy('id', 'desc')
            ->first();

        if (!$record || !$record->isValid()) {
            return null;
        }

        $record->used_at = Carbon::now();
        $record->save();

        return $user;
    }

    public function login(Request $request, RegistrationUser $user, int $eventId, bool $remember = false): void
    {
        $request->session()->put(self::SESSION_USER_KEY, $user->id);
        $request->session()->put(self::SESSION_EVENT_KEY, $eventId);
        $request->session()->regenerate();

        if ($remember) {
            $token = RegistrationUserRememberToken::createForUser($user, $eventId);
            Cookie::queue(cookie(
                self::REMEMBER_COOKIE,
                $token->token,
                60 * 24 * 30,
                '/',
                null,
                false,
                true,
                false,
                'lax'
            ));
        }
    }

    public function tryRememberLogin(Request $request, int $eventId): ?RegistrationUser
    {
        $tokenValue = $request->cookie(self::REMEMBER_COOKIE);
        if (!$tokenValue) {
            return null;
        }

        $token = RegistrationUserRememberToken::where('token', $tokenValue)
            ->where('event_id', $eventId)
            ->first();

        if (!$token || !$token->isValid()) {
            return null;
        }

        $user = RegistrationUser::find($token->registration_user_id);
        if (!$user) {
            return null;
        }

        $token->last_used_at = Carbon::now();
        $token->save();

        $request->session()->put(self::SESSION_USER_KEY, $user->id);
        $request->session()->put(self::SESSION_EVENT_KEY, $eventId);

        return $user;
    }

    public function currentUser(Request $request, int $eventId): ?RegistrationUser
    {
        $sessionEventId = (int) $request->session()->get(self::SESSION_EVENT_KEY);
        $userId = (int) $request->session()->get(self::SESSION_USER_KEY);

        if ($userId && $sessionEventId === $eventId) {
            $user = RegistrationUser::with('registration')->find($userId);
            if ($user && $user->registration && (int) $user->registration->event_id === $eventId) {
                return $user;
            }
        }

        return $this->tryRememberLogin($request, $eventId);
    }

    public function logout(Request $request): void
    {
        $tokenValue = $request->cookie(self::REMEMBER_COOKIE);
        if ($tokenValue) {
            RegistrationUserRememberToken::where('token', $tokenValue)->delete();
            Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
        }

        $request->session()->forget([self::SESSION_USER_KEY, self::SESSION_EVENT_KEY]);
    }
}
