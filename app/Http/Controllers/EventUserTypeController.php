<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\UserType;
use App\Models\UserTypeOption;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        
        $userTypes = $query->with('options')->withCount('registrationUsers')->orderBy($sort_by, $sort_order)->paginate(15);

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
        $event = Event::scope()->find($event_id);
        return view('ManageEvent.Modals.CreateUserType', ['event' => $event]);
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

            $slug = $this->uniqueUserTypeSlug($event_id, $request->name);
            $userType = UserType::create([
                'name' => $request->name,
                'slug' => $slug,
                'event_id' => $event_id,
                'show_on_landing' => $request->boolean('show_on_landing', true),
            ]);
            $optionNames = $request->input('option_names', []);
            if (is_array($optionNames)) {
                foreach (array_filter(array_map('trim', $optionNames)) as $i => $name) {
                    UserTypeOption::create([
                        'user_type_id' => $userType->id,
                        'name' => $name,
                        'slug' => $this->uniqueUserTypeOptionSlug($userType->id, $name),
                        'sort_order' => $i,
                    ]);
                }
            }
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
        $userType = UserType::with('options')->findOrFail($user_type_id);
        $event = $userType->event;
        return view('ManageEvent.Modals.EditUserType', [
            'userType' => $userType,
            'event' => $event,
        ]);
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
            $userType->slug = $this->uniqueUserTypeSlug($event_id, $userType->name, $userType->id);
            $userType->show_on_landing = $request->boolean('show_on_landing', true);
            $userType->save();
            $optionNames = $request->input('option_names', []);
            if (is_array($optionNames)) {
                $optionNames = array_values(array_filter(array_map('trim', $optionNames)));
                $existing = $userType->options()->pluck('name', 'id')->toArray();
                $toKeep = [];
                foreach ($optionNames as $i => $name) {
                    $found = array_search($name, $existing);
                    if ($found !== false) {
                        $toKeep[] = $found;
                        $userType->options()->where('id', $found)->update(['sort_order' => $i]);
                    } else {
                        $opt = UserTypeOption::create([
                            'user_type_id' => $userType->id,
                            'name' => $name,
                            'slug' => $this->uniqueUserTypeOptionSlug($userType->id, $name),
                            'sort_order' => $i,
                        ]);
                        $toKeep[] = $opt->id;
                    }
                }
                $userType->options()->whereNotIn('id', $toKeep)->delete();
            } else {
                $userType->options()->delete();
            }
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

    /**
     * Generate unique slug for user type (unique per event).
     */
    protected function uniqueUserTypeSlug($event_id, $name, $excludeId = null)
    {
        $slug = Str::slug($name);
        if (strlen($slug) === 0) {
            $slug = 'type';
        }
        $base = $slug;
        $n = 0;
        $q = UserType::where('event_id', $event_id)->where('slug', $slug);
        if ($excludeId !== null) {
            $q->where('id', '!=', $excludeId);
        }
        while ($q->exists()) {
            $slug = $base . '-' . (++$n);
            $q = UserType::where('event_id', $event_id)->where('slug', $slug);
            if ($excludeId !== null) {
                $q->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }

    /**
     * Generate unique slug for user type option (unique per user type).
     */
    protected function uniqueUserTypeOptionSlug($user_type_id, $name, $excludeId = null)
    {
        $slug = Str::slug($name);
        if (strlen($slug) === 0) {
            $slug = 'option';
        }
        $base = $slug;
        $n = 0;
        $q = UserTypeOption::where('user_type_id', $user_type_id)->where('slug', $slug);
        if ($excludeId !== null) {
            $q->where('id', '!=', $excludeId);
        }
        while ($q->exists()) {
            $slug = $base . '-' . (++$n);
            $q = UserTypeOption::where('user_type_id', $user_type_id)->where('slug', $slug);
            if ($excludeId !== null) {
                $q->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }
}
