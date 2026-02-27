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

        $this->deleteTicketFilesAndPath($user);
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
                $this->deleteTicketFilesAndPath($user);
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
     * Delete ticket PDF + image files and clear path/timestamp. Keeps unique_code and ticket_token.
     *
     * @param RegistrationUser $user
     * @return void
     */
    private function deleteTicketFilesAndPath(RegistrationUser $user)
    {
        if ($user->ticket_pdf_path && Storage::disk('public')->exists($user->ticket_pdf_path)) {
            Storage::disk('public')->delete($user->ticket_pdf_path);
        }
        $ticketImagePath = 'ticket_images/ticket_' . $user->unique_code . '.png';
        if (Storage::disk('public')->exists($ticketImagePath)) {
            Storage::disk('public')->delete($ticketImagePath);
        }
        $user->ticket_pdf_path = null;
        $user->ticket_generated_at = null;
        $user->save();
    }

}
