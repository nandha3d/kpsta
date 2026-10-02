<?php

namespace App\Controllers\Api\V1;

use App\Models\Download_model;
use CodeIgniter\HTTP\ResponseInterface;

class DownloadController extends BaseApiController
{
    /**
     * Map string download type to integer ID.
     */
    protected function resolveDownloadType(string $typeStr): int
    {
        switch (strtolower(trim($typeStr))) {
            case 'act_rules':
                return 1;
            case 'softwares':
                return 2;
            case 'fonts':
                return 3;
            case 'forms':
                return 4;
            case 'notice_poster':
                return 5;
            case 'melakal':
                return 6;
            case 'memorandums':
            case 'official_outlook':
                return 7;
            case 'academic_corner':
                return 8;
            default:
                return is_numeric($typeStr) ? (int)$typeStr : 4;
        }
    }

    /**
     * GET /api/v1/downloads
     * Query: type (forms|act_rules|softwares|fonts|notice_poster|melakal|memorandums|academic_corner),
     *        category, search, page, per_page
     */
    public function index(): ResponseInterface
    {
        $downloadModel = model(Download_model::class);

        $typeParam = (string)($this->request->getGet('type') ?: 'forms');
        $typeId = $this->resolveDownloadType($typeParam);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'type'     => $typeId,
            'limit'    => $perPage,
            'offset'   => $offset,
            'category' => $this->request->getGet('category') ?: null,
            'search'   => $this->request->getGet('search') ?: null,
        ];

        $rawList = $downloadModel->getAll($params, true) ?: [];
        $total = (int)$downloadModel->getAllCount($params, true);

        $items = array_map(function ($row) {
            return [
                'id'            => (int)($row['id'] ?? 0),
                'title'         => $row['description'] ?? '',
                'category_id'   => !empty($row['category_id']) ? (int)$row['category_id'] : null,
                'category_name' => $row['category'] ?? '',
                'date'          => $this->formatIsoDate($row['date_unformat'] ?? null) ?: ($row['date'] ?? null),
                'upload_type'   => $row['upload_type'] ?? 'file',
                'file_url'      => !empty($row['path']) ? $this->formatFileUrl('uploads/download/' . $row['path']) : null,
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        $meta['filter_type'] = $typeParam;

        return $this->respondSuccess($items, 'Downloads retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/downloads/{id}
     */
    public function show(int $id): ResponseInterface
    {
        $downloadModel = model(Download_model::class);
        $item = $downloadModel->get($id);

        if (!$item || empty($item['is_publish']) || !empty($item['is_delete'])) {
            return $this->respondNotFound('Download item not found.');
        }

        $formatted = [
            'id'          => (int)$item['id'],
            'title'       => $item['description'] ?? '',
            'type_id'     => (int)($item['type'] ?? 0),
            'category_id' => (int)($item['category'] ?? 0),
            'date'        => $this->formatIsoDate($item['date'] ?? null),
            'upload_type' => $item['upload_type'] ?? 'file',
            'file_url'    => !empty($item['path']) ? $this->formatFileUrl('uploads/download/' . $item['path']) : null,
        ];

        return $this->respondSuccess($formatted, 'Download item details retrieved.');
    }

    /**
     * GET /api/v1/downloads/categories
     * Query: type (forms|melakal|academic_corner)
     */
    public function categories(): ResponseInterface
    {
        $downloadModel = model(Download_model::class);
        $typeParam = (string)($this->request->getGet('type') ?: 'forms');
        $typeId = $this->resolveDownloadType($typeParam);

        $categories = $downloadModel->getFormsCategory(['type' => $typeId]) ?: [];
        $data = array_map(function ($c) {
            return [
                'id'   => (int)($c['id'] ?? 0),
                'name' => $c['name'] ?? '',
            ];
        }, $categories);

        return $this->respondSuccess($data, 'Download categories retrieved successfully.');
    }
}
