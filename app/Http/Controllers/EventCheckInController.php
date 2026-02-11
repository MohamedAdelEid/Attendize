<?php
namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Attendee;
use App\Models\CheckInCheckOutLog;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use JavaScript;

class EventCheckInController extends MyBaseController
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showGuestKiosk']);
    }

    public function PostScanTicket(Request $request, $event_id)
    {
        $eventId = $event_id;
        $event = Event::find($eventId);
        if (!$event) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Event not found.'], 404);
            }
            return redirect()->back()->with('error', 'Event not found.');
        }

        $input = trim($request->unique_code ?? $request->input('unique_code_or_email', ''));
        if ($input === '') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please enter a unique code or email.'
                ]);
            }
            return redirect()->back()->with('error', 'Please enter a unique code or email.');
        }

        // Look up by email or unique code (scoped to this event)
        if (strpos($input, '@') !== false) {
            $uniqueCodeFromDb = RegistrationUser::whereHas('registration', function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('email', $input)->first();
        } else {
            $uniqueCodeFromDb = RegistrationUser::whereHas('registration', function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('unique_code', $input)->first();
        }

        if (is_null($uniqueCodeFromDb)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid ticket code or email. Please check and try again.'
                ]);
            }
            return redirect()->back()->with('error', 'Invalid QR Code or email');
        }

        // Get the latest log entry to determine current status
        $latestLog = CheckInCheckOutLog::where('registration_user_id', $uniqueCodeFromDb->id)
            ->where('event_id', $event->id)
            ->orderBy('action_time', 'desc')
            ->first();

        $actionTime = Carbon::now();
        $action = null;
        $message = '';
        $successMessage = '';

        // Determine action based on latest log entry
        // If no log exists or last action was check_out, then check_in
        // If last action was check_in, then check_out
        if (is_null($latestLog) || $latestLog->action === 'check_out') {
            // Perform Check-in
            $uniqueCodeFromDb->update([
                'check_in' => $actionTime,
                'check_out' => null
            ]);

            $action = 'check_in';
            $message = 'Successfully checked in!';
            $successMessage = 'User Checked In Successfully';
        } else {
            // Perform Check-out
            $uniqueCodeFromDb->update([
                'check_out' => $actionTime
            ]);

            $action = 'check_out';
            $message = 'Successfully checked out!';
            $successMessage = 'User Checked Out Successfully';
        }

        // Log the action
        CheckInCheckOutLog::create([
            'registration_user_id' => $uniqueCodeFromDb->id,
            'event_id' => $event->id,
            'action' => $action,
            'action_time' => $actionTime,
        ]);

        // Refresh to get updated data
        $uniqueCodeFromDb->refresh();

        if ($request->ajax() || $request->wantsJson()) {
            $userPayload = [
                'id' => $uniqueCodeFromDb->id,
                'first_name' => $uniqueCodeFromDb->first_name,
                'last_name' => $uniqueCodeFromDb->last_name,
                'email' => $uniqueCodeFromDb->email,
                'unique_code' => $uniqueCodeFromDb->unique_code,
                'check_in' => $uniqueCodeFromDb->check_in,
                'check_out' => $uniqueCodeFromDb->check_out,
            ];
            if ($uniqueCodeFromDb->ticket_token) {
                $userPayload['ticket_token'] = $uniqueCodeFromDb->ticket_token;
            }
            return response()->json([
                'status' => 'success',
                'action' => $action,
                'message' => $message,
                'user' => $userPayload,
            ]);
        }

        return redirect()->back()->with([
            'success' => $successMessage,
            'action' => $action,
            'user' => $uniqueCodeFromDb,
            'unique_code_input' => $input,
        ]);
    }

    public function getAttendanceStats(Event $event)
    {
        $totalRegistrations = $event->registrations->sum(function ($registration) {
            return $registration->registrationUsers()->count();
        });

        // Currently checked in (latest attendance is check_in without check_out)
        $currentlyCheckedIn = Attendances::where('event_id', $event->id)
            ->where('status', 'checked_in')
            ->whereNull('check_out')
            ->distinct('registration_user_id')
            ->count();

        // Total unique users who have checked out at least once
        $totalCheckedOut = Attendances::where('event_id', $event->id)
            ->whereNotNull('check_out')
            ->distinct('registration_user_id')
            ->count();

        // Total attendance records (all check-ins)
        $totalCheckIns = Attendances::where('event_id', $event->id)->count();

        return [
            'total_registrations' => $totalRegistrations,
            'currently_checked_in' => $currentlyCheckedIn,
            'total_checked_out' => $totalCheckedOut,
            'total_check_ins' => $totalCheckIns
        ];
    }

    /**
     * Bulk check-in: all approved users who are not currently in (no check_in or already checked out).
     */
    public function bulkCheckIn(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $users = RegistrationUser::whereHas('registration', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('check_in')->orWhereNotNull('check_out');
            })
            ->get();

        $actionTime = Carbon::now();
        $count = 0;
        foreach ($users as $user) {
            $user->update(['check_in' => $actionTime, 'check_out' => null]);
            CheckInCheckOutLog::create([
                'registration_user_id' => $user->id,
                'event_id' => $event->id,
                'action' => 'check_in',
                'action_time' => $actionTime,
            ]);
            $count++;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $count . ' attendee(s) checked in.', 'count' => $count]);
        }
        return redirect()->back()->with('success', $count . ' attendee(s) checked in.');
    }

    /**
     * Bulk check-out: all approved users who are currently checked in (have check_in, no check_out).
     */
    public function bulkCheckOut(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $users = RegistrationUser::whereHas('registration', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
            ->where('status', 'approved')
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->get();

        $actionTime = Carbon::now();
        $count = 0;
        foreach ($users as $user) {
            $user->update(['check_out' => $actionTime]);
            CheckInCheckOutLog::create([
                'registration_user_id' => $user->id,
                'event_id' => $event->id,
                'action' => 'check_out',
                'action_time' => $actionTime,
            ]);
            $count++;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $count . ' attendee(s) checked out.', 'count' => $count]);
        }
        return redirect()->back()->with('success', $count . ' attendee(s) checked out.');
    }

    /**
     * Show the check-in page
     *
     * @param $event_id
     * @return \Illuminate\View\View
     */
    public function showCheckIn($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $data = [
            'event' => $event,
        ];

        return view('tickets.scanner', $data);
    }

    /**
     * Show the Guest / Self-Service Kiosk page (no auth required).
     * QR Scanner + Manual Check-in/Check-out only; no bulk actions or dashboard.
     *
     * @param int $event_id
     * @return \Illuminate\View\View
     */
    public function showGuestKiosk($event_id)
    {
        $event = Event::findOrFail($event_id);
        return view('tickets.guest-kiosk', ['event' => $event]);
    }

    public function showCheckInDashboard($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        
        // Get only approved registration users with pagination
        $query = RegistrationUser::whereHas('registration', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
        ->where('status', 'approved')
        ->with(['registration', 'checkInCheckOutLogs']);

        // Apply search filter if provided
        $search = request()->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('unique_code', 'like', '%' . $search . '%');
            });
        }

        // Apply attendance filter if provided
        $attendanceFilter = request()->get('attendance');
        if ($attendanceFilter === 'checked_in') {
            $query->whereNotNull('check_in')->whereNull('check_out');
        } elseif ($attendanceFilter === 'checked_out') {
            $query->whereNotNull('check_out');
        } elseif ($attendanceFilter === 'not_checked_in') {
            $query->whereNull('check_in');
        }

        // Get paginated results
        $perPage = request()->get('per_page', 15);
        $registrationUsers = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = [
            'event' => $event,
            'registrationUsers' => $registrationUsers
        ];

        return view('tickets.dashboard', $data);
    }

    public function showQRCodeModal(Request $request, $event_id)
    {
        return view('ManageEvent.Modals.QrcodeCheckIn');
    }

    /**
     * Search attendees
     *
     * @param  Request  $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInSearch(Request $request, $event_id)
    {
        $searchQuery = $request->get('q');

        $attendees = Attendee::scope()
            ->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->where(function ($query) use ($event_id) {
                $query->where('attendees.event_id', '=', $event_id);
            })
            ->where(function ($query) use ($searchQuery) {
                $query
                    ->orWhere('attendees.first_name', 'like', $searchQuery . '%')
                    ->orWhere(
                        DB::raw("CONCAT_WS(' ', attendees.first_name, attendees.last_name)"),
                        'like',
                        $searchQuery . '%'
                    )
                    // ->orWhere('attendees.email', 'like', $searchQuery . '%')
                    ->orWhere('orders.order_reference', 'like', $searchQuery . '%')
                    ->orWhere('attendees.private_reference_number', 'like', $searchQuery . '%')
                    ->orWhere('attendees.last_name', 'like', $searchQuery . '%');
            })
            ->select([
                'attendees.id',
                'attendees.first_name',
                'attendees.last_name',
                'attendees.email',
                'attendees.arrival_time',
                'attendees.reference_index',
                'attendees.has_arrived',
                'tickets.title as ticket',
                'orders.order_reference',
                'orders.is_payment_received'
            ])
            ->orderBy('attendees.first_name', 'ASC')
            ->get();

        return response()->json($attendees);
    }

    /**
     * Check in/out an attendee
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendee(Request $request)
    {
        $attendee_id = $request->get('attendee_id');
        $checking = $request->get('checking');

        $attendee = Attendee::scope()->find($attendee_id);

        /*
         * Ugh
         */
        if ((($checking == 'in') && ($attendee->has_arrived == 1)) || (($checking == 'out') && ($attendee->has_arrived == 0))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendee Already Checked ' . (($checking == 'in') ? 'In (at ' . $attendee->arrival_time->format('H:i A, F j') . ')' : 'Out') . '!',
                'checked' => $checking,
                'id' => $attendee->id,
            ]);
        }

        $attendee->has_arrived = ($checking == 'in') ? 1 : 0;
        $attendee->arrival_time = Carbon::now();
        $attendee->save();

        return response()->json([
            'status' => 'success',
            'checked' => $checking,
            'message' => (($checking == 'in') ? trans('Controllers.attendee_successfully_checked_in') : trans('Controllers.attendee_successfully_checked_out')),
            'id' => $attendee->id,
        ]);
    }

    /**
     * Check in an attendee
     *
     * @param $event_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendeeQr($event_id, Request $request)
    {
        $event = Event::scope()->findOrFail($event_id);

        $qrcodeToken = $request->get('attendee_reference');
        $attendee = Attendee::scope()
            ->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where(function ($query) use ($event, $qrcodeToken) {
                $query
                    ->where('attendees.event_id', $event->id)
                    ->where('attendees.private_reference_number', $qrcodeToken);
            })
            ->select([
                'attendees.id',
                'attendees.order_id',
                'attendees.first_name',
                'attendees.last_name',
                'attendees.email',
                'attendees.reference_index',
                'attendees.arrival_time',
                'attendees.has_arrived',
                'tickets.title as ticket',
            ])
            ->first();

        if (is_null($attendee)) {
            return response()->json([
                'status' => 'error',
                'message' => trans('Controllers.invalid_ticket_error')
            ]);
        }

        $relatedAttendesCount = Attendee::where('id', '!=', $attendee->id)
            ->where([
                'order_id' => $attendee->order_id,
                'has_arrived' => false
            ])
            ->count();

        if ($attendee->has_arrived) {
            return response()->json([
                'status' => 'error',
                'message' => trans('Controllers.attendee_already_checked_in',
                    ['time' => $attendee->arrival_time->format(config('attendize.default_datetime_format'))])
            ]);
        }

        Attendee::find($attendee->id)->update(['has_arrived' => true, 'arrival_time' => Carbon::now()]);

        return response()->json([
            'status' => 'success',
            'name' => $attendee->first_name . ' ' . $attendee->last_name,
            'reference' => $attendee->reference,
            'ticket' => $attendee->ticket
        ]);
    }

    public function fetchRegistrationUsers($event_id)
    {
        $event = Event::find($event_id);
        
        // Build query
        $query = RegistrationUser::whereHas('registration', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
        ->with(['registration']);

        // Apply search filter if provided
        $search = request()->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('unique_code', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter if provided
        $statusFilter = request()->get('status');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Apply attendance filter if provided
        $attendanceFilter = request()->get('attendance');
        if ($attendanceFilter === 'checked_in') {
            $query->whereNotNull('check_in')->whereNull('check_out');
        } elseif ($attendanceFilter === 'checked_out') {
            $query->whereNotNull('check_out');
        } elseif ($attendanceFilter === 'not_checked_in') {
            $query->whereNull('check_in');
        }

        // Get paginated results
        $perPage = request()->get('per_page', 15);
        $registrationUsers = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'registrationUsers' => $registrationUsers->items(),
            'current_page' => $registrationUsers->currentPage(),
            'last_page' => $registrationUsers->lastPage(),
            'per_page' => $registrationUsers->perPage(),
            'total' => $registrationUsers->total(),
            'from' => $registrationUsers->firstItem(),
            'to' => $registrationUsers->lastItem(),
        ]);
    }

    /**
     * Get check-in/check-out logs for a specific user
     *
     * @param $event_id
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLogs($event_id, $user_id)
    {
        $logs = CheckInCheckOutLog::where('registration_user_id', $user_id)
            ->where('event_id', $event_id)
            ->orderBy('action_time', 'desc')
            ->get();

        return response()->json([
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'action_time' => $log->action_time->format('Y-m-d H:i:s'),
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }
}
