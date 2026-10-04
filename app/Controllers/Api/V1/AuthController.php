<?php

namespace App\Controllers\Api\V1;

use App\Services\Api\AuthService;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseApiController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Throwable $e) {
            $json = null;
        }
        if (!is_array($json)) {
            $json = $this->request->getPost();
        }

        $username = trim((string)($json['username'] ?? $json['identifier'] ?? ''));
        $password = (string)($json['password'] ?? '');
        $deviceId = trim((string)($json['device_id'] ?? ''));
        $deviceType = trim((string)($json['device_type'] ?? 'mobile'));

        if (empty($username) || empty($password)) {
            return $this->respondValidationFailed([
                'username' => empty($username) ? ['Username, email or phone is required.'] : [],
                'password' => empty($password) ? ['Password is required.'] : [],
            ]);
        }

        $result = $this->authService->loginWithPassword($username, $password, $deviceId, $deviceType);

        if (!$result['success']) {
            return $this->respondError(
                $result['message'],
                $result['code'] ?? 'LOGIN_FAILED',
                $result['data'] ?? null,
                200
            );
        }

        return $this->respondSuccess($result['data'], $result['message']);
    }

    /**
     * POST /api/v1/auth/whatsapp/request-otp
     */
    public function requestOtp(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $phone = trim((string)($json['phone'] ?? ''));

        if (empty($phone)) {
            return $this->respondValidationFailed([
                'phone' => ['Mobile number is required.'],
            ]);
        }

        $ip = $this->request->getIPAddress();
        $result = $this->authService->requestOtp($phone, $ip);

        if (!$result['success']) {
            return $this->respondError(
                $result['message'],
                $result['code'] ?? 'ERROR',
                $result['data'] ?? null,
                $result['code'] === 'OTP_RATE_LIMITED' ? 429 : 400
            );
        }

        return $this->respondSuccess($result['data'], $result['message']);
    }

    /**
     * POST /api/v1/auth/whatsapp/verify-otp
     */
    public function verifyOtp(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $phone = trim((string)($json['phone'] ?? ''));
        $otp = trim((string)($json['otp'] ?? ''));
        $deviceId = trim((string)($json['device_id'] ?? ''));
        $deviceType = trim((string)($json['device_type'] ?? 'mobile'));

        $errors = [];
        if (empty($phone)) {
            $errors['phone'] = ['Mobile number is required.'];
        }
        if (empty($otp)) {
            $errors['otp'] = ['OTP is required.'];
        }
        if (!empty($errors)) {
            return $this->respondValidationFailed($errors);
        }

        $result = $this->authService->verifyOtp($phone, $otp, $deviceId, $deviceType);

        if (!$result['success']) {
            $status = 400;
            if ($result['code'] === 'ACCOUNT_NOT_FOUND') {
                $status = 404;
            } elseif ($result['code'] === 'ACCOUNT_DISABLED') {
                $status = 403;
            } elseif ($result['code'] === 'OTP_RATE_LIMITED') {
                $status = 429;
            }
            return $this->respondError(
                $result['message'],
                $result['code'] ?? 'AUTHENTICATION_FAILED',
                $result['data'] ?? null,
                $status
            );
        }

        return $this->respondSuccess($result['data'], $result['message']);
    }

    /**
     * POST /api/v1/auth/refresh
     */
    public function refresh(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $refreshToken = trim((string)($json['refresh_token'] ?? ''));

        if (empty($refreshToken)) {
            return $this->respondValidationFailed([
                'refresh_token' => ['Refresh token is required.'],
            ]);
        }

        $result = $this->authService->refreshToken($refreshToken);

        if (!$result['success']) {
            return $this->respondUnauthorized($result['message']);
        }

        return $this->respondSuccess($result['data'], $result['message']);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(): ResponseInterface
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
            $this->authService->logout($token);
        }

        return $this->respondSuccess(null, 'Logged out successfully.');
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(): ResponseInterface
    {
        $unauth = $this->requireAuth();
        if ($unauth) {
            return $unauth;
        }

        return $this->respondSuccess($this->getAuthenticatedUser(), 'User profile retrieved successfully.');
    }

    /**
     * GET /api/v1/auth/permissions
     */
    public function permissions(): ResponseInterface
    {
        $unauth = $this->requireAuth();
        if ($unauth) {
            return $unauth;
        }

        $user = $this->getAuthenticatedUser();
        return $this->respondSuccess([
            'role'        => $user['role'] ?? 'guest',
            'is_admin'    => $user['is_admin'] ?? false,
            'permissions' => $user['permissions'] ?? [],
        ], 'Permissions retrieved successfully.');
    }
}
