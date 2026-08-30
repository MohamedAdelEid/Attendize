<?php

namespace App\Http\Middleware;

use App\Services\AttendeePortalAuthService;
use Closure;
use Illuminate\Http\Request;

class EnsureAttendeePortalAccess
{
    protected $auth;

    public function __construct(AttendeePortalAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $eventId = (int) $request->route('event_id');
        $user = $this->auth->currentUser($request, $eventId);

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('showAttendeePortalLogin', ['event_id' => $eventId]));
        }

        $request->attributes->set('attendee_user', $user);

        return $next($request);
    }
}
