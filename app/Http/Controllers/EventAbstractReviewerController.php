<?php

namespace App\Http\Controllers;

use App\Models\AbstractReviewer;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Services\AbstractReviewerInvitationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventAbstractReviewerController extends MyBaseController
{
    protected $invitationService;

    public function __construct(AbstractReviewerInvitationService $invitationService)
    {
        parent::__construct();
        $this->invitationService = $invitationService;
    }

    public function showCreateReviewer($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $abstracts = EventAbstract::where('event_id', $event_id)->orderBy('name')->get();

        return view('ManageEvent.Modals.CreateAbstractReviewer', compact('event', 'abstracts'));
    }

    public function postCreateReviewer(Request $request, $event_id)
    {
        Event::scope()->findOrFail($event_id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('abstract_reviewers', 'email')->where('event_id', $event_id),
            ],
            'password' => 'required|min:6|max:255',
            'access_all_abstracts' => 'nullable',
            'abstract_ids' => 'nullable|array',
            'abstract_ids.*' => [
                'integer',
                Rule::exists('abstracts', 'id')->where('event_id', $event_id),
            ],
            'can_review' => 'nullable',
            'can_edit' => 'nullable',
            'can_delete' => 'nullable',
            'is_active' => 'nullable',
        ]);

        $accessAll = $request->has('access_all_abstracts');
        $validator->after(function ($v) use ($request, $accessAll) {
            if (!$accessAll && empty($request->get('abstract_ids'))) {
                $v->errors()->add('abstract_ids', trans('Abstract.reviewer_select_abstracts'));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $plainPassword = $request->get('password');
            $reviewer = AbstractReviewer::create([
                'event_id' => $event_id,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($plainPassword),
                'is_active' => $request->has('is_active'),
                'access_all_abstracts' => $accessAll,
                'can_review' => $request->has('can_review'),
                'can_edit' => $request->has('can_edit'),
                'can_delete' => $request->has('can_delete'),
            ]);

            if (!$accessAll) {
                $reviewer->abstracts()->sync($request->get('abstract_ids', []));
            }

            DB::commit();

            $event = Event::scope()->findOrFail($event_id);
            $this->invitationService->send($event, $reviewer, $plainPassword);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.reviewer_created'),
            'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'reviewers']),
        ]);
    }

    public function showEditReviewer($event_id, $reviewer_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $reviewer = AbstractReviewer::where('event_id', $event_id)->with('abstracts')->findOrFail($reviewer_id);
        $abstracts = EventAbstract::where('event_id', $event_id)->orderBy('name')->get();

        return view('ManageEvent.Modals.EditAbstractReviewer', compact('event', 'reviewer', 'abstracts'));
    }

    public function postEditReviewer(Request $request, $event_id, $reviewer_id)
    {
        Event::scope()->findOrFail($event_id);
        $reviewer = AbstractReviewer::where('event_id', $event_id)->findOrFail($reviewer_id);

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
            'password' => 'nullable|min:6|max:255',
            'access_all_abstracts' => 'nullable',
            'abstract_ids' => 'nullable|array',
            'abstract_ids.*' => [
                'integer',
                Rule::exists('abstracts', 'id')->where('event_id', $event_id),
            ],
            'can_review' => 'nullable',
            'can_edit' => 'nullable',
            'can_delete' => 'nullable',
            'is_active' => 'nullable',
        ]);

        $accessAll = $request->has('access_all_abstracts');
        $validator->after(function ($v) use ($request, $accessAll) {
            if (!$accessAll && empty($request->get('abstract_ids'))) {
                $v->errors()->add('abstract_ids', trans('Abstract.reviewer_select_abstracts'));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $reviewer->name = $request->get('name');
            $reviewer->email = $request->get('email');
            $reviewer->is_active = $request->has('is_active');
            $reviewer->access_all_abstracts = $accessAll;
            $reviewer->can_review = $request->has('can_review');
            $reviewer->can_edit = $request->has('can_edit');
            $reviewer->can_delete = $request->has('can_delete');

            if ($request->filled('password')) {
                $reviewer->password = Hash::make($request->get('password'));
            }

            $reviewer->save();

            if ($accessAll) {
                $reviewer->abstracts()->sync([]);
            } else {
                $reviewer->abstracts()->sync($request->get('abstract_ids', []));
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.reviewer_updated'),
            'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'reviewers']),
        ]);
    }

    public function postDeleteReviewer(Request $request, $event_id, $reviewer_id)
    {
        Event::scope()->findOrFail($event_id);
        $reviewer = AbstractReviewer::where('event_id', $event_id)->findOrFail($reviewer_id);
        $reviewer->abstracts()->detach();
        $reviewer->delete();

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.reviewer_deleted'),
        ]);
    }
}
