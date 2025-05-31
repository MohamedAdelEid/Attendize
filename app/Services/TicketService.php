<?php

namespace App\Services;

use App\Models\RegistrationUser;
use App\Models\TicketTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade as PDF;
use Intervention\Image\Facades\Image;

class TicketService
{
    /**
     * Generate a unique code for the user
     *
     * @param RegistrationUser $user
     * @return string
     */
    public function generateUniqueCode(RegistrationUser $user)
    {
        // Include part of user ID to ensure uniqueness
        $userIdPart = str_pad($user->id % 1000, 3, '0', STR_PAD_LEFT);
        $randomPart = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);

        $code = $userIdPart . $randomPart;

        // Ensure uniqueness
        while (RegistrationUser::where('unique_code', $code)->exists()) {
            $randomPart = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
            $code = $userIdPart . $randomPart;
        }

        return $code;
    }

    /**
     * Generate QR code for the user
     *
     * @param RegistrationUser $user
     * @param string $uniqueCode
     * @return string Path to the QR code
     */
    public function generateQRCode(RegistrationUser $user, string $uniqueCode)
    {
        // Create secure data for QR code
        $qrData = json_encode([
            'code' => $uniqueCode,
            'user_id' => $user->id,
            'event_id' => $user->registration->event_id,
            'timestamp' => now()->timestamp
        ]);

        // Generate QR code
        $qrCodeFileName = 'qr_codes/' . $user->id . '_' . time() . '.png';

        // Ensure directory exists
        if (!Storage::disk('public')->exists('qr_codes')) {
            Storage::disk('public')->makeDirectory('qr_codes');
        }

        // Generate and save QR code
        QrCode::format('png')
            ->size(200)
            ->margin(10)
            ->generate($qrData, storage_path('app/public/' . $qrCodeFileName));

        return $qrCodeFileName;
    }

    /**
     * Generate PDF ticket for the user
     *
     * @param RegistrationUser $user
     * @return string Path to the PDF ticket
     */
    public function generateTicketPDF(RegistrationUser $user)
    {
        $event = $user->registration->event;
        $template = TicketTemplate::where('event_id', $event->id)->first();

        // Create ticket image with template if available
        if ($template && $template->background_image_path) {
            $ticketImagePath = $this->createTicketImage($user, $template);
        }

        // Generate PDF
        $pdf = PDF::loadView('tickets.pdf-template', [
            'user' => $user,
            'event' => $event,
            'ticket_image' => $ticketImagePath ?? null,
            'template' => $template ?? null
        ]);

        // Save PDF
        $pdfFileName = 'tickets/ticket_' . $user->unique_code . '.pdf';

        // Ensure directory exists
        if (!Storage::disk('public')->exists('tickets')) {
            Storage::disk('public')->makeDirectory('tickets');
        }

        Storage::disk('public')->put($pdfFileName, $pdf->output());

        return $pdfFileName;
    }

    /**
     * Create ticket image with overlays based on template
     *
     * @param RegistrationUser $user
     * @param TicketTemplate $template
     * @return string Path to the ticket image
     */
    private function createTicketImage(RegistrationUser $user, TicketTemplate $template)
    {
        // Load background template
        $backgroundPath = storage_path('app/public/' . $template->background_image_path);
        $image = Image::make($backgroundPath);

        // Add user name
        if (isset($template->name_position_x) && isset($template->name_position_y)) {
            $image->text(
                $user->first_name . ' ' . $user->last_name,
                $template->name_position_x,
                $template->name_position_y,
                function($font) use ($template) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size($template->name_font_size ?? 24);
                    $font->color($template->name_font_color ?? '#000000');
                }
            );
        }

        // Add unique code
        if (isset($template->code_position_x) && isset($template->code_position_y)) {
            $image->text(
                $user->unique_code,
                $template->code_position_x,
                $template->code_position_y,
                function($font) use ($template) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size($template->code_font_size ?? 20);
                    $font->color($template->code_font_color ?? '#000000');
                }
            );
        }

        // Add QR code
        if (isset($template->qr_position_x) && isset($template->qr_position_y) && $user->qr_code_path) {
            $qrImage = Image::make(storage_path('app/public/' . $user->qr_code_path));
            $qrSize = $template->qr_size ?? 100;
            $qrImage->resize($qrSize, $qrSize);

            $image->insert(
                $qrImage,
                'top-left',
                $template->qr_position_x,
                $template->qr_position_y
            );
        }

        // Save the image
        $imagePath = 'ticket_images/ticket_' . $user->unique_code . '.png';

        // Ensure directory exists
        if (!Storage::disk('public')->exists('ticket_images')) {
            Storage::disk('public')->makeDirectory('ticket_images');
        }

        $image->save(storage_path('app/public/' . $imagePath));

        return $imagePath;
    }

    /**
     * Process user approval and generate ticket
     *
     * @param RegistrationUser $user
     * @return void
     */
    public function processApproval(RegistrationUser $user)
    {
        // Generate unique code
        $uniqueCode = $this->generateUniqueCode($user);
        $user->unique_code = $uniqueCode;

        // Generate QR code
        $qrCodePath = $this->generateQRCode($user, $uniqueCode);
        $user->qr_code_path = $qrCodePath;

        // Generate secure token for ticket download
        $user->ticket_token = Str::random(32);

        // Save user with new data
        $user->save();

        // Note: We don't generate the PDF here - it will be generated on demand when downloaded
    }
}
