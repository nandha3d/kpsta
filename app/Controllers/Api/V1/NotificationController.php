<?php

namespace App\Controllers\Api\V1;

use App\Libraries\PushDispatcher;
use App\Models\DevicePushTokenModel;
use App\Models\FlashNews_model;
use App\Models\News_model;
use App\Models\OrderCircular_model;
use CodeIgniter\HTTP\ResponseInterface;

class NotificationController extends BaseApiController
{
    /**
     * POST /api/v1/notifications/register-token
     * Register or update a device push token for notifications.
     */
    public function registerToken(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?: [];
        $token = trim($json['token'] ?? $this->request->getPost('token') ?? '');
        $platform = trim($json['platform'] ?? $this->request->getPost('platform') ?? 'android');
        $deviceModel = trim($json['device_model'] ?? $this->request->getPost('device_model') ?? '');

        if (empty($token)) {
            return $this->respondBadRequest('Device push token is required.');
        }

        $userId = $this->apiUser['id'] ?? null;

        try {
            $model = new DevicePushTokenModel();
            $success = $model->registerToken($token, $platform, $userId ? (int)$userId : null, $deviceModel ?: null);
        } catch (\Throwable $e) {
            $success = false;
        }

        if (!$success) {
            return $this->respondInternalError('Failed to register device token.');
        }

        return $this->respondSuccess([
            'registered' => true,
            'platform'   => $platform,
        ], 'Device push token registered successfully.');
    }

    /**
     * GET /api/v1/notifications/latest
     * Fetch latest official Orders/Circulars, Website News Updates, and Flash News.
     */
    public function getLatestNotifications(): ResponseInterface
    {
        $circularModel = model(OrderCircular_model::class);
        $newsModel = model(News_model::class);
        $flashNewsModel = model(FlashNews_model::class);

        $recentCirculars = $circularModel->getAllByType(['limit' => 5], true) ?: [];
        $recentNews = $newsModel->getAllNews(['limit' => 5, 'isPublish' => true]) ?: [];
        $recentFlash = $flashNewsModel->getAll(['isPublish' => true, 'limit' => 3]) ?: [];

        $notifications = [];

        foreach ($recentCirculars as $c) {
            $id = (int)($c['id'] ?? 0);
            $createdAt = !empty($c['created_at']) ? $c['created_at'] : ($c['date_unformat'] ?? date('Y-m-d H:i:s'));
            $ts = strtotime((string)$createdAt) ?: 0;
            $notifications[] = [
                'id'         => 'circular_' . $id,
                'type'       => 'circular',
                'title'      => 'New Official Order / Circular',
                'body'       => trim(strip_tags((string)($c['description'] ?? ''))),
                'target_id'  => (string)$id,
                'target_url' => '/circulars/' . $id,
                'created_at' => $this->formatIsoDate($createdAt) ?: (string)$createdAt,
                '_sort_ts'   => $ts,
            ];
        }

        foreach ($recentNews as $n) {
            $id = (int)($n['id'] ?? 0);
            $createdAt = $n['created_at'] ?? $n['news_date'] ?? date('Y-m-d H:i:s');
            $ts = strtotime((string)$createdAt) ?: 0;
            $notifications[] = [
                'id'         => 'news_' . $id,
                'type'       => 'news',
                'title'      => 'KPSTA News Update',
                'body'       => trim(strip_tags((string)($n['heading'] ?? $n['title'] ?? ''))),
                'target_id'  => (string)$id,
                'target_url' => '/news/' . $id,
                'created_at' => $this->formatIsoDate($createdAt) ?: (string)$createdAt,
                '_sort_ts'   => $ts,
            ];
        }

        foreach ($recentFlash as $fn) {
            $id = (int)($fn['id'] ?? 0);
            $createdAt = $fn['created_at'] ?? date('Y-m-d H:i:s');
            $ts = strtotime((string)$createdAt) ?: 0;
            $notifications[] = [
                'id'         => 'flash_' . $id,
                'type'       => 'flash_news',
                'title'      => 'KPSTA Flash News',
                'body'       => trim(strip_tags((string)($fn['description'] ?? $fn['title'] ?? ''))),
                'target_id'  => (string)$id,
                'target_url' => '/news',
                'created_at' => $this->formatIsoDate($createdAt) ?: (string)$createdAt,
                '_sort_ts'   => $ts,
            ];
        }

        usort($notifications, function ($a, $b) {
            $diff = ($b['_sort_ts'] ?? 0) <=> ($a['_sort_ts'] ?? 0);
            if ($diff !== 0) {
                return $diff;
            }
            return ((int)$b['target_id']) <=> ((int)$a['target_id']);
        });

        // Strip internal sort timestamp before responding
        $notifications = array_map(function ($item) {
            unset($item['_sort_ts']);
            return $item;
        }, array_slice($notifications, 0, 12));

        $etag = md5(json_encode($notifications));
        $this->response->setHeader('ETag', '"' . $etag . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, must-revalidate');

        return $this->respondSuccess($notifications, 'Latest notifications retrieved successfully.');
    }

    /**
     * POST /api/v1/admin/notifications/send
     * Send broadcast push notification to registered devices (Admin only).
     */
    public function sendBroadcast(): ResponseInterface
    {
        $unauth = $this->requireAdmin();
        if ($unauth) {
            return $unauth;
        }

        $json = $this->request->getJSON(true) ?: [];
        $title = trim($json['title'] ?? $this->request->getPost('title') ?? '');
        $body = trim($json['body'] ?? $this->request->getPost('body') ?? '');
        $targetUrl = trim($json['target_url'] ?? $this->request->getPost('target_url') ?? '/home');

        if (empty($title) || empty($body)) {
            return $this->respondBadRequest('Title and body are required for push notification.');
        }

        $recipientCount = PushDispatcher::dispatch(
            'broadcast_' . time(),
            'broadcast',
            $title,
            $body,
            '0',
            $targetUrl
        );

        return $this->respondSuccess([
            'sent'            => true,
            'recipient_count' => $recipientCount,
            'title'           => $title,
            'body'            => $body,
            'target_url'      => $targetUrl,
        ], "Broadcast push notification dispatched to {$recipientCount} active devices.");
    }
}
