<?php

namespace App\Controllers\Api\V1;

use App\Models\Gallery_model;
use App\Models\ReactionGallery_model;
use CodeIgniter\HTTP\ResponseInterface;

class GalleryController extends BaseApiController
{
    /**
     * GET /api/v1/galleries
     * Query: year, page, per_page
     */
    public function index(): ResponseInterface
    {
        $galleryModel = model(Gallery_model::class);

        $params = ['isPublish' => true];
        $year = $this->request->getGet('year');
        if (!empty($year)) {
            $params['year'] = $year;
        }

        $rawAlbums = $galleryModel->getAllAlbum($params) ?: [];
        $total = count($rawAlbums);

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $pagedSlice = array_slice($rawAlbums, $offset, $perPage);

        $items = array_map(function ($a) {
            return [
                'id'          => (int)($a['id'] ?? 0),
                'gu_id'       => $a['guId'] ?? $a['gu_id'] ?? '',
                'name'        => $a['name'] ?? '',
                'description' => $a['description'] ?? '',
                'image_count' => (int)($a['iCount'] ?? 0),
                'cover_image' => !empty($a['coverImage']) ? $this->formatFileUrl('uploads/gallery/' . $a['coverImage']) : null,
                'created_at'  => $this->formatIsoDate($a['created_at'] ?? null),
            ];
        }, $pagedSlice);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        $meta['available_years'] = $galleryModel->getAlbumYears() ?: [];

        return $this->respondSuccess($items, 'Galleries retrieved successfully.', $meta);
    }

    /**
     * GET /api/v1/galleries/{id}
     * Accepts either numeric id or gu_id string.
     */
    public function show($id): ResponseInterface
    {
        $galleryModel = model(Gallery_model::class);

        $album = is_numeric($id) ? $galleryModel->getAlbum($id) : $galleryModel->getAlbumByGuid($id);
        if (!$album || empty($album['is_publish'])) {
            return $this->respondNotFound('Gallery album not found.');
        }

        $guid = $album['gu_id'] ?? $album['guId'] ?? '';
        $rawImages = $galleryModel->getAllImages(['albumId' => $guid, 'isPublish' => true]) ?: [];

        $images = array_map(function ($img) {
            return [
                'id'         => (int)($img['id'] ?? 0),
                'caption'    => $img['caption'] ?? '',
                'image_url'  => $this->formatFileUrl('uploads/gallery/' . ($img['image'] ?? '')),
                'is_cover'   => !empty($img['is_cover']),
                'created_at' => $this->formatIsoDate($img['created_at'] ?? null),
            ];
        }, $rawImages);

        $formatted = [
            'id'          => (int)$album['id'],
            'gu_id'       => $guid,
            'name'        => $album['name'] ?? '',
            'description' => $album['description'] ?? '',
            'created_at'  => $this->formatIsoDate($album['created_at'] ?? null),
            'images'      => $images,
        ];

        return $this->respondSuccess($formatted, 'Gallery album details retrieved.');
    }

    /**
     * GET /api/v1/galleries/{id}/images
     */
    public function images($id): ResponseInterface
    {
        $galleryModel = model(Gallery_model::class);
        $album = is_numeric($id) ? $galleryModel->getAlbum($id) : $galleryModel->getAlbumByGuid($id);

        if (!$album || empty($album['is_publish'])) {
            return $this->respondNotFound('Gallery album not found.');
        }

        $guid = $album['gu_id'] ?? $album['guId'] ?? '';
        $rawImages = $galleryModel->getAllImages(['albumId' => $guid, 'isPublish' => true]) ?: [];

        $images = array_map(function ($img) {
            return [
                'id'         => (int)($img['id'] ?? 0),
                'caption'    => $img['caption'] ?? '',
                'image_url'  => $this->formatFileUrl('uploads/gallery/' . ($img['image'] ?? '')),
                'is_cover'   => !empty($img['is_cover']),
                'created_at' => $this->formatIsoDate($img['created_at'] ?? null),
            ];
        }, $rawImages);

        return $this->respondSuccess($images, 'Gallery images retrieved.');
    }

    /**
     * GET /api/v1/reaction-gallery
     */
    public function reactionGallery(): ResponseInterface
    {
        $rgModel = model(ReactionGallery_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'isPublish' => true,
            'limit'     => $perPage,
            'offset'    => $offset,
        ];

        $rawList = $rgModel->getAll($params) ?: [];
        $total = (int)$rgModel->getAllCount($params);

        $items = array_map(function ($rg) {
            return [
                'id'         => (int)($rg['id'] ?? 0),
                'title'      => $rg['title'] ?? '',
                'image_url'  => $this->formatFileUrl('uploads/reaction_gallery/' . ($rg['image'] ?? '')),
                'created_at' => $this->formatIsoDate($rg['created_at'] ?? null),
            ];
        }, $rawList);

        $meta = $this->buildPaginationMeta($page, $perPage, $total);
        return $this->respondSuccess($items, 'Reaction gallery retrieved successfully.', $meta);
    }
}
