<?php

namespace App\Controllers\Api\V1;

use App\Models\Quicklink_model;
use App\Models\ResultLink_model;
use CodeIgniter\HTTP\ResponseInterface;

class LinkController extends BaseApiController
{
    /**
     * GET /api/v1/quick-links
     */
    public function quickLinks(): ResponseInterface
    {
        $quicklinkModel = model(Quicklink_model::class);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 50)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'isPublish' => true,
            'limit'     => $perPage,
            'offset'    => $offset,
            'search'    => $this->request->getGet('search') ?: false,
        ];

        $rawList = $quicklinkModel->getAll($params) ?: [];
        $total = (int)$quicklinkModel->getAllCount($params, true);

        $items = array_map(function ($row) {
            return [
                'id'       => (int)($row['id'] ?? 0),
                'title'    => $row['title'] ?? '',
                'url'      => $row['url'] ?? $row['link'] ?? '',
                'position' => (int)($row['position'] ?? 0),
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        return $this->respondSuccess($items, 'Quick links retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/results
     */
    public function results(): ResponseInterface
    {
        $resultModel = model(ResultLink_model::class);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 50)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'isPublish' => true,
            'limit'     => $perPage,
            'offset'    => $offset,
            'search'    => $this->request->getGet('search') ?: false,
        ];

        $rawList = $resultModel->getAll($params) ?: [];
        $total = (int)$resultModel->getAllCount($params, true);

        $items = array_map(function ($row) {
            return [
                'id'       => (int)($row['id'] ?? 0),
                'title'    => $row['title'] ?? '',
                'url'      => $row['url'] ?? $row['link'] ?? '',
                'position' => (int)($row['position'] ?? 0),
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        return $this->respondSuccess($items, 'Exam/competition results retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/results/{id}
     */
    public function showResult(int $id): ResponseInterface
    {
        $resultModel = model(ResultLink_model::class);
        $item = $resultModel->get($id);

        if (!$item || empty($item['is_publish']) || !empty($item['is_delete'])) {
            return $this->respondNotFound('Result link not found.');
        }

        $formatted = [
            'id'       => (int)$item['id'],
            'title'    => $item['title'] ?? '',
            'url'      => $item['url'] ?? $item['link'] ?? '',
            'position' => (int)($item['position'] ?? 0),
        ];

        return $this->respondSuccess($formatted, 'Result link details retrieved.');
    }
}
