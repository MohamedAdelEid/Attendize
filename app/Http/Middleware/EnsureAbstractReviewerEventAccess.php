<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureAbstractReviewerEventAccess
{
    public function handle($request, Closure $next)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();
        $eventId = (int) $request->route('event_id');

        if (!$reviewer) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('showAbstractReviewLogin', ['event_id' => $eventId]));
        }

        if (!$reviewer->is_active) {
            Auth::guard('abstract_reviewer')->logout();

            return redirect()->route('showAbstractReviewLogin', ['event_id' => $eventId])
                ->withErrors(['email' => trans('Abstract.reviewer_inactive')]);
        }

        if ((int) $reviewer->event_id !== $eventId) {
            abort(403, 'This reviewer does not belong to this event.');
        }

        return $next($request);
    }
}
