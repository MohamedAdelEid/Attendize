<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AbstractReviewSettingsController extends Controller
{
    public function show(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $reviewer = Auth::guard('abstract_reviewer')->user();

        return view('AbstractReview.settings', compact('event', 'reviewer'));
    }

    public function update(Request $request, $event_id)
    {
        $reviewer = Auth::guard('abstract_reviewer')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('abstract_reviewers', 'email')
                    ->where('event_id', $event_id)
                    ->ignore($reviewer->id),
            ],
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->get('current_password'), $reviewer->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => trans('Abstract.reviewer_wrong_password')])
                    ->withInput();
            }
            $reviewer->password = Hash::make($request->get('password'));
        }

        $reviewer->name = $request->get('name');
        $reviewer->email = $request->get('email');
        $reviewer->save();

        return redirect()->route('showAbstractReviewSettings', ['event_id' => $event_id])
            ->with('success', trans('Abstract.reviewer_settings_saved'));
    }
}
