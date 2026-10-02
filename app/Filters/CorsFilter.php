<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin') ?: '*';

        if ($request->getMethod() === 'OPTIONS') {
            return Services::response()
                ->setStatusCode(204)
                ->setHeader('Access-Control-Allow-Origin', $origin)
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Request-ID, Accept')
                ->setHeader('Access-Control-Max-Age', '86400');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin') ?: '*';
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-Request-ID, Accept');

        return $response;
    }
}
