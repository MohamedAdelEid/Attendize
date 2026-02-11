<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\UserType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventUserTypeController extends MyBaseController
{
    /**
     * Display user types for an event
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showUserTypes(Request $request, $event_id)
    {
        $allowed_sorts = ['name', 'created_at'];

        // Getting get parameters
        $searchQuery = $request->get('q');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');

        // Find event or return 404 error
        $event = Event::scope()->find($event_id);
        if ($event === null) {
            abort(404);
        }

        // Get user types for event with search
        $query = $event->userTypes();
        
        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }
        
        $userTypes = $query->orderBy($sort_by, $sort_order)->paginate(15);

        $data = [
            'userTypes' => $userTypes,
            'event' => $event,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'q' => $searchQuery ? $searchQuery : '',
        ];

        return view('ManageEvent.UserTypes', $data);
    }

    /**
     * Show the create user type modal
     *
     * @param $event_id
     * @return \Illuminate\Contracts\View\View
     */
    public function showCreateUserType($event_id)
    {
        return view('ManageEvent.Modals.CreateUserType', [
            'event' => Event::scope()->find($event_id),
        ]);
    }

    /**
     * Creates a user type
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCreateUserType(Request $request, $event_id)
    {
        DB::beginTransaction();

        try {
            $userType = new UserType();

            if (!$userType->validate($request->all())) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $userType->errors(),
                ]);
            }

            $userType = UserType::create([
                'name' => $request->name,
                'event_id' => $event_id,
            ]);

            DB::commit();

            session()->flash('message', 'Successfully Created User Type');

            return response()->json([
                'status' => 'success',
                'id' => $userType->id,
                'message' => trans("Controllers.refreshing"),
                'redirectUrl' => route('showEventUserTypes', [
                    'event_id' => $event_id,
                ]),
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the 'Edit User Type' modal
     *
     * @param Request $request
     * @param $event_id
     * @param $user_type_id
     * @return View
     */
    public function showEditUserType(Request $request, $event_id, $user_type_id)
    {
        $userType = UserType::findOrFail($user_type_id);

        $data = [
            'userType' => $userType,
            'event' => $userType->event,
        ];

        return view('ManageEvent.Modals.EditUserType', $data);
    }

    /**
     * Updates a user type
     *
     * @param Request $request
     * @param $event_id
     * @param $user_type_id
     * @return mixed
     */
    public function postEditUserType(Request $request, $event_id, $user_type_id)
    {
        DB::beginTransaction();

        try {
            $userType = UserType::findOrFail($user_type_id);

            if (!$userType->validate($request->all())) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $userType->errors(),
                ]);
            }

            $userType->name = $request->input('name');
            $userType->save();

            DB::commit();

            session()->flash('message', 'Successfully Updated User Type');

            return response()->json([
                'status' => 'success',
                'id' => $userType->id,
                'message' => trans("Controllers.refreshing"),
                'redirectUrl' => route('showEventUserTypes', [
                    'event_id' => $event_id,
                ]),
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the 'Delete User Type' modal
     *
     * @param Request $request
     * @param $event_id
     * @param $user_type_id
     * @return View
     */
    public function showDeleteUserType(Request $request, $event_id, $user_type_id)
    {
        $userType = UserType::findOrFail($user_type_id);

        $data = [
            'userType' => $userType,
        ];

        return view('ManageEvent.Modals.DeleteUserType', $data);
    }

    /**
     * Delete a user type
     *
     * @param Request $request
     * @param $event_id
     * @param $user_type_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteUserType(Request $request, $event_id, $user_type_id)
    {
        DB::beginTransaction();

        try {
            $userType = UserType::find($user_type_id);

            if (!$userType) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User type not found.',
                ]);
            }

            $userType->delete();

            DB::commit();

            session()->flash('message', 'Successfully Deleted User Type');

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Deleted User Type',
                'id' => $user_type_id,
                'redirectUrl' => route('showEventUserTypes', [
                    'event_id' => $event_id,
                ]),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete multiple user types at once
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postBulkDeleteUserTypes(Request $request, $event_id)
    {
        $request->validate([
            'user_type_ids' => 'required|array',
            'user_type_ids.*' => 'integer'
        ]);

        $userTypeIds = $request->user_type_ids;
        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        try {
            foreach ($userTypeIds as $userTypeId) {
                $userType = UserType::find($userTypeId);

                if (!$userType) {
                    $errorCount++;
                    continue;
                }

                $userType->delete();
                $successCount++;
            }

            DB::commit();

            $message = $successCount > 0
                ? "Successfully deleted {$successCount} user types"
                : "No user types were deleted";

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'counts' => [
                    'success' => $successCount,
                    'error' => $errorCount
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
