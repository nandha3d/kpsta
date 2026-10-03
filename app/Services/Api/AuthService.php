<?php

namespace App\Services\Api;

use Config\Database;

class AuthService
{
    protected $db;
    protected WhatsAppProviderInterface $whatsApp;

    public const OTP_TTL_SECONDS = 300; // 5 minutes
    public const OTP_RESEND_SECONDS = 60; // 1 minute
    public const OTP_MAX_ATTEMPTS = 5;
    public const ACCESS_TOKEN_TTL_SECONDS = 86400; // 24 hours
    public const REFRESH_TOKEN_TTL_SECONDS = 2592000; // 30 days

    public function __construct(?WhatsAppProviderInterface $whatsApp = null)
    {
        $this->db = Database::connect();
        $this->whatsApp = $whatsApp ?? new WhatsAppService();
    }

    /**
     * Normalize Indian / International mobile numbers to pure digits.
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen($digits) === 10 && in_array($digits[0], ['6', '7', '8', '9'])) {
            $digits = '91' . $digits;
        }
        return $digits;
    }

    /**
     * Request a WhatsApp OTP.
     *
     * @return array [success => bool, message => string, data => array|null, code => string|null]
     */
    public function requestOtp(string $phone, ?string $ip = null): array
    {
        $normalized = $this->normalizePhone($phone);
        if (strlen($normalized) < 10 || strlen($normalized) > 15) {
            return [
                'success' => false,
                'message' => 'Invalid mobile number format. Please provide a valid 10-digit number with country code.',
                'code'    => 'VALIDATION_ERROR',
                'data'    => null,
            ];
        }

        $now = date('Y-m-d H:i:s');

        // Check resend cooldown
        $builder = $this->db->table('api_otps');
        $recent = $builder->where('phone', $normalized)
            ->where('resend_available_at >', $now)
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRowArray();

        if ($recent) {
            $secondsLeft = max(1, strtotime($recent['resend_available_at']) - time());
            return [
                'success' => false,
                'message' => "Please wait {$secondsLeft} seconds before requesting a new OTP.",
                'code'    => 'OTP_RATE_LIMITED',
                'data'    => ['resend_after' => $secondsLeft],
            ];
        }

        // Generate 6-digit OTP
        // In local development with test phone 919876543210, fixed 123456 can be used for automated tests
        if (ENVIRONMENT === 'development' && $normalized === '919876543210') {
            $otp = '123456';
        } else {
            $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        $otpHash = hash('sha256', $otp);
        $expiresAt = date('Y-m-d H:i:s', time() + self::OTP_TTL_SECONDS);
        $resendAt = date('Y-m-d H:i:s', time() + self::OTP_RESEND_SECONDS);

        $this->db->table('api_otps')->insert([
            'phone'               => $normalized,
            'otp_hash'            => $otpHash,
            'attempts'            => 0,
            'expires_at'          => $expiresAt,
            'resend_available_at' => $resendAt,
            'ip_address'          => $ip,
            'created_at'          => $now,
        ]);

        // Send via provider
        $sent = $this->whatsApp->sendOtp($normalized, $otp);
        if (!$sent) {
            return [
                'success' => false,
                'message' => 'Failed to dispatch WhatsApp OTP. Please try again shortly.',
                'code'    => 'PROVIDER_ERROR',
                'data'    => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully to your WhatsApp number.',
            'code'    => null,
            'data'    => [
                'phone'        => $normalized,
                'expires_in'   => self::OTP_TTL_SECONDS,
                'resend_after' => self::OTP_RESEND_SECONDS,
            ],
        ];
    }

    /**
     * Verify WhatsApp OTP and issue tokens.
     */
    public function verifyOtp(string $phone, string $otp, ?string $deviceId = null, ?string $deviceType = 'mobile'): array
    {
        $normalized = $this->normalizePhone($phone);
        $now = date('Y-m-d H:i:s');

        // Find active OTP record
        $record = $this->db->table('api_otps')
            ->where('phone', $normalized)
            ->where('verified_at IS NULL')
            ->where('expires_at >=', $now)
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRowArray();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'OTP has expired or is invalid. Please request a new OTP.',
                'code'    => 'OTP_EXPIRED',
                'data'    => null,
            ];
        }

        if ((int)$record['attempts'] >= self::OTP_MAX_ATTEMPTS) {
            return [
                'success' => false,
                'message' => 'Maximum OTP attempts exceeded. Please request a new OTP.',
                'code'    => 'OTP_RATE_LIMITED',
                'data'    => null,
            ];
        }

        $inputHash = hash('sha256', $otp);
        if (!hash_equals($record['otp_hash'], $inputHash)) {
            $this->db->table('api_otps')
                ->where('id', $record['id'])
                ->update(['attempts' => (int)$record['attempts'] + 1]);

            $attemptsLeft = self::OTP_MAX_ATTEMPTS - ((int)$record['attempts'] + 1);
            return [
                'success' => false,
                'message' => "Invalid OTP entered. {$attemptsLeft} attempt(s) remaining.",
                'code'    => 'INVALID_OTP',
                'data'    => ['attempts_left' => $attemptsLeft],
            ];
        }

        // Mark OTP as verified
        $this->db->table('api_otps')
            ->where('id', $record['id'])
            ->update(['verified_at' => $now]);

        // Find user by phone in aauth_users
        $user = $this->db->table('aauth_users')
            ->where('phone', $normalized)
            ->get(1)
            ->getRowArray();

        // Fallback: check 10-digit format or username
        if (!$user && strlen($normalized) >= 10) {
            $tenDigit = substr($normalized, -10);
            $user = $this->db->table('aauth_users')
                ->where('phone', $tenDigit)
                ->orWhere('username', $tenDigit)
                ->orWhere('username', $normalized)
                ->get(1)
                ->getRowArray();
        }

        // Auto-provision verified WhatsApp mobile user if not already in aauth_users
        if (!$user) {
            $tenDigit = strlen($normalized) >= 10 ? substr($normalized, -10) : $normalized;
            $teacher = $this->db->table('teacher_details')
                ->where('mobile', $normalized)
                ->orWhere('mobile', $tenDigit)
                ->get(1)
                ->getRowArray();

            $name = $teacher['name'] ?? ('KPSTA Member ' . substr($normalized, -4));
            $email = 'member_' . $normalized . '@kpsta.in';

            $this->db->table('aauth_users')->insert([
                'phone'        => $normalized,
                'username'     => $normalized,
                'email'        => $email,
                'name'         => $name,
                'group_id'     => 2, // Member group
                'banned'       => 0,
                'date_created' => $now,
            ]);

            $user = $this->db->table('aauth_users')
                ->where('phone', $normalized)
                ->get(1)
                ->getRowArray();
        }

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Unable to initialize user account for this WhatsApp number. Please contact the administrator.',
                'code'    => 'ACCOUNT_NOT_FOUND',
                'data'    => null,
            ];
        }

        if (!empty($user['banned'])) {
            return [
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
                'code'    => 'ACCOUNT_DISABLED',
                'data'    => null,
            ];
        }

        // Issue tokens
        $tokens = $this->issueTokens((int)$user['id'], $deviceId, $deviceType);
        $profile = $this->getUserProfile((int)$user['id']);

        return [
            'success' => true,
            'message' => 'Authentication successful.',
            'code'    => null,
            'data'    => array_merge($tokens, [
                'user' => $profile,
            ]),
        ];
    }

    /**
     * Issue Access and Refresh tokens for a given user.
     */
    public function issueTokens(int $userId, ?string $deviceId = null, ?string $deviceType = 'mobile'): array
    {
        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(40));

        $now = date('Y-m-d H:i:s');
        $accessExpiresAt = date('Y-m-d H:i:s', time() + self::ACCESS_TOKEN_TTL_SECONDS);
        $refreshExpiresAt = date('Y-m-d H:i:s', time() + self::REFRESH_TOKEN_TTL_SECONDS);

        $this->db->table('api_tokens')->insert([
            'user_id'                    => $userId,
            'access_token'               => $accessToken,
            'refresh_token'              => $refreshToken,
            'device_id'                  => $deviceId,
            'device_type'                => $deviceType,
            'access_token_expires_at'    => $accessExpiresAt,
            'refresh_token_expires_at'   => $refreshExpiresAt,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => self::ACCESS_TOKEN_TTL_SECONDS,
        ];
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refreshToken(string $refreshToken): array
    {
        $now = date('Y-m-d H:i:s');
        $tokenRecord = $this->db->table('api_tokens')
            ->where('refresh_token', $refreshToken)
            ->where('revoked_at IS NULL')
            ->where('refresh_token_expires_at >=', $now)
            ->get(1)
            ->getRowArray();

        if (!$tokenRecord) {
            return [
                'success' => false,
                'message' => 'Invalid or expired refresh token. Please log in again.',
                'code'    => 'AUTHENTICATION_REQUIRED',
                'data'    => null,
            ];
        }

        // Revoke old token record and issue new pair (rotation)
        $this->db->table('api_tokens')
            ->where('id', $tokenRecord['id'])
            ->update(['revoked_at' => $now]);

        $tokens = $this->issueTokens((int)$tokenRecord['user_id'], $tokenRecord['device_id'], $tokenRecord['device_type']);
        $profile = $this->getUserProfile((int)$tokenRecord['user_id']);

        return [
            'success' => true,
            'message' => 'Token refreshed successfully.',
            'code'    => null,
            'data'    => array_merge($tokens, [
                'user' => $profile,
            ]),
        ];
    }

    /**
     * Invalidate an active access token on logout.
     */
    public function logout(string $accessToken): bool
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('api_tokens')
            ->where('access_token', $accessToken)
            ->update(['revoked_at' => $now]);

        return true;
    }

    /**
     * Validate an access token and return user identity if valid.
     */
    public function validateAccessToken(string $token): ?array
    {
        $now = date('Y-m-d H:i:s');
        $record = $this->db->table('api_tokens')
            ->where('access_token', $token)
            ->where('revoked_at IS NULL')
            ->where('access_token_expires_at >=', $now)
            ->get(1)
            ->getRowArray();

        if (!$record) {
            return null;
        }

        return $this->getUserProfile((int)$record['user_id']);
    }

    /**
     * Retrieve complete user profile with role, groups, and permissions.
     */
    public function getUserProfile(int $userId): ?array
    {
        $user = $this->db->table('aauth_users')
            ->where('id', $userId)
            ->get(1)
            ->getRowArray();

        if (!$user) {
            return null;
        }

        // Check group
        $groupId = (int)($user['group_id'] ?? 1);
        $groupRow = $this->db->table('aauth_groups')->where('id', $groupId)->get(1)->getRowArray();
        $groupName = $groupRow['name'] ?? 'Admin';

        $isAdmin = ($groupId === 1 || strtolower($groupName) === 'admin' || strtolower($groupName) === 'superadmin');

        // Retrieve menu permissions from aauth_group_to_menu
        $menus = [];
        if ($isAdmin) {
            $menuRows = $this->db->table('aauth_menus')->get()->getResultArray();
            foreach ($menuRows as $m) {
                $menus[] = $m['name'] ?? $m['link'] ?? '';
            }
        } else {
            $permRows = $this->db->table('aauth_group_to_menu')
                ->select('aauth_menus.name, aauth_menus.link')
                ->join('aauth_menus', 'aauth_menus.id = aauth_group_to_menu.menu_id')
                ->where('aauth_group_to_menu.group_id', $groupId)
                ->get()
                ->getResultArray();
            foreach ($permRows as $p) {
                $menus[] = $p['name'] ?? $p['link'] ?? '';
            }
        }

        return [
            'id'          => (int)$user['id'],
            'username'    => $user['username'] ?? '',
            'name'        => !empty($user['name']) ? $user['name'] : ($user['username'] ?? 'User'),
            'email'       => $user['email'] ?? '',
            'phone'       => $user['phone'] ?? '',
            'group_id'    => $groupId,
            'role'        => $isAdmin ? 'admin' : 'member',
            'is_admin'    => $isAdmin,
            'permissions' => array_values(array_filter(array_unique($menus))),
        ];
    }
}
