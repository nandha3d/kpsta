<?php

namespace App\Services\Api;

interface WhatsAppProviderInterface
{
    /**
     * Send OTP to the specified normalized phone number.
     *
     * @param string $phone E.164 formatted or normalized digits (e.g. 919876543210)
     * @param string $otp 6-digit OTP string
     * @return bool True if successfully dispatched, false otherwise
     */
    public function sendOtp(string $phone, string $otp): bool;
}
