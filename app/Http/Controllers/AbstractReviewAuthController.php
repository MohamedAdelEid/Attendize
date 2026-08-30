<?php

namespace App\Http\Controllers;

use App\Models\AbstractReviewer;
use App\Models\AbstractReviewerLoginToken;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbstractReviewAuthController extends Controller
{
    public function showLogin($event_id)
    {
        $event = Event::findOrFail($event_id);

        if (Auth::guard('abstract_reviewer')->check()) {
            $reviewer = Auth::guard('abstract_reviewer')->user();
            if ((int) $reviewer->event_id === (int) $event_id && $reviewer->is_active) {
                return redirect()->route('showAbstractReviewDashboard', ['event_id' => $event_id]);
            }
            Auth::guard('abstract_reviewer')->logout();
        }

        return view('AbstractReview.login', compact('event'));
    }

    public function login(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->only('email'));
        }

        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'event_id' => $event_id,
            'is_active' => 1,
        ];

        if (!Auth::guard('abstract_reviewer')->attempt($credentials, $request->has('remember'))) {
            return redirect()->back()
                ->withErrors(['email' => trans('Abstract.reviewer_login_failed')])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        /** @var AbstractReviewer $reviewer */
        $reviewer = Auth::guard('abstract_reviewer')->user();
        $reviewer->last_login_at = Carbon::now();
        $reviewer->save();

        return redirect()->intended(route('showAbstractReviewDashboard', ['event_id' => $event->id]));
    }

    public function logout(Request $request, $event_id)
    {
        Auth::guard('abstract_reviewer')->logout();

        return redirect()->route('showAbstractReviewLogin', ['event_id' => $event_id]);
    }

    public function welcomeLogin(Request $request, $event_id, $token)
    {
        $event = Event::findOrFail($event_id);

        $loginToken = AbstractReviewerLoginToken::where('token', $token)
            ->whereHas('reviewer', function ($q) use ($event_id) {
                $q->where('event_id', $event_id)->where('is_active', true);
            })
            ->first();

        if (!$loginToken || !$loginToken->isValid()) {
            return redirect()->route('showAbstractReviewLogin', ['event_id' => $event_id])
                ->withErrors(['email' => trans('Abstract.reviewer_link_expired')]);
        }

        $reviewer = $loginToken->reviewer;
        $loginToken->used_at = Carbon::now();
        $loginToken->save();

        Auth::guard('abstract_reviewer')->login($reviewer);
        $request->session()->regenerate();

        $reviewer->last_login_at = Carbon::now();
        $reviewer->save();

        return redirect()->route('showAbstractReviewDashboard', ['event_id' => $event_id])
            ->with('success', trans('Abstract.reviewer_welcome', ['name' => $reviewer->name]));
    }
}
