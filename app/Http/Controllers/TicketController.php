<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\RegistrationUser;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $pdfPath = $this->ticketService->generateTicketPDF($user);
            $user->ticket_pdf_path = $pdfPath;
            $user->ticket_generated_at = now();
            $user->save();
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($user->ticket_pdf_path)) {
            // Regenerate if file is missing
            $pdfPath = $this->ticketService->generateTicketPDF($user);
            $user->ticket_pdf_path = $pdfPath;
            $user->ticket_generated_at = now();
            $user->save();
        }

        // Return the file for download
        return response()->download(
            storage_path('app/public/' . $user->ticket_pdf_path),
            'ticket_' . $user->unique_code . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
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
     * Admin function to download a user's ticket
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return mixed
     */
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
            $pdfPath = $this->ticketService->generateTicketPDF($user);
            $user->ticket_pdf_path = $pdfPath;
            $user->ticket_generated_at = now();
            $user->save();
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($user->ticket_pdf_path)) {
            // Regenerate if file is missing
            $pdfPath = $this->ticketService->generateTicketPDF($user);
            $user->ticket_pdf_path = $pdfPath;
            $user->ticket_generated_at = now();
            $user->save();
        }

        // Return the file for download
        return response()->download(
            storage_path('app/public/' . $user->ticket_pdf_path),
            'ticket_' . $user->unique_code . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
