<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationUser;
use Illuminate\Http\Request;
use Mail;

class RegistrationUsersController extends Controller
{
    /**
     * Show all users registered across all registration forms for an event.
     *
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function showEventRegistrationUsers(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        // Get all registration IDs for this event
        $registrationIds = Registration::where('event_id', $event_id)->pluck('id')->toArray();

        // Get filters from request
        $filters = $request->only(['search', 'status', 'registration_id']);

        // Query registration users with filters
        $query = RegistrationUser::whereIn('registration_id', $registrationIds)
            ->with('registration');

        // Apply search filter if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filters['status']);
        }

        // Apply registration filter if provided
        if (!empty($filters['registration_id'])) {
            $query->where('registration_id', $filters['registration_id']);
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all registrations for the filter dropdown
        $registrations = Registration::where('event_id', $event_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // Mark all new registrations as viewed
        if ($request->has('mark_as_viewed')) {
            RegistrationUser::whereIn('registration_id', $registrationIds)
                ->where('is_new', true)
                ->update(['is_new' => false]);

            return redirect()->route('showEventRegistrationUsers', ['event_id' => $event_id]);
        }

        return view('ManageEvent.RegistrationUsers', compact('event', 'users', 'filters', 'registrations'));
    }

    /**
     * Show all users registered for a specific registration form.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationUsers(Request $request, $event_id, $registration_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $registration = Registration::findOrFail($registration_id);

        // Get filters from request
        $filters = $request->only(['search', 'status']);

        // Query registration users with filters
        $query = RegistrationUser::where('registration_id', $registration_id)
            ->with('registration');

        // Apply search filter if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filters['status']);
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all registrations for the filter dropdown (for potential switching)
        $registrations = Registration::where('event_id', $event_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // Mark new registrations for this form as viewed
        if ($request->has('mark_as_viewed')) {
            RegistrationUser::where('registration_id', $registration_id)
                ->where('is_new', true)
                ->update(['is_new' => false]);

            return redirect()->route('showRegistrationUsers', ['event_id' => $event_id, 'registration_id' => $registration_id]);
        }

        return view('ManageEvent.RegistrationUsers', compact('event', 'registration', 'users', 'filters', 'registrations'));
    }

    /**
     * Update the status of a registration user.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $registration_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function updateUserStatus(Request $request, $event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);
        // Validate the request
        $this->validate($request, [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $user->status = $request->input('status');
        $user->save();

        // Send notification email based on status change
        if ($request->input('status') === 'approved') {
            // Send approval email
            Mail::to($user->email)->send(new RegistrationApproved($user, $event));
        } elseif ($request->input('status') === 'rejected') {
            // Send rejection email
            Mail::to($user->email)->send(new RegistrationRejected($user, $event));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated successfully',
        ]);
    }

    /**
     * Delete a registration user.
     *
     * @param int $event_id
     * @param int $registration_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser($event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Bulk update user statuses.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdateUsers(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $userIds = $request->input('user_ids');
        $action = $request->input('action');

        if ($action === 'delete') {
            RegistrationUser::whereIn('id', $userIds)->delete();
            $message = 'Selected users have been deleted';
        } else {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            RegistrationUser::whereIn('id', $userIds)->update(['status' => $status]);

            // Send emails to users
            // $users = RegistrationUser::whereIn('id', $userIds)->get();
            // foreach ($users as $user) {
            //     if ($status === 'approved') {
            //         Mail::to($user->email)->send(new RegistrationApproved($user));
            //     } else {
            //         Mail::to($user->email)->send(new RegistrationRejected($user));
            //     }
            // }

            $message = 'Selected users have been ' . $status;
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Get user details for the modal.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function getUserDetails($event_id, $user_id)
    {
        $user = RegistrationUser::with(['registration', 'formFieldValues.field'])->findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        return view('ManageEvent.Modals.UserDetails', compact('user', 'event'));
    }
}
