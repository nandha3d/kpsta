<?php

namespace App\Services\Api;

class WhatsAppService implements WhatsAppProviderInterface
{
    protected string $provider;
    protected string $apiUrl;
    protected string $authKey;
    protected string $integratedNumber;
    protected string $fallbackNumber;
    protected string $templateName;

    public function __construct()
    {
        $this->provider = getenv('WHATSAPP_PROVIDER') ?: 'msg91';
        $this->apiUrl = getenv('WHATSAPP_API_URL') ?: 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';
        $this->authKey = getenv('WHATSAPP_AUTH_KEY') ?: '463379AbmG58Zt6892f626P1';
        // Primary integrated number 15554929613 (Animazon)
        $this->integratedNumber = getenv('WHATSAPP_INTEGRATED_NUMBER') ?: '15554929613';
        $this->fallbackNumber = getenv('WHATSAPP_FALLBACK_NUMBER') ?: '15554929613';
        $this->templateName = getenv('WHATSAPP_TEMPLATE_NAME') ?: 'kpsta';
    }

    /**
     * Send OTP to the specified WhatsApp phone number via MSG91 WhatsApp Outbound API.
     *
     * @param string $phone Phone number (e.g. 919876543210 or 9876543210)
     * @param string $otp 6-digit OTP string
     * @return bool True if successfully dispatched, false otherwise
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        // Allow mock logging in tests if explicitly configured
        if ($this->provider === 'log') {
            log_message('info', "[WhatsAppService] (MOCK LOG) Dispatched OTP to {$phone}: {$otp}");
            return true;
        }

        // Normalize phone number (pure digits, ensuring country code e.g. 91)
        $cleanPhone = preg_replace('/\D+/', '', $phone);
        if (strlen($cleanPhone) === 10 && in_array($cleanPhone[0], ['6', '7', '8', '9'])) {
            $cleanPhone = '91' . $cleanPhone;
        }

        // Try primary integrated number, fallback if needed
        $numbersToTry = array_unique(array_filter([$this->integratedNumber, $this->fallbackNumber]));
        foreach ($numbersToTry as $senderNumber) {
            $success = $this->dispatchMsg91($cleanPhone, $otp, $senderNumber);
            if ($success) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dispatch single WhatsApp template request via MSG91.
     */
    protected function dispatchMsg91(string $phone, string $otp, string $senderNumber): bool
    {
        $payload = [
            'integrated_number' => $senderNumber,
            'content_type'      => 'template',
            'payload'           => [
                'messaging_product' => 'whatsapp',
                'type'              => 'template',
                'template'          => [
                    'name'     => $this->templateName,
                    'language' => [
                        'code'   => 'en',
                        'policy' => 'deterministic',
                    ],
                    'namespace' => null,
                    'to_and_components' => [
                        [
                            'to' => [$phone],
                            'components' => [
                                'body_1' => [
                                    'type'  => 'text',
                                    'value' => $otp,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'authkey'      => $this->authKey,
                ],
                'body'        => json_encode($payload),
                'http_errors' => false,
                'timeout'     => 15,
                'verify'      => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string)$response->getBody();
            $data = json_decode($body, true);

            if ($statusCode >= 200 && $statusCode < 300 && ($data['status'] ?? '') === 'success') {
                $requestId = $data['request_id'] ?? 'N/A';
                log_message('info', "[WhatsAppService] MSG91 OTP sent successfully to {$phone} via {$senderNumber}. Request ID: {$requestId}");
                return true;
            }

            log_message('warning', "[WhatsAppService] MSG91 send attempt failed for {$phone} via {$senderNumber}. Status: {$statusCode}, Body: {$body}");
            return false;
        } catch (\Throwable $e) {
            log_message('error', "[WhatsAppService] Exception sending MSG91 OTP to {$phone} via {$senderNumber}: " . $e->getMessage());
            return false;
        }
    }
}
