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
        
        $qrData = $uniqueCode;

        // Generate QR code
        $qrCodeFileName = 'qr_codes/' . $user->id . '_' . time() . '.png';

        // Ensure directory exists
        if (!Storage::disk('public')->exists('qr_codes')) {
            Storage::disk('public')->makeDirectory('qr_codes');
        }

        // Generate and save QR code with minimal margin
        QrCode::format('png')
            ->size(220)
            ->margin(2) // Reduced from 10 to 1 for minimal white space
            ->generate($qrData, storage_path('app/public/' . $qrCodeFileName));

        return $qrCodeFileName;
    }

    /**
     * Generate PDF ticket for the user with proper scaling
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
        ])->setPaper('a4', 'landscape'); 
        

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
     * Check if text contains Arabic characters
     *
     * @param string $text
     * @return bool
     */
    private function hasArabicText($text)
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Create ticket image with overlays based on template with proper scaling
     *
     * @param RegistrationUser $user
     * @param TicketTemplate $template
     * @return string Path to the ticket image
     */
    private function createTicketImage(RegistrationUser $user, TicketTemplate $template)
    {
        // Load background template
        $backgroundPath = storage_path('app/public/' . $template->background_image_path);

        if (!file_exists($backgroundPath)) {
            throw new \Exception('Template background image not found: ' . $backgroundPath);
        }

        $image = Image::make($backgroundPath);

        // Get actual image dimensions
        $imageWidth = $image->width();
        $imageHeight = $image->height();

        // Calculate scaling factor if template has preview dimensions
        $scaleX = 1;
        $scaleY = 1;

        if (isset($template->preview_width) && $template->preview_width > 0 &&
            isset($template->preview_height) && $template->preview_height > 0) {
            $scaleX = $imageWidth / $template->preview_width;
            $scaleY = $imageHeight / $template->preview_height;
        }

        // Add user name with scaling and Arabic support
        if (isset($template->name_position_x) && isset($template->name_position_y)) {
            $nameX = (int) ($template->name_position_x * $scaleX);
            $nameY = (int) ($template->name_position_y * $scaleY);
            $fontSize = (int) (($template->name_font_size ?? 24) * $scaleX);

            $fullName = $user->first_name . ' ' . $user->last_name;
            $isArabic = $this->hasArabicText($fullName);

            if ($isArabic) {
                // Use I18N_Arabic for proper Arabic text rendering
                $this->renderArabicText(
                    $image,
                    $fullName,
                    $nameX,
                    $nameY,
                    $fontSize,
                    $template->name_font_color ?? '#000000'
                );
            } else {
                // Use standard text rendering for non-Arabic text
                $image->text(
                    $fullName,
                    $nameX,
                    $nameY,
                    function($font) use ($fontSize, $template) {
                        $defaultFontPath = public_path('fonts/ARIAL.ttf');
                        if (file_exists($defaultFontPath)) {
                            $font->file($defaultFontPath);
                        }
                        $font->size($fontSize);
                        $font->color($template->name_font_color ?? '#000000');
                        $font->align('left');
                        $font->valign('top');
                    }
                );
            }
        }

        // Add unique code with scaling and Arabic support
        if (isset($template->code_position_x) && isset($template->code_position_y)) {
            $codeX = (int) ($template->code_position_x * $scaleX);
            $codeY = (int) ($template->code_position_y * $scaleY);
            $fontSize = (int) (($template->code_font_size ?? 20) * $scaleX);

            $codeText = $user->unique_code;
            $isArabic = $this->hasArabicText($codeText);

            if ($isArabic) {
                // Use I18N_Arabic for proper Arabic text rendering
                $this->renderArabicText(
                    $image,
                    $codeText,
                    $codeX,
                    $codeY,
                    $fontSize,
                    $template->code_font_color ?? '#000000'
                );
            } else {
                // Use standard text rendering for non-Arabic text
                $image->text(
                    $codeText,
                    $codeX,
                    $codeY,
                    function($font) use ($fontSize, $template) {
                        $defaultFontPath = public_path('fonts/ARIAL.ttf');
                        if (file_exists($defaultFontPath)) {
                            $font->file($defaultFontPath);
                        }
                        $font->size($fontSize);
                        $font->color($template->code_font_color ?? '#000000');
                        $font->align('left');
                        $font->valign('top');
                    }
                );
            }
        }

        // Add QR code with scaling and rounded corners
        if(!isset($user->qr_code_path)){
            $qrCodePath = $this->generateQRCode($user, $user->unique_code);
            $user->qr_code_path = $qrCodePath;
            $user->save();
        }
        
        if (isset($template->qr_position_x) && isset($template->qr_position_y) && $user->qr_code_path) {
            $qrX = (int) ($template->qr_position_x * $scaleX);
            $qrY = (int) ($template->qr_position_y * $scaleY);
            $qrSize = (int) (($template->qr_size ?? 100) * $scaleX);

            $qrCodePath = storage_path('app/public/' . $user->qr_code_path);
            if (file_exists($qrCodePath)) {
                $qrImage = Image::make($qrCodePath);
                $qrImage->resize($qrSize, $qrSize);

                // Add rounded corners to QR code
                $cornerRadius = min($qrSize * 0.1, 10); // 10% of size or max 10px
                $qrImage = $this->addRoundedCorners($qrImage, $cornerRadius);

                $image->insert(
                    $qrImage,
                    'top-left',
                    $qrX,
                    $qrY
                );
            }
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
     * Render Arabic text on image using I18N_Arabic library
     *
     * @param \Intervention\Image\Image $image
     * @param string $text
     * @param int $x
     * @param int $y
     * @param int $fontSize
     * @param string $color
     * @return void
     */
    private function renderArabicText($image, $text, $x, $y, $fontSize, $color)
    {
        // Check if I18N_Arabic is available
        $arabicLibPath = base_path('vendor/khaled.alshamaa/ar-php/src/Arabic.php');

        if (!file_exists($arabicLibPath)) {
            // Fallback to standard text rendering if library not available
            $image->text(
                $text,
                $x,
                $y,
                function($font) use ($fontSize, $color) {
                    $arabicFontPath = public_path('fonts/NotoNaskhArabic-Regular.ttf');
                    if (file_exists($arabicFontPath)) {
                        $font->file($arabicFontPath);
                    }
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align('right');
                    $font->valign('top');
                }
            );
            return;
        }

        // Include the Arabic library
        require_once $arabicLibPath;

        // Initialize Arabic object
        $Arabic = new \ArPHP\I18N\Arabic();

        // Process text for proper glyph rendering
        $text = $Arabic->utf8Glyphs($text);

        // Convert hex color to RGB
        $hexColor = ltrim($color, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 6, 2));

        // Get image resource
        $imageResource = $image->getCore();

        // Create color resource
        $colorResource = imagecolorallocate($imageResource, $r, $g, $b);

        // Font path
        $fontPath = public_path('fonts/NotoNaskhArabic-Regular.ttf');

        // Calculate text width for RTL positioning
        $box = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($box[2] - $box[0]);

        // Adjust X position for RTL text
        $adjustedX = $x;

        // Render text
        imagettftext(
            $imageResource,
            $fontSize,
            0,
            $adjustedX,
            $y + $fontSize,
            $colorResource,
            $fontPath,
            $text
        );

        // Update the image with the modified resource
        $image->setCore($imageResource);
    }

    /**
     * Generate QR code token for secure ticket access
     *
     * @param RegistrationUser $user
     * @return string
     */
    public function generateQRToken(RegistrationUser $user)
    {
        // Create a secure token that includes user info and timestamp
        $tokenData = [
            'user_id' => $user->id,
            'unique_code' => $user->unique_code,
            'event_id' => $user->registration->event_id,
            'timestamp' => now()->timestamp
        ];

        // Create a hash of the data for security
        $token = hash('sha256', json_encode($tokenData) . config('app.key'));

        return $token;
    }

    /**
     * Validate QR token and return user
     *
     * @param string $token
     * @param int $userId
     * @return RegistrationUser|null
     */
    public function validateQRToken(string $token, int $userId)
    {
        $user = RegistrationUser::find($userId);

        if (!$user || $user->status !== 'approved') {
            return null;
        }

        // Recreate the token to validate
        $tokenData = [
            'user_id' => $user->id,
            'unique_code' => $user->unique_code,
            'event_id' => $user->registration->event_id,
            'timestamp' => now()->timestamp
        ];

        $expectedToken = hash('sha256', json_encode($tokenData) . config('app.key'));

        // For security, we'll allow tokens generated within the last 24 hours
        $validTimeRange = 24 * 60 * 60; // 24 hours in seconds

        // Check multiple timestamps within the valid range
        for ($i = 0; $i <= $validTimeRange; $i += 3600) { // Check every hour
            $testTimestamp = now()->timestamp - $i;
            $testTokenData = [
                'user_id' => $user->id,
                'unique_code' => $user->unique_code,
                'event_id' => $user->registration->event_id,
                'timestamp' => $testTimestamp
            ];

            $testToken = hash('sha256', json_encode($testTokenData) . config('app.key'));

            if (hash_equals($testToken, $token)) {
                return $user;
            }
        }

        return null;
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

    /**
     * Add rounded corners to an image
     *
     * @param \Intervention\Image\Image $image
     * @param int $radius
     * @return \Intervention\Image\Image
     */
    private function addRoundedCorners($image, $radius)
    {
        $width = $image->width();
        $height = $image->height();

        // Create a mask with rounded corners
        $mask = Image::canvas($width, $height, '#000000');

        // Draw rounded rectangle on mask
        $mask->circle($radius * 2, $radius, $radius, function ($draw) {
            $draw->background('#ffffff');
        });

        $mask->circle($radius * 2, $width - $radius, $radius, function ($draw) {
            $draw->background('#ffffff');
        });

        $mask->circle($radius * 2, $radius, $height - $radius, function ($draw) {
            $draw->background('#ffffff');
        });

        $mask->circle($radius * 2, $width - $radius, $height - $radius, function ($draw) {
            $draw->background('#ffffff');
        });

        // Fill the center rectangle
        $mask->rectangle($radius, 0, $width - $radius, $height, function ($draw) {
            $draw->background('#ffffff');
        });

        $mask->rectangle(0, $radius, $width, $height - $radius, function ($draw) {
            $draw->background('#ffffff');
        });

        // Apply mask to create rounded corners
        $image->mask($mask, false);

        return $image;
    }
}
