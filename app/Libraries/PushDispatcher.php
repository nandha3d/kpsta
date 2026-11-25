<?php

namespace App\Libraries;

use App\Models\DevicePushTokenModel;
use App\Models\Settings_model;

/**
 * PushDispatcher
 * Dispatches notification records and optional FCM push messages whenever
 * official Orders/Circulars or News are uploaded/published via the Admin CMS.
 */
class PushDispatcher
{
    public static function ensureTokenTable(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('device_push_tokens')) {
                $db->query("CREATE TABLE IF NOT EXISTS `device_push_tokens` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `device_token` VARCHAR(255) NOT NULL,
                    `platform` VARCHAR(32) NOT NULL DEFAULT 'android',
                    `user_id` INT(11) UNSIGNED NULL,
                    `device_model` VARCHAR(128) NULL,
                    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `device_token_unique` (`device_token`),
                    KEY `platform_idx` (`platform`),
                    KEY `is_active_idx` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            }
        } catch (\Throwable $e) {
            // Silently ignore so admin saves are never interrupted
        }
    }

    public static function notifyCircular(int $id, string $description): void
    {
        $cleanBody = trim(strip_tags($description));
        if ($cleanBody === '') {
            $cleanBody = 'A new official order or circular has been published.';
        }
        self::dispatch(
            'circular_' . $id,
            'circular',
            'New Official Order / Circular',
            $cleanBody,
            (string)$id,
            '/circulars/' . $id
        );
    }

    public static function notifyNews(int $id, string $heading): void
    {
        $cleanBody = trim(strip_tags($heading));
        if ($cleanBody === '') {
            $cleanBody = 'A new KPSTA news update has been published.';
        }
        self::dispatch(
            'news_' . $id,
            'news',
            'KPSTA News Update',
            $cleanBody,
            (string)$id,
            '/news/' . $id
        );
    }

    public static function dispatch(
        string $eventId,
        string $type,
        string $title,
        string $body,
        string $targetId,
        string $targetUrl
    ): int {
        self::ensureTokenTable();

        $recipientCount = 0;

        try {
            $settings = model(Settings_model::class);
            $settings->update('last_push_event_json', json_encode([
                'id'         => $eventId,
                'type'       => $type,
                'title'      => $title,
                'body'       => mb_substr($body, 0, 220),
                'target_id'  => $targetId,
                'target_url' => $targetUrl,
                'created_at' => date('c'),
            ], JSON_UNESCAPED_UNICODE));

            $tokenModel = new DevicePushTokenModel();
            $tokens = $tokenModel->getActiveTokens();
            $recipientCount = count($tokens);

            // Optional direct FCM legacy push if server key is configured in env or settings
            $fcmServerKey = trim((string)(getenv('FCM_SERVER_KEY') ?: $settings->get('fcm_server_key') ?: ''));
            if ($fcmServerKey !== '' && !empty($tokens) && function_exists('curl_init')) {
                $registrationIds = array_values(array_filter(array_map(
                    fn($row) => trim((string)($row['device_token'] ?? '')),
                    $tokens
                )));
                // Filter out synthetic fallback local tokens before sending to FCM
                $fcmIds = array_values(array_filter(
                    $registrationIds,
                    fn($t) => !str_starts_with($t, 'kpsta_device_')
                ));
                if (!empty($fcmIds)) {
                    foreach (array_chunk($fcmIds, 500) as $batch) {
                        self::postFcmLegacy($fcmServerKey, $batch, $title, $body, $targetUrl, $type, $targetId);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Do not interrupt admin CMS response if push dispatch fails
        }

        return $recipientCount;
    }

    private static function postFcmLegacy(
        string $serverKey,
        array $registrationIds,
        string $title,
        string $body,
        string $targetUrl,
        string $type,
        string $targetId
    ): void {
        $payload = json_encode([
            'registration_ids' => $registrationIds,
            'priority'         => 'high',
            'notification'     => [
                'title' => $title,
                'body'  => mb_substr($body, 0, 180),
                'sound' => 'default',
            ],
            'data' => [
                'type'       => $type,
                'target_id'  => $targetId,
                'target_url' => $targetUrl,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        if ($ch === false) {
            return;
        }
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT        => 3,
        ]);
        @curl_exec($ch);
        @curl_close($ch);
    }
}

