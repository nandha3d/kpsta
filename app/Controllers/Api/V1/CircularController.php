<?php

namespace App\Controllers\Api\V1;

use App\Models\OrderCircular_model;
use CodeIgniter\HTTP\ResponseInterface;

class CircularController extends BaseApiController
{
    /**
     * Map string type name to numeric type ID.
     */
    protected function resolveTypeId(string $typeStr): int
    {
        switch (strtolower(trim($typeStr))) {
            case 'hse':
                return 2;
            case 'vhse':
                return 3;
            case 'general':
            default:
                return 1;
        }
    }

    /**
     * GET /api/v1/order-circulars
     * Query params: type (general|hse|vhse), category, search, year, month, page, per_page
     */
    public function index(): ResponseInterface
    {
        $circularModel = model(OrderCircular_model::class);

        $typeStr = (string)($this->request->getGet('type') ?: 'general');
        $typeId = $this->resolveTypeId($typeStr);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'type'     => $typeId,
            'limit'    => $perPage,
            'offset'   => $offset,
            'category' => $this->request->getGet('category') ?: null,
            'search'   => $this->request->getGet('search') ?: null,
            'year'     => $this->request->getGet('year') ?: null,
            'month'    => $this->request->getGet('month') ?: null,
        ];

        $rawList = $circularModel->getAllByType($params, true) ?: [];
        $total = (int)$circularModel->getAllByTypeCount($params, true);

        $items = array_map(function ($row) {
            return [
                'id'            => (int)($row['id'] ?? 0),
                'title'         => $row['description'] ?? '',
                'category_id'   => !empty($row['raw_category']) ? (int)$row['raw_category'] : null,
                'category_name' => $row['category'] ?? '',
                'date'          => $this->formatIsoDate($row['date_unformat'] ?? null) ?: ($row['date'] ?? null),
                'upload_type'   => $row['upload_type'] ?? 'file',
                'file_url'      => !empty($row['path']) ? $this->formatFileUrl('uploads/order_circular/' . $row['path']) : null,
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        $meta['filter_type'] = $typeStr;

        return $this->respondSuccess($items, 'Order and circulars retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/order-circulars/{id}
     */
    public function show(int $id): ResponseInterface
    {
        $circularModel = model(OrderCircular_model::class);
        $item = $circularModel->get($id);

        if (!$item || empty($item['is_publish']) || !empty($item['is_delete'])) {
            return $this->respondNotFound('Order/Circular not found.');
        }

        $formatted = [
            'id'          => (int)$item['id'],
            'title'       => $item['description'] ?? '',
            'type_id'     => (int)($item['type'] ?? 1),
            'category_id' => (int)($item['category'] ?? 0),
            'date'        => $this->formatIsoDate($item['date'] ?? null),
            'upload_type' => $item['upload_type'] ?? 'file',
            'file_url'    => !empty($item['path']) ? $this->formatFileUrl('uploads/order_circular/' . $item['path']) : null,
        ];

        return $this->respondSuccess($formatted, 'Order/Circular details retrieved.');
    }

    /**
     * GET /api/v1/order-circulars/categories
     * Query: type (general|hse|vhse)
     */
    public function categories(): ResponseInterface
    {
        $circularModel = model(OrderCircular_model::class);
        $typeStr = (string)($this->request->getGet('type') ?: 'general');
        $typeId = $this->resolveTypeId($typeStr);

        if ($typeStr === 'all' || $this->request->getGet('all')) {
            $allMap = $circularModel->getAllCategory() ?: [];
            $data = [];
            foreach ($allMap as $id => $name) {
                $data[] = [
                    'id'   => (int)$id,
                    'name' => (string)$name,
                ];
            }
            return $this->respondSuccess($data, 'Categories retrieved successfully.');
        }

        $categories = $circularModel->getOrderCircularCategory(['type' => $typeId]) ?: [];
        if (empty($categories)) {
            $allMap = $circularModel->getAllCategory() ?: [];
            $data = [];
            foreach ($allMap as $id => $name) {
                $data[] = [
                    'id'   => (int)$id,
                    'name' => (string)$name,
                ];
            }
        } else {
            $data = array_map(function ($c) {
                return [
                    'id'   => (int)($c['id'] ?? 0),
                    'name' => $c['name'] ?? '',
                ];
            }, $categories);
        }

        return $this->respondSuccess($data, 'Categories retrieved successfully.');
    }

    /**
     * GET /api/v1/memorandums
     */
    public function memorandums(): ResponseInterface
    {
        // Reuses download controller logic for type 'memorandums' (type 7)
        $downloadController = new DownloadController();
        $this->request->setGlobal('get', array_merge($this->request->getGet(), ['type' => 'memorandums']));
        return $downloadController->index();
    }
}
