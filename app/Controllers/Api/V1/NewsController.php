<?php

namespace App\Controllers\Api\V1;

use App\Models\FlashNews_model;
use App\Models\News_model;
use CodeIgniter\HTTP\ResponseInterface;

class NewsController extends BaseApiController
{
    /**
     * GET /api/v1/news
     * Query: page, per_page, search
     */
    public function index(): ResponseInterface
    {
        $newsModel = model(News_model::class);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $search = trim((string)$this->request->getGet('search'));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'     => $perPage,
            'offset'    => $offset,
            'isPublish' => true,
        ];
        if (!empty($search)) {
            $params['search'] = $search;
        }

        $rawList = $newsModel->getAllNews($params) ?: [];
        $total = (int)$newsModel->getAllNewsCount($params);

        $items = array_map(function ($n) {
            return [
                'id'           => (int)($n['id'] ?? 0),
                'title'        => $n['heading'] ?? $n['title'] ?? '',
                'description'  => $n['description'] ?? '',
                'content'      => $n['content'] ?? '',
                'image_url'    => !empty($n['image']) ? $this->formatFileUrl('uploads/news/' . $n['image']) : null,
                'published_at' => $this->formatIsoDate($n['news_date'] ?? $n['created_at'] ?? null),
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        return $this->respondSuccess($items, 'News retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/news/{id}
     */
    public function show(int $id): ResponseInterface
    {
        $newsModel = model(News_model::class);
        $n = $newsModel->getNews($id);

        if (!$n || empty($n['publish'])) {
            return $this->respondNotFound('News article not found.');
        }

        $item = [
            'id'           => (int)$n['id'],
            'title'        => $n['heading'] ?? $n['title'] ?? '',
            'description'  => $n['description'] ?? '',
            'content'      => $n['content'] ?? '',
            'image_url'    => !empty($n['image']) ? $this->formatFileUrl('uploads/news/' . $n['image']) : null,
            'published_at' => $this->formatIsoDate($n['news_date'] ?? $n['created_at'] ?? null),
        ];

        return $this->respondSuccess($item, 'News article details retrieved.');
    }

    /**
     * GET /api/v1/flash-news
     */
    public function flashNews(): ResponseInterface
    {
        $flashNewsModel = model(FlashNews_model::class);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'     => $perPage,
            'offset'    => $offset,
            'isPublish' => true,
        ];
        $search = trim((string)$this->request->getGet('search'));
        if (!empty($search)) {
            $params['search'] = $search;
        }

        $rawList = $flashNewsModel->getAll($params) ?: [];
        $total = (int)$flashNewsModel->getAllCount($params);

        $items = array_map(function ($f) {
            return [
                'id'          => (int)($f['id'] ?? 0),
                'description' => $f['description'] ?? '',
                'path'        => $f['path'] ?? null,
                'news_type'   => (int)($f['news_type'] ?? 1),
                'position'    => (int)($f['position'] ?? 0),
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        return $this->respondSuccess($items, 'Flash news retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/flash-news/{id}
     */
    public function showFlashNews(int $id): ResponseInterface
    {
        $flashNewsModel = model(FlashNews_model::class);
        $f = $flashNewsModel->get($id);

        if (!$f || empty($f['is_publish'])) {
            return $this->respondNotFound('Flash news item not found.');
        }

        $item = [
            'id'          => (int)$f['id'],
            'description' => $f['description'] ?? '',
            'path'        => $f['path'] ?? null,
            'news_type'   => (int)($f['news_type'] ?? 1),
            'position'    => (int)($f['position'] ?? 0),
        ];

        return $this->respondSuccess($item, 'Flash news item details retrieved.');
    }
}
