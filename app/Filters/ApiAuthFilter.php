<?php

namespace App\Filters;

use App\Services\Api\AuthService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiAuthFilter implements FilterInterface
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Authentication required. Missing Bearer token.',
                    'data'    => null,
                    'meta'    => null,
                    'errors'  => null,
                    'code'    => 'AUTHENTICATION_REQUIRED',
                ]);
        }

        $token = trim($matches[1]);
        $user = $this->authService->validateAccessToken($token);

        if (!$user) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid or expired access token. Please log in again.',
                    'data'    => null,
                    'meta'    => null,
                    'errors'  => null,
                    'code'    => 'AUTHENTICATION_REQUIRED',
                ]);
        }

        // Attach user to request
        $request->apiUser = $user;

        // Role check argument (e.g., ['admin'])
        if (!empty($arguments) && in_array('admin', $arguments, true)) {
            if (empty($user['is_admin'])) {
                return Services::response()
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Administrator privileges required for this action.',
                        'data'    => null,
                        'meta'    => null,
                        'errors'  => null,
                        'code'    => 'FORBIDDEN',
                    ]);
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
