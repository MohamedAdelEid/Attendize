<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsAppService
{
    protected $accountSid;
    protected $authToken;
    protected $from;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid');
        $this->authToken = config('services.twilio.auth_token');
        $this->from = config('services.twilio.whatsapp_from');
    }

    /**
     * Check if WhatsApp is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->accountSid) && !empty($this->authToken) && !empty($this->from);
    }

    /**
     * Normalize phone to E.164 for WhatsApp (e.g. 966501234567).
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        $phone = preg_replace('/[^\d]/', '', $phone);
        if (strlen($phone) === 0) {
            return null;
        }
        // If starts with 0 (e.g. 05xxxxxxxx), assume Saudi and replace with 966
        if (substr($phone, 0, 1) === '0') {
            $phone = '966' . substr($phone, 1);
        }
        // If 9 digits and no country code, assume Saudi
        if (strlen($phone) === 9 && in_array(substr($phone, 0, 1), ['5', '4', '3'])) {
            $phone = '966' . $phone;
        }
        return $phone;
    }

    /**
     * Send a WhatsApp message via Twilio API.
     *
     * @param string $to   E.164 number (e.g. 966501234567)
     * @param string $body Message text
     * @return array { 'success' => bool, 'message' => string, 'sid' => string|null }
     */
    public function send(string $to, string $body): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WhatsApp is not configured. Please set TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, and TWILIO_WHATSAPP_FROM in .env',
                'sid' => null,
            ];
        }

        $to = self::normalizePhone($to);
        if (empty($to)) {
            return [
                'success' => false,
                'message' => 'Invalid or empty phone number.',
                'sid' => null,
            ];
        }

        $toWhatsApp = 'whatsapp:+' . $to;
        $fromWhatsApp = $this->from;
        if (stripos($fromWhatsApp, 'whatsapp:') !== 0) {
            $fromWhatsApp = 'whatsapp:' . (strpos($fromWhatsApp, '+') === 0 ? $fromWhatsApp : '+' . $fromWhatsApp);
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->accountSid . '/Messages.json';

        try {
            $client = new Client(['base_uri' => 'https://api.twilio.com']);
            $response = $client->post('2010-04-01/Accounts/' . $this->accountSid . '/Messages.json', [
                'auth' => [$this->accountSid, $this->authToken],
                'form_params' => [
                    'From' => $fromWhatsApp,
                    'To' => $toWhatsApp,
                    'Body' => $body,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode((string) $response->getBody(), true);

            if ($statusCode >= 200 && $statusCode < 300 && isset($data['sid'])) {
                return [
                    'success' => true,
                    'message' => 'Message sent.',
                    'sid' => $data['sid'],
                ];
            }

            $errorMessage = $data['message'] ?? $data['error_message'] ?? 'Unknown error';
            Log::warning('WhatsApp send failed', ['response' => $data, 'to' => $to]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'sid' => null,
            ];
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : '';
            $data = json_decode($body, true);
            $errorMessage = is_array($data) ? ($data['message'] ?? $data['error_message'] ?? $e->getMessage()) : $e->getMessage();
            Log::warning('WhatsApp send failed', ['response' => $body, 'to' => $to]);
            return [
                'success' => false,
                'message' => $errorMessage,
                'sid' => null,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', ['exception' => $e->getMessage(), 'to' => $to]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'sid' => null,
            ];
        }
    }

    /**
     * Replace placeholders in a message with user/event data.
     * Placeholders: @first_name, @last_name, @email, @phone, @unique_code, @event_title, @registration_name, @user_type
     */
    public static function replacePlaceholders(string $message, $user, $event = null): string
    {
        if ($event === null && $user->relationLoaded('registration')) {
            $event = $user->registration->event ?? null;
        }
        if ($event === null) {
            $reg = $user->registration ?? $user->registration()->with('event')->first();
            $event = $reg && $reg->relationLoaded('event') ? $reg->event : ($reg ? $reg->event : null);
        }
        $registration = $user->registration ?? $user->registration()->first();

        $map = [
            '@first_name' => $user->first_name ?? '',
            '@last_name' => $user->last_name ?? '',
            '@email' => $user->email ?? '',
            '@phone' => $user->phone ?? '',
            '@unique_code' => $user->unique_code ?? '',
            '@event_title' => $event ? $event->title : '',
            '@registration_name' => $registration ? $registration->name : '',
            '@user_type' => $user->userType ? $user->userType->name : '',
        ];

        foreach ($map as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }

        return $message;
    }
}
