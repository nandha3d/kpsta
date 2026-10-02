<?php

namespace App\Controllers\Api\V1;

use App\Models\AdayapakaSabham_model;
use App\Models\ServiceCorner_model;
use CodeIgniter\HTTP\ResponseInterface;

class ServiceController extends BaseApiController
{
    /**
     * GET /api/v1/service-corner
     */
    public function index(): ResponseInterface
    {
        $serviceModel = model(ServiceCorner_model::class);
        $rawServices = $serviceModel->getAll(['status' => 1]) ?: [];

        $items = array_map(function ($s) {
            return [
                'id'          => (int)($s['id'] ?? 0),
                'title'       => $s['title'] ?? '',
                'description' => $s['description'] ?? '',
                'icon'        => $s['icon'] ?? null,
                'status'      => (int)($s['status'] ?? 1),
                'created_at'  => $this->formatIsoDate($s['created_at'] ?? null),
            ];
        }, $rawServices);

        return $this->respondSuccess($items, 'Service corner items retrieved successfully.');
    }

    /**
     * GET /api/v1/service-corner/{id}
     */
    public function show(int $id): ResponseInterface
    {
        $serviceModel = model(ServiceCorner_model::class);
        $service = $serviceModel->getAll(['id' => $id]);

        if (!$service || empty($service['status'])) {
            return $this->respondNotFound('Service corner item not found.');
        }

        $rules = $serviceModel->getRulesByServiceId($id) ?: [];

        $formattedRules = array_map(function ($r) {
            return [
                'id'          => (int)($r['id'] ?? 0),
                'title'       => $r['title'] ?? '',
                'description' => $r['description'] ?? '',
                'file_url'    => !empty($r['file']) ? $this->formatFileUrl('uploads/service_rules/' . $r['file']) : null,
            ];
        }, $rules);

        $formatted = [
            'id'          => (int)$service['id'],
            'title'       => $service['title'] ?? '',
            'description' => $service['description'] ?? '',
            'icon'        => $service['icon'] ?? null,
            'rules'       => $formattedRules,
        ];

        return $this->respondSuccess($formatted, 'Service details and rules retrieved.');
    }

    /**
     * GET /api/v1/adayapaka-sabham
     */
    public function adayapakaSabham(): ResponseInterface
    {
        $model = model(AdayapakaSabham_model::class);
        $rawList = $model->getAll(['isPublish' => true]) ?: [];

        $items = array_map(function ($row) {
            return [
                'id'          => (int)($row['id'] ?? 0),
                'title'       => $row['description'] ?? '',
                'date'        => $this->formatIsoDate($row['date'] ?? null),
                'upload_type' => $row['upload_type'] ?? 'file',
                'file_url'    => !empty($row['path']) ? $this->formatFileUrl('uploads/adayapaka_sabham/' . $row['path']) : null,
            ];
        }, $rawList);

        return $this->respondSuccess($items, 'Adayapaka Sabham items retrieved successfully.');
    }
}
