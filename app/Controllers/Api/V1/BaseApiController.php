<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseApiController extends BaseController
{
    /** @var list<string> Helpers loaded for API requests. */
    protected $helpers = ['url', 'text', 'format'];

    protected string $requestId = '';
    protected ?array $apiUser = null;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Correlation ID
        $reqId = $request->getHeaderLine('X-Request-ID');
        $this->requestId = !empty($reqId) ? $reqId : bin2hex(random_bytes(16));
        $this->response->setHeader('X-Request-ID', $this->requestId);
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

        // Extract user from request if ApiAuthFilter set it
        if (isset($request->apiUser)) {
            $this->apiUser = $request->apiUser;
        }
    }

    /**
     * Get authenticated API user profile.
     */
    protected function getAuthenticatedUser(): ?array
    {
        return $this->apiUser;
    }

    /**
     * Require that the caller is authenticated.
     */
    protected function requireAuth(): ?ResponseInterface
    {
        if (!$this->apiUser) {
            return $this->respondUnauthorized();
        }
        return null;
    }

    /**
     * Require that the caller is an administrator.
     */
    protected function requireAdmin(): ?ResponseInterface
    {
        $unauth = $this->requireAuth();
        if ($unauth) {
            return $unauth;
        }
        if (empty($this->apiUser['is_admin'])) {
            return $this->respondForbidden('Administrator privileges required for this action.');
        }
        return null;
    }

    /**
     * Build unified success envelope.
     */
    protected function respondSuccess($data = null, ?string $message = 'Success', ?array $meta = null, int $status = 200): ResponseInterface
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
            'errors'  => null,
            'code'    => null,
        ];

        return $this->response->setStatusCode($status)->setJSON($payload);
    }

    /**
     * Build created envelope (201).
     */
    protected function respondCreated($data = null, ?string $message = 'Resource created successfully'): ResponseInterface
    {
        return $this->respondSuccess($data, $message, null, 201);
    }

    /**
     * Build empty response (204).
     */
    protected function respondNoContent(): ResponseInterface
    {
        return $this->response->setStatusCode(204);
    }

    /**
     * Build standardized error envelope.
     */
    protected function respondError(string $message, string $code = 'ERROR', $errors = null, int $status = 400): ResponseInterface
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'data'    => null,
            'meta'    => null,
            'errors'  => $errors,
            'code'    => $code,
        ];

        return $this->response->setStatusCode($status)->setJSON($payload);
    }

    protected function respondNotFound(string $message = 'Resource not found'): ResponseInterface
    {
        return $this->respondError($message, 'NOT_FOUND', null, 404);
    }

    protected function respondUnauthorized(string $message = 'Authentication required'): ResponseInterface
    {
        return $this->respondError($message, 'AUTHENTICATION_REQUIRED', null, 401);
    }

    protected function respondForbidden(string $message = 'You do not have permission to perform this action'): ResponseInterface
    {
        return $this->respondError($message, 'FORBIDDEN', null, 403);
    }

    protected function respondValidationFailed(array $errors, string $message = 'Validation failed'): ResponseInterface
    {
        return $this->respondError($message, 'VALIDATION_ERROR', $errors, 422);
    }

    /**
     * Build pagination metadata object.
     */
    protected function buildPaginationMeta(int $page, int $perPage, int $total): array
    {
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
        return [
            'page'          => $page,
            'per_page'      => $perPage,
            'total'         => $total,
            'total_pages'   => max(1, $totalPages),
            'has_next'      => $page < $totalPages,
            'has_previous'  => $page > 1,
        ];
    }

    /**
     * Convert relative file path to absolute public URL.
     */
    protected function formatFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        return base_url(ltrim($path, '/'));
    }

    /**
     * Format a date into ISO 8601 string.
     */
    protected function formatIsoDate(?string $date): ?string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return null;
        }
        $ts = strtotime($date);
        return $ts !== false ? date('c', $ts) : $date;
    }
}
