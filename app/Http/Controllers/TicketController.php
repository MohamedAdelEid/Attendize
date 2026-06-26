<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\RegistrationUser;
use App\Services\TicketService;
use App\Models\CheckInCheckOutLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Download a ticket using the secure token
     *
     * @param Request $request
     * @param string $token
     * @return mixed
     */
    public function downloadTicket(Request $request, $token)
    {
        // Find user by token
        $user = RegistrationUser::where('ticket_token', $token)->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Invalid or expired ticket link.');
        }

        // Check if user is approved
        if ($user->status !== 'approved') {
            return redirect()->route('home')->with('error', 'Your registration has not been approved yet.');
        }

        // Generate PDF ticket if not already generated
        if (!$user->ticket_pdf_path) {
            $this->ticketService->renderTicketForUser($user);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($user->ticket_pdf_path)) {
            $this->ticketService->renderTicketForUser($user);
        }

        // Return the file for download
        return response()->download(
            storage_path('app/public/' . $user->ticket_pdf_path),
            'ticket_' . $user->unique_code . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
	
	
	public function downloadCertificate(Request $request, $token)
    {
        // Find user by token
		
		
		$event = Event::scope()->findOrFail(2);

        $user = RegistrationUser::where('ticket_token', $token)->first();

        if (!$user) {
           
			$data = [
				'event' => $event,
				'code' => 'Link is not working',
				'description' => 'Invalid or expired ticket link.',
			];

			
			return view('tickets.result', $data);
        }

        // Check if user is approved
        if ($user->status !== 'approved') {
           
			$data = [
				'event' => $event,
				'code' => 'Link is not working',
				'description' => 'Your registration has not been approved yet.',
			];

			
			return view('tickets.result', $data);
        }

		$attendance = CheckInCheckOutLog::where('registration_user_id', $user->id)->get();
		
		
		if(count($attendance) == 0){
			$data = [
				'event' => $event,
				'code' => 'Dear '.$user->first_name . ' '.$user->last_name,
				'description' => 'Our records indicate that you did not attend the event.',
			];

			
			return view('tickets.result', $data);
		}
		//dd($user->first_name.' '.$user->last_name);

		$data =[
			'attendee_name' => $user->first_name.' '.$user->last_name,
        ];
		


		//return view('frontend.ticket', $data);
		$html = view('tickets.certificate', $data)->render();
		
		$pdf= PDF::loadHTML($html);
		$pdf->setPaper('A4', 'landscape');
		
	
					
        return  $pdf->stream('SGSS2026-Certificate.pdf'); // download pdf file*/
		
    }

    /**
     * View a ticket in the browser
     *
     * @param Request $request
     * @param string $token
     * @return mixed
     */
    public function viewTicket(Request $request, $token)
    {
        // Find user by token
        $user = RegistrationUser::where('ticket_token', $token)->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Invalid or expired ticket link.');
        }

        // Check if user is approved
        if ($user->status !== 'approved') {
            return redirect()->route('home')->with('error', 'Your registration has not been approved yet.');
        }

        $event = $user->registration->event;

        return view('tickets.view', [
            'user' => $user,
            'event' => $event
        ]);
    }

    /**
     * View the template-based ticket (from ticket_template) for print/display.
     * Falls back to standard ticket view if no template image exists.
     *
     * @param Request $request
     * @param string $token
     * @return mixed
     */
    public function viewTicketTemplate(Request $request, $token)
    {
        $user = RegistrationUser::where('ticket_token', $token)->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Invalid or expired ticket link.');
        }

        if ($user->status !== 'approved') {
            return redirect()->route('home')->with('error', 'Your registration has not been approved yet.');
        }

        $ticketImagePath = $this->ticketService->getOrCreateTicketImagePath($user);

        if ($ticketImagePath) {
            return view('tickets.view-template', [
                'user' => $user,
                'event' => $user->registration->event,
                'ticket_image_path' => $ticketImagePath,
            ]);
        }

        // No template ticket: fall back to standard ticket view
        return redirect()->route('viewTicket', ['token' => $token]);
    }

    /**
     * Admin function to download a user's ticket
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return mixed
     */
     
    public function printUserTicket(Request $request, $event_id, $user_id)
    {

        $event = Event::findOrFail($event_id);
        $user = RegistrationUser::findOrFail($user_id);

        // Check if user belongs to this event
        if ($user->registration->event_id != $event_id) {
            return redirect()->back()->with('error', 'User does not belong to this event.');
        }

        
       // return view('admin.passport.approve_card',compact('passport','proffession_name','redirect'));


        // Check if user is approved
        if ($user->status !== 'approved') {
            return redirect()->route('home')->with('error', 'Your registration has not been approved yet.');
        }
        

        $event = $user->registration->event;
		$url = $request->url != null ? $request->url : url()->previous();
		

        return view('tickets.print', [
            'user' => $user,
            'event' => $event,
			'url' => $url,
        ]);
        
        
    }
    public function downloadUserTicket(Request $request, $event_id, $user_id)
    {
        $event = Event::findOrFail($event_id);
        $user = RegistrationUser::findOrFail($user_id);

        // Check if user belongs to this event
        if ($user->registration->event_id != $event_id) {
            return redirect()->back()->with('error', 'User does not belong to this event.');
        }

        // Generate PDF ticket if not already generated
        if (!$user->ticket_pdf_path) {
            $this->ticketService->renderTicketForUser($user);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($user->ticket_pdf_path)) {
            $this->ticketService->renderTicketForUser($user);
        }

        // Return the file for download
        return response()->download(
            storage_path('app/public/' . $user->ticket_pdf_path),
            'ticket_' . $user->unique_code . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Delete the current generated ticket for a user (files + path). CR code and token stay;
     * next Download will regenerate the ticket.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUserTicket(Request $request, $event_id, $user_id)
    {
        $event = Event::findOrFail($event_id);
        $user = RegistrationUser::findOrFail($user_id);

        if ($user->registration->event_id != $event_id) {
            return redirect()->back()->with('error', 'User does not belong to this event.');
        }

        if ($user->status !== 'approved' || !$user->unique_code) {
            return redirect()->back()->with('error', 'Ticket can only be deleted for approved users with a CR code.');
        }

        $this->ticketService->deleteRenderedTicketFiles($user);
        return redirect()->back()->with('success', 'Ticket deleted. Use Download to generate a new one.');
    }

    /**
     * Bulk delete tickets for selected users (same as delete ticket per user).
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDeleteUserTickets(Request $request, $event_id)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
        ]);

        $event = Event::findOrFail($event_id);
        $userIds = $request->input('user_ids');
        $users = RegistrationUser::whereIn('id', $userIds)
            ->whereHas('registration', function ($q) use ($event_id) {
                $q->where('event_id', $event_id);
            })
            ->get();

        $count = 0;
        foreach ($users as $user) {
            if ($user->status === 'approved' && $user->unique_code) {
                $this->ticketService->deleteRenderedTicketFiles($user);
                $count++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $count === 0
                ? 'No tickets were deleted (selected users have no generated ticket).'
                : "Ticket(s) deleted for {$count} user(s). Use Download to generate new ones.",
        ]);
    }

    /**
     * Render tickets in batches (all approved users or a selected subset).
     */
    public function renderEventTickets(Request $request, $event_id)
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        Event::findOrFail($event_id);

        $userIds = $request->input('user_ids');
        $query = $this->ticketService->approvedUsersQueryForEvent($event_id, $userIds);
        $total = (clone $query)->count();
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 20);

        $users = $query->offset($offset)->limit($limit)->get();

        $generated = 0;
        $failed = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                $this->ticketService->renderTicketForUser($user);
                $generated++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'message' => $e->getMessage(),
                ];
            }
        }

        $processed = $offset + $users->count();

        return response()->json([
            'status' => 'success',
            'generated' => $generated,
            'failed' => $failed,
            'total' => $total,
            'processed' => $processed,
            'done' => $processed >= $total,
            'next_offset' => $processed,
            'errors' => $errors,
            'message' => $generated . ' ticket(s) rendered in this batch.',
        ]);
    }

    /**
     * Delete rendered tickets in batches (all approved users or a selected subset).
     */
    public function deleteEventTickets(Request $request, $event_id)
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        Event::findOrFail($event_id);

        $userIds = $request->input('user_ids');
        $query = $this->ticketService->approvedUsersQueryForEvent($event_id, $userIds);
        $total = (clone $query)->count();
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 50);

        $users = $query->offset($offset)->limit($limit)->get();

        $deleted = 0;
        foreach ($users as $user) {
            if ($user->ticket_pdf_path || ($user->unique_code && Storage::disk('public')->exists('ticket_images/ticket_' . $user->unique_code . '.png'))) {
                $this->ticketService->deleteRenderedTicketFiles($user);
                $deleted++;
            }
        }

        $processed = $offset + $users->count();

        return response()->json([
            'status' => 'success',
            'deleted' => $deleted,
            'total' => $total,
            'processed' => $processed,
            'done' => $processed >= $total,
            'next_offset' => $processed,
            'message' => $deleted . ' ticket(s) deleted in this batch.',
        ]);
    }

    /**
     * Ticket render stats for an event.
     */
    public function eventTicketStats($event_id)
    {
        Event::findOrFail($event_id);

        $baseQuery = $this->ticketService->approvedUsersQueryForEvent($event_id);
        $eligible = (clone $baseQuery)->count();
        $rendered = (clone $baseQuery)->whereNotNull('ticket_pdf_path')->count();

        return response()->json([
            'status' => 'success',
            'eligible' => $eligible,
            'rendered' => $rendered,
            'pending' => max(0, $eligible - $rendered),
        ]);
    }

    /**
     * Delete ticket PDF + image files and clear path/timestamp. Keeps unique_code and ticket_token.
     *
     * @param RegistrationUser $user
     * @return void
     * @deprecated Use TicketService::deleteRenderedTicketFiles()
     */
    private function deleteTicketFilesAndPath(RegistrationUser $user)
    {
        $this->ticketService->deleteRenderedTicketFiles($user);
    }
}
