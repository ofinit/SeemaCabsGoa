<?php

namespace App\Services;

use App\Enums\Type;
use App\Models\Environment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CashfreeService
{
    protected ?string $appId;
    protected ?string $secretKey;
    protected string $mode;
    protected ?string $webhookSecret;
    // 2025-01-01 is additive over 2023-08-01 for every field this app uses
    // (order create/get, refunds, order_splits, easy-split split-after-payment).
    protected string $apiVersion = '2025-01-01';

    public function __construct()
    {
        $settings = Environment::whereIn('title', [
            Type::CASHFREE_APP_ID,
            Type::CASHFREE_SECRET_KEY,
            Type::CASHFREE_MODE,
            Type::CASHFREE_WEBHOOK_SECRET,
            Type::CASHFREE_ENABLED,
        ])->pluck('value', 'title');

        $this->appId = $settings[Type::CASHFREE_APP_ID] ?? null;
        $this->secretKey = $settings[Type::CASHFREE_SECRET_KEY] ?? null;
        $this->mode = strtolower($settings[Type::CASHFREE_MODE] ?? 'sandbox');
        $this->webhookSecret = $settings[Type::CASHFREE_WEBHOOK_SECRET] ?? $this->secretKey;
    }

    public function getBaseUrl(): string
    {
        return $this->mode === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->secretKey);
    }

    public function isEnabled(): bool
    {
        $enabled = Environment::where('title', Type::CASHFREE_ENABLED)->value('value');
        return ($enabled === '1' || $enabled === 1 || $enabled === true) && $this->isConfigured();
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function getMode(): string
    {
        return $this->mode === 'production' ? 'production' : 'sandbox';
    }

    protected function getHeaders(): array
    {
        return [
            'x-client-id' => $this->appId,
            'x-client-secret' => $this->secretKey,
            'x-api-version' => $this->apiVersion,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create Cashfree PG Order with optional Easy Split configuration.
     */
    public function createOrder(
        string $orderId,
        float $amount,
        array $customerDetails,
        ?array $orderSplits = null,
        ?string $returnUrl = null,
        ?string $notifyUrl = null
    ): array {
        if (!$this->isConfigured()) {
            throw new \Exception('Cashfree is not configured. Missing App ID or Secret Key.');
        }

        // Format clean 10-digit phone
        $phone = preg_replace('/[^0-9]/', '', $customerDetails['phone'] ?? '');
        if (strlen($phone) > 10) {
            $phone = substr($phone, -10);
        }
        if (empty($phone)) {
            $phone = '9999999999';
        }

        $payload = [
            'order_id' => $orderId,
            'order_amount' => round($amount, 2),
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => (string) ($customerDetails['id'] ?? 'cust_' . substr(md5($phone), 0, 10)),
                'customer_name' => $customerDetails['name'] ?? 'Guest Customer',
                'customer_email' => $customerDetails['email'] ?? 'customer@seemacabsgoa.com',
                'customer_phone' => $phone,
            ],
            'order_meta' => [
                'return_url' => $returnUrl ?? url('/app/actions/cashfree/return?order_id={order_id}'),
                'notify_url' => $notifyUrl ?? url('/api/cashfree/webhook'),
            ],
        ];

        // Attach Easy Split if vendor split is configured
        if (!empty($orderSplits)) {
            $payload['order_splits'] = $orderSplits;
        }

        $response = Http::withHeaders($this->getHeaders())
            ->timeout(20)
            ->post($this->getBaseUrl() . '/orders', $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? $response->body();
            Log::error('Cashfree Create Order Failed: ' . $errorMessage, ['payload' => $payload, 'response' => $errorBody]);
            throw new \Exception('Cashfree Error: ' . $errorMessage);
        }

        return $response->json();
    }

    /**
     * Fetch Order Status from Cashfree
     */
    public function getOrder(string $orderId): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->timeout(15)
            ->get($this->getBaseUrl() . '/orders/' . $orderId);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Cashfree order: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Fetch Payments for an Order
     */
    public function getOrderPayments(string $orderId): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->timeout(15)
            ->get($this->getBaseUrl() . '/orders/' . $orderId . '/payments');

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Cashfree order payments: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Create Refund via Cashfree
     */
    public function createRefund(string $orderId, string $refundId, float $amount, string $note = 'Booking refund'): array
    {
        $payload = [
            'refund_id' => $refundId,
            'refund_amount' => round($amount, 2),
            'refund_note' => $note,
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->timeout(20)
            ->post($this->getBaseUrl() . '/orders/' . $orderId . '/refunds', $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? $response->body();
            Log::error('Cashfree Refund Failed: ' . $errorMessage, ['payload' => $payload, 'response' => $errorBody]);
            throw new \Exception('Cashfree Refund Error: ' . $errorMessage);
        }

        return $response->json();
    }

    /**
     * Create post-order split if split was not included at order creation
     */
    public function createSplit(string $orderId, array $splits): array
    {
        // Split After Payment (Easy Split). disable_split=true closes the order for further splits.
        $payload = [
            'split' => $splits,
            'disable_split' => true,
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->timeout(20)
            ->post($this->getBaseUrl() . '/easy-split/orders/' . $orderId . '/split', $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? $response->body();
            Log::error('Cashfree Split Failed: ' . $errorMessage, ['payload' => $payload, 'response' => $errorBody]);
            throw new \Exception('Cashfree Split Error: ' . $errorMessage);
        }

        return $response->json();
    }

    /**
     * Verify Webhook Signature (HMAC SHA-256)
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signature, ?string $timestamp): bool
    {
        if (empty($signature) || empty($timestamp)) {
            return false;
        }

        $secret = !empty($this->webhookSecret) ? $this->webhookSecret : $this->secretKey;
        if (empty($secret)) {
            return false;
        }

        $dataToSign = $timestamp . $rawBody;
        $expectedSignature = base64_encode(hash_hmac('sha256', $dataToSign, $secret, true));

        return hash_equals($expectedSignature, $signature);
    }
}
