<?php

namespace App\Services\Api;

class WhatsAppService implements WhatsAppProviderInterface
{
    protected string $provider;
    protected string $apiUrl;
    protected string $apiToken;
    protected string $templateName;

    public function __construct()
    {
        $this->provider = getenv('WHATSAPP_PROVIDER') ?: 'log';
        $this->apiUrl = getenv('WHATSAPP_API_URL') ?: '';
        $this->apiToken = getenv('WHATSAPP_API_TOKEN') ?: '';
        $this->templateName = getenv('WHATSAPP_TEMPLATE_NAME') ?: 'kpsta_otp_verification';
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        // In development or when provider is 'log', record safely to log
        if ($this->provider === 'log' || empty($this->apiUrl) || empty($this->apiToken)) {
            log_message('info', "[WhatsAppService] (DEV/MOCK) Dispatched OTP to {$phone}: {$otp}");
            return true;
        }

        // Live HTTP provider dispatch
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'template',
                    'template'          => [
                        'name' => $this->templateName,
                        'language' => ['code' => 'en'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $otp],
                                ],
                            ],
                            [
                                'type' => 'button',
                                'sub_type' => 'url',
                                'index' => '0',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $otp],
                                ],
                            ],
                        ],
                    ],
                ],
                'http_errors' => false,
                'timeout'     => 10,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                log_message('info', "[WhatsAppService] Live OTP sent successfully to {$phone}.");
                return true;
            }

            log_message('error', "[WhatsAppService] Live OTP send failed for {$phone}. Status: {$statusCode}, Body: " . $response->getBody());
            return false;
        } catch (\Throwable $e) {
            log_message('error', "[WhatsAppService] Exception sending OTP to {$phone}: " . $e->getMessage());
            return false;
        }
    }
}
