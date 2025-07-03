<?php
namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use JavaScript;

class EventCheckInController extends MyBaseController
{
    public function PostScanTicket(Request $request)
    {
        $event = Event::find($request->event_id);
        $qrCode = $request->unique_code;

        // Find the registration user
        $registrationUser = RegistrationUser::where('unique_code', $qrCode)->first();

        if (is_null($registrationUser)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid ticket code. Please check the code and try again.'
                ]);
            }
            return redirect()->back()->with('error', 'Invalid QR Code');
        }

        // Get the latest attendance record for this user and event
        $latestAttendance = Attendances::where('registration_user_id', $registrationUser->id)
            ->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $action = '';
        $message = '';
        $successMessage = '';

        // Determine the action based on the latest attendance record
        if (!$latestAttendance) {
            // First scan - Check In
            $attendance = Attendances::create([
                'registration_user_id' => $registrationUser->id,
                'event_id' => $event->id,
                'check_in' => Carbon::now(),
                'status' => 'checked_in'
            ]);

            $action = 'check_in';
            $message = 'Successfully checked in!';
            $successMessage = 'User Checked In Successfully';
        } elseif ($latestAttendance->status === 'checked_in' && is_null($latestAttendance->check_out)) {
            // Second scan - Check Out (update the same record)
            $latestAttendance->update([
                'check_out' => Carbon::now(),
                'status' => 'checked_out'
            ]);

            $attendance = $latestAttendance;
            $action = 'check_out';
            $message = 'Successfully checked out!';
            $successMessage = 'User Checked Out Successfully';
        } else {
            // Third scan and beyond - New Check In
            $attendance = Attendances::create([
                'registration_user_id' => $registrationUser->id,
                'event_id' => $event->id,
                'check_in' => Carbon::now(),
                'status' => 'checked_in'
            ]);

            $action = 'check_in';
            $message = 'Successfully checked in again!';
            $successMessage = 'User Checked In Successfully';
        }

        // Get attendance history for response
        $attendanceHistory = Attendances::where('registration_user_id', $registrationUser->id)
            ->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'action' => $action,
                'message' => $message,
                'user' => [
                    'id' => $registrationUser->id,
                    'first_name' => $registrationUser->first_name,
                    'last_name' => $registrationUser->last_name,
                    'email' => $registrationUser->email,
                    'unique_code' => $registrationUser->unique_code,
                    'current_status' => $attendance->status,
                    'last_check_in' => $attendance->check_in,
                    'last_check_out' => $attendance->check_out,
                    'attendance_history' => $attendanceHistory
                ]
            ]);
        }

        return redirect()->back()->with([
            'success' => $successMessage,
            'action' => $action,
            'user' => $registrationUser,
            'attendance' => $attendance
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
     * Show the check-in page
     *
     * @param $event_id
     * @return \Illuminate\View\View
     */
    public function showCheckIn($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $registrations = Registration::where('event_id', $event->id)->get();
        $data = [
            'event' => $event,
            'attendees' => $event->attendees,
            'registrations' => $registrations
        ];

        JavaScript::put([
            'qrcodeCheckInRoute' => route('postQRCodeCheckInAttendee', ['event_id' => $event->id]),
            'checkInRoute' => route('postCheckInAttendee', ['event_id' => $event->id]),
            'checkInSearchRoute' => route('postCheckInSearch', ['event_id' => $event->id]),
        ]);

        return view('tickets.scan-ticket', $data);
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
        $registrations = Registration::where('event_id', $event->id)->get();
        $registrationUsers = $registrations->registrationUsers()->get();
        return response()->json([
            'registrations' => $registrations,
            'registrationUsers' => $registrationUsers
        ]);
    }
}
