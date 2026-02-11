<?php

namespace Services\PaymentGateway;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * HyperPay Payment Gateway Integration
 * 
 * HyperPay is a payment gateway popular in the Middle East and North Africa region.
 * This integration uses HyperPay's REST API for payment processing.
 */
class HyperPay
{
    const GATEWAY_NAME = 'HyperPay';

    private $transaction_data;
    private $gateway;
    private $config;
    private $options = [];

    // HyperPay API endpoints
    private $test_url = 'https://eu-test.oppwa.com/v1';
    private $live_url = 'https://eu-prod.oppwa.com/v1';

    private $base_url_used;

    public function __construct($gateway, $config = [])
    {
        $this->gateway = $gateway;
        $this->config = $config;
    }

    public function getBaseUrl()
    {
        return $this->base_url_used ?? '';
    }

    /**
     * Create transaction data for HyperPay checkout
     */
    private function createTransactionData($order_total, $order_email, $event, $returnUrl = null)
    {
        $base_url = !empty($this->config['testMode']) ? $this->test_url : $this->live_url;
        $this->base_url_used = $base_url;

        if (!$returnUrl) {
            $returnUrl = route('showEventCheckoutPaymentReturn', [
                'event_id' => $event->id,
                'is_payment_successful' => 1,
            ]);
        }

        // Use entityId from config (default for Visa/Mastercard)
        // If MADA entityId exists, it can be used when payment method is MADA
        $entityId = $this->config['entityId'] ?? '';

        // Log which entityId is being used
        Log::info('HyperPay Entity ID Selection', [
            'entityId' => $entityId,
            'entityIdMada' => $this->config['entityIdMada'] ?? null,
            'using_default' => !empty($entityId)
        ]);

        $amount = number_format($order_total, 2, '.', '');
        $currency = $event->currency->code;

        // Generate unique transaction ID
        $merchantTransactionId = 'ATTENDIZE_' . time() . '_' . uniqid();

        $this->transaction_data = [
            'entityId' => $entityId,
            'amount' => $amount,
            'currency' => $currency,
            'paymentType' => 'DB', // Debit (can be DB for Debit, PA for Pre-authorization)
            'integrity' => 'true',
            'merchantTransactionId' => $merchantTransactionId,
            'billing.street1' => '',
            'billing.city' => '',
            'billing.state' => '',
            'billing.postcode' => '',
            'billing.country' => $event->currency->code === 'SAR' ? 'SA' : ($event->currency->code === 'AED' ? 'AE' : 'US'),
            'customer.email' => $order_email,
            'customer.givenName' => '',
            'customer.surname' => '',
            'testMode' => !empty($this->config['testMode']) ? 'EXTERNAL' : '',
            'createRegistration' => false,
            'shopperResultUrl' => $returnUrl,
        ];

        return $this->transaction_data;
    }

    /**
     * Start the payment transaction
     */
    public function startTransaction($order_total, $order_email, $event, $returnUrl = null)
    {
        $this->createTransactionData($order_total, $order_email, $event, $returnUrl);

        $base_url = $this->base_url_used ?? (!empty($this->config['testMode']) ? $this->test_url : $this->live_url);
        $entityId = $this->config['entityId'] ?? '';
        $accessToken = $this->config['accessToken'] ?? '';

        try {
            Log::info('HyperPay Request', [
                'base_url' => $base_url,
                'entityId' => $entityId,
                'entityId_length' => strlen($entityId ?? ''),
                'has_access_token' => !empty($accessToken),
                'access_token_length' => strlen($accessToken ?? ''),
                'access_token_start' => substr($accessToken ?? '', 0, 10) . '...',
                'testMode' => !empty($this->config['testMode']),
                'config_keys' => array_keys($this->config ?? [])
            ]);

            // Ensure base_url ends with /
            $base_url = rtrim($base_url, '/') . '/';

            $client = new Client([
                'base_uri' => $base_url,
                'timeout' => 30,
                'verify' => true,
            ]);

            // Prepare form data
            $formData = [];
            foreach ($this->transaction_data as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $formData[$key] = $value;
                }
            }

            Log::info('HyperPay Form Data (without sensitive info)', [
                'entityId' => $entityId,
                'amount' => $formData['amount'] ?? null,
                'currency' => $formData['currency'] ?? null,
                'paymentType' => $formData['paymentType'] ?? null,
                'has_access_token' => !empty($accessToken),
                'base_url' => $base_url,
                'full_url' => $base_url . 'checkouts'
            ]);

            // Make request to HyperPay checkout API
            $response = $client->post('checkouts', [
                'form_params' => $formData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('HyperPay Response', [
                'status_code' => $response->getStatusCode(),
                'response_body' => $responseBody
            ]);

            // Create a mock response object
            return new HyperPayResponse($responseBody, $base_url);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = '';
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;

            if ($e->getResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
            }

            Log::error('HyperPay Client Error', [
                'status_code' => $statusCode,
                'message' => $e->getMessage(),
                'response' => $responseBody
            ]);

            $errorData = json_decode($responseBody, true);
            if ($errorData && isset($errorData['result'])) {
                return new HyperPayResponse($errorData, $base_url, true);
            }

            return new HyperPayResponse([
                'result' => [
                    'code' => 'ERROR',
                    'description' => 'HTTP ' . $statusCode . ': ' . ($errorData['result']['description'] ?? $e->getMessage())
                ],
                'id' => null
            ], $base_url, true);

        } catch (\Exception $e) {
            Log::error('HyperPay General Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return new HyperPayResponse([
                'result' => ['code' => 'ERROR', 'description' => $e->getMessage()],
                'id' => null
            ], $base_url, true);
        }
    }

    /**
     * Get transaction data
     */
    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    /**
     * Extract request parameters from HyperPay redirect
     */
    public function extractRequestParameters($request)
    {
        // HyperPay redirects back with 'id' (checkout ID) and optionally 'resourcePath'
        if ($request->has('id')) {
            $this->options['checkout_id'] = $request->get('id');
        }
        if ($request->has('resourcePath')) {
            $this->options['resourcePath'] = $request->get('resourcePath');
        }
    }

    /**
     * Complete the transaction after redirect
     */
    public function completeTransaction($data)
    {
        $base_url = !empty($this->config['testMode']) ? $this->test_url : $this->live_url;
        $accessToken = $this->config['accessToken'] ?? '';

        try {
            // Get checkout ID from options (set by extractRequestParameters) or data
            // HyperPay redirects back with 'id' parameter in the URL
            $checkoutId = $this->options['checkout_id'] ?? $data['id'] ?? null;
            $resourcePath = $this->options['resourcePath'] ?? $data['resourcePath'] ?? null;

            if (!$checkoutId && !$resourcePath) {
                return new HyperPayResponse([
                    'result' => ['code' => 'ERROR', 'description' => 'Missing checkout ID'],
                    'id' => null
                ], $base_url, false);
            }

            $client = new Client([
                'base_uri' => $base_url,
                'timeout' => 30,
                'verify' => true,
            ]);

            // Use resourcePath if available, otherwise construct from checkout ID
            $endpoint = $resourcePath ?? '/checkouts/' . $checkoutId . '/payment';

            // Remove base URL from resourcePath if present
            $endpoint = str_replace($base_url, '', $endpoint);
            $endpoint = ltrim($endpoint, '/');

            $response = $client->get($endpoint, [
                'query' => [
                    'entityId' => $this->config['entityId'] ?? '',
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            return new HyperPayResponse($responseBody, $base_url);

        } catch (\Exception $e) {
            Log::error('HyperPay Completion Error: ' . $e->getMessage());
            return new HyperPayResponse([
                'result' => ['code' => 'ERROR', 'description' => $e->getMessage()],
                'id' => null
            ], $base_url, false);
        }
    }

    /**
     * Get additional data from response
     */
    public function getAdditionalData($response)
    {
        return [
            'id' => $response->getCheckoutId(),
            'resourcePath' => $response->getResourcePath(),
        ];
    }

    /**
     * Whether to store additional data
     */
    public function storeAdditionalData()
    {
        return true;
    }

    /**
     * Refund a transaction
     */
    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {
        $base_url = !empty($this->config['testMode']) ? $this->test_url : $this->live_url;
        $accessToken = $this->config['accessToken'] ?? '';
        $entityId = $this->config['entityId'] ?? '';

        try {
            $client = new Client([
                'base_uri' => $base_url,
                'timeout' => 30,
                'verify' => true,
            ]);

            $amount = number_format($refund_amount, 2, '.', '');
            $paymentId = $order->transaction_id;

            $response = $client->post('/payments/' . $paymentId, [
                'form_params' => [
                    'entityId' => $entityId,
                    'amount' => $amount,
                    'paymentType' => 'RF', // Refund
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (
                isset($responseBody['result']['code']) &&
                (strpos($responseBody['result']['code'], '000') === 0 ||
                    strpos($responseBody['result']['code'], '001') === 0)
            ) {
                $refundResponse['successful'] = true;
            } else {
                $refundResponse['successful'] = false;
                $refundResponse['error_message'] = $responseBody['result']['description'] ?? 'Refund failed';
            }

        } catch (\Exception $e) {
            Log::error('HyperPay Refund Error: ' . $e->getMessage());
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $e->getMessage();
        }

        return $refundResponse;
    }
}

/**
 * HyperPay Response Wrapper
 * Mimics Omnipay response interface
 */
class HyperPayResponse
{
    private $data;
    private $base_url;
    private $isError;

    public function __construct($data, $base_url, $isError = false)
    {
        $this->data = $data;
        $this->base_url = $base_url;
        $this->isError = $isError;
    }

    public function isSuccessful()
    {
        if ($this->isError) {
            return false;
        }

        // HyperPay success codes typically start with 000 or 001
        $code = $this->data['result']['code'] ?? '';
        return (strpos($code, '000') === 0 || strpos($code, '001') === 0);
    }

    public function isRedirect()
    {
        // HyperPay uses redirect for hosted payment page
        return isset($this->data['id']) && !empty($this->data['id']);
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            // HyperPay uses JavaScript widget, so we redirect back to payment page with checkout ID
            // The widget will be loaded on that page
            $checkoutId = $this->data['id'] ?? '';
            // Store checkout ID in session temporarily so we can use it on the payment page
            if (request()->route('event_id')) {
                session()->put('hyperpay_checkout_id_' . request()->route('event_id'), $checkoutId);
                return route('showEventPayment', ['event_id' => request()->route('event_id')]) . '?hyperpay_checkout=' . $checkoutId;
            }
        }
        return null;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return [];
    }

    public function getTransactionReference()
    {
        // Return payment ID or checkout ID
        return $this->data['id'] ?? $this->data['ndc'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['result']['description'] ?? ($this->isError ? 'Payment failed' : 'Payment successful');
    }

    public function getCheckoutId()
    {
        return $this->data['id'] ?? null;
    }

    public function getResourcePath()
    {
        return $this->data['resourcePath'] ?? null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getIntegrity()
    {
        return $this->data['integrity'] ?? null;
    }
}

