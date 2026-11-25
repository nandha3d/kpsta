<?php

namespace App\Controllers\Api\V1;

use App\Models\FlashNews_model;
use App\Models\Gallery_model;
use App\Models\News_model;
use App\Models\OfficeBearer_model;
use App\Models\OrderCircular_model;
use App\Models\Quicklink_model;
use App\Models\ReactionGallery_model;
use App\Models\ServiceCorner_model;
use App\Models\Settings_model;
use App\Models\Slider_model;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseApiController
{
    /**
     * GET /api/v1/home
     * Consolidated homepage payload for both Web and Mobile surfaces.
     */
    public function index(): ResponseInterface
    {
        $sliderModel = model(Slider_model::class);
        $flashNewsModel = model(FlashNews_model::class);
        $newsModel = model(News_model::class);
        $circularModel = model(OrderCircular_model::class);
        $galleryModel = model(Gallery_model::class);
        $quicklinkModel = model(Quicklink_model::class);
        $reactionGalleryModel = model(ReactionGallery_model::class);
        $officeBearerModel = model(OfficeBearer_model::class);
        $settingsModel = model(Settings_model::class);
        $serviceModel = model(ServiceCorner_model::class);

        // 1. Sliders
        $rawSliders = $sliderModel->getAll(['isPublish' => true, 'limit' => 5, 'show_on_home' => 1]) ?: [];
        $sliders = array_map(function ($s) {
            return [
                'id'          => (int)($s['id'] ?? 0),
                'title'       => $s['description'] ?? $s['heading'] ?? $s['title'] ?? '',
                'description' => $s['description'] ?? '',
                'image_url'   => !empty($s['image']) ? $this->formatFileUrl('uploads/slider/' . $s['image']) : null,
                'link'        => $s['path'] ?? $s['link'] ?? null,
                'created_at'  => $this->formatIsoDate($s['created_at'] ?? null),
            ];
        }, $rawSliders);

        // 2. Flash News
        $rawFlash = $flashNewsModel->getAll(['isPublish' => true]) ?: [];
        $flashNews = array_map(function ($f) {
            return [
                'id'         => (int)($f['id'] ?? 0),
                'title'      => $f['description'] ?? $f['title'] ?? $f['news'] ?? '',
                'link'       => $f['path'] ?? $f['link'] ?? null,
                'created_at' => $this->formatIsoDate($f['created_at'] ?? null),
            ];
        }, $rawFlash);

        // 3. Latest News
        $rawNews = $newsModel->getAllNews(['limit' => 5, 'isPublish' => true]) ?: [];
        $latestNews = array_map(function ($n) {
            $rawContent = $n['description'] ?? $n['content'] ?? '';
            return [
                'id'           => (int)($n['id'] ?? 0),
                'title'        => $n['heading'] ?? $n['title'] ?? '',
                'description'  => trim(strip_tags((string)$rawContent)),
                'image_url'    => !empty($n['image']) ? $this->formatFileUrl('uploads/news/' . $n['image']) : null,
                'published_at' => $this->formatIsoDate($n['news_date'] ?? $n['created_at'] ?? null),
            ];
        }, $rawNews);

        // 4. Latest Order & Circulars
        $rawOrders = $circularModel->getAllByType(['limit' => 6], true) ?: [];
        $circulars = array_map(function ($o) {
            return [
                'id'            => (int)($o['id'] ?? 0),
                'title'         => $o['description'] ?? $o['title'] ?? '',
                'circular_no'   => $o['circular_no'] ?? null,
                'type'          => $o['type_name'] ?? $o['type'] ?? 'General',
                'category'      => $o['category'] ?? $o['category_name'] ?? '',
                'circular_date' => $this->formatIsoDate($o['date_unformat'] ?? null) ?: ($o['date'] ?? null),
                'file_url'      => !empty($o['path']) ? $this->formatFileUrl('uploads/order_circular/' . $o['path']) : null,
            ];
        }, $rawOrders);

        // 5. Gallery Highlights
        $rawImages = $galleryModel->getAllImages(['limit' => 12, 'isPublish' => true]) ?: [];
        $galleryHighlights = array_map(function ($img) {
            $imageFile = $img['image'] ?? '';
            $albumId = (int)($img['id'] ?? 0);
            $albumGuid = $img['guId'] ?? $img['gu_id'] ?? '';
            $albumName = $img['name'] ?? $img['album_name'] ?? '';
            $caption = !empty($img['title']) ? $img['title'] : (!empty($albumName) ? $albumName : 'KPSTA Event');

            return [
                'id'         => (int)($img['id'] ?? 0),
                'album_id'   => $albumId > 0 ? (string)$albumId : $albumGuid,
                'album_guid' => $albumGuid,
                'album_name' => $albumName,
                'image_url'  => $this->formatGalleryImageUrl($imageFile, 'original'),
                'thumb_url'  => $this->formatGalleryImageUrl($imageFile, 'thumb'),
                'caption'    => $caption,
            ];
        }, $rawImages);

        // 6. Quick Links
        $rawLinks = $quicklinkModel->getAll(['limit' => 8, 'isPublish' => true]) ?: [];
        $quickLinks = array_map(function ($q) {
            return [
                'id'    => (int)($q['id'] ?? 0),
                'title' => $q['description'] ?? $q['title'] ?? '',
                'url'   => $q['path'] ?? $q['url'] ?? $q['link'] ?? '',
                'icon'  => $q['icon'] ?? null,
            ];
        }, $rawLinks);

        // 7. Reaction Gallery
        $rawReactions = $reactionGalleryModel->getAll(['isPublish' => true, 'limit' => 6]) ?: [];
        $reactionGallery = array_map(function ($rg) {
            return [
                'id'        => (int)($rg['id'] ?? 0),
                'title'     => $rg['description'] ?? $rg['title'] ?? '',
                'image_url' => !empty($rg['image']) ? $this->formatFileUrl('uploads/reaction_gallery/' . $rg['image']) : null,
            ];
        }, $rawReactions);

        // 8. Key State Office Bearers
        $activeTerm = $settingsModel->getActiveTerm();
        $rawLeaders = $officeBearerModel->getAll([
            'isPublish'   => true,
            'is_former'   => 0,
            'limit'       => 6,
            'active_term' => $activeTerm,
            'level'       => 'State',
            'designation' => '1,2,3',
            'sort'        => 'primary',
        ]) ?: [];
        $officeBearers = array_map(function ($ob) {
            return [
                'id'          => (int)($ob['id'] ?? 0),
                'name'        => $ob['name'] ?? '',
                'designation' => $ob['designation'] ?? '',
                'phone'       => !empty($ob['phone']) ? (string)$ob['phone'] : null,
                'email'       => $ob['email'] ?? null,
                'photo_url'   => !empty($ob['image']) ? $this->formatFileUrl('uploads/office_bearer/' . $ob['image']) : (!empty($ob['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $ob['photo']) : null),
            ];
        }, $rawLeaders);

        // 9. Service Corner Highlights
        $rawServices = $serviceModel->getAll(['status' => 1]) ?: [];
        $services = array_map(function ($svc) {
            return [
                'id'          => (int)($svc['id'] ?? 0),
                'title'       => $svc['title'] ?? '',
                'description' => $svc['description'] ?? '',
                'icon'        => $svc['icon'] ?? null,
            ];
        }, $rawServices);

        // 10. Home Auto-Popup Settings (PopupWindow manager settings with fallback)
        $popupEnabled = $settingsModel->get('popup_enabled');
        if ($popupEnabled === false || $popupEnabled === null) {
            $popupEnabled = $settingsModel->get('reaction_popup_enabled');
        }
        $isPopupEnabled = ($popupEnabled === false || $popupEnabled === null) ? true : (bool)(int)$popupEnabled;

        $popupDuration = $settingsModel->get('popup_duration');
        if ($popupDuration === false || $popupDuration === null) {
            $popupDuration = $settingsModel->get('reaction_popup_duration');
        }
        $popupDuration = (!empty($popupDuration) && (int)$popupDuration > 0) ? (int)$popupDuration : 10;

        $popupMode = (string)($settingsModel->get('popup_mode') ?: '');
        if ($popupMode === '') {
            $legacySrc = (string)($settingsModel->get('reaction_popup_source') ?: 'both');
            $popupMode = $legacySrc === 'both' ? 'combination' : $legacySrc;
        }

        $startDate = (string)($settingsModel->get('popup_start_date') ?: ($settingsModel->get('reaction_popup_start_date') ?: ''));
        $endDate = (string)($settingsModel->get('popup_end_date') ?: ($settingsModel->get('reaction_popup_end_date') ?: ''));
        $deviceTarget = (string)($settingsModel->get('popup_device_target') ?: 'both');

        // Check device target: if 'web', mobile app should not display popup
        if ($deviceTarget === 'web') {
            $isPopupEnabled = false;
        }

        $today = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Kolkata')))->format('Y-m-d');
        $inDateRange = true;
        if ($startDate !== '' && $today < $startDate) {
            $inDateRange = false;
        }
        if ($endDate !== '' && $today > $endDate) {
            $inDateRange = false;
        }

        // Specific IDs if selected
        $selectedReactionId = (int)($settingsModel->get('popup_reaction_id') ?: 0);
        $selectedNewsId = (int)($settingsModel->get('popup_news_id') ?: 0);

        // Custom fields
        $customBadge = (string)($settingsModel->get('popup_custom_badge') ?: 'Announcement');
        $customTitle = (string)($settingsModel->get('popup_custom_title') ?: 'KPSTA Special Notification');
        $customContent = (string)($settingsModel->get('popup_custom_content') ?: '');
        $customImage = (string)($settingsModel->get('popup_custom_image') ?: '');
        $customFile = (string)($settingsModel->get('popup_custom_file') ?: '');
        $customFileName = (string)($settingsModel->get('popup_custom_file_name') ?: '');
        $customCtaText = (string)($settingsModel->get('popup_custom_cta_text') ?: 'View More');
        $customCtaUrl = (string)($settingsModel->get('popup_custom_cta_url') ?: '');

        $customImageUrl = !empty($customImage) ? $this->formatFileUrl('uploads/popup/' . $customImage) : null;
        $customFileUrl = !empty($customFile) ? $this->formatFileUrl('uploads/popup/' . $customFile) : null;

        $popupItems = [];

        // Reaction item
        if (in_array($popupMode, ['combination', 'both', 'reaction_gallery'], true) && !empty($reactionGallery)) {
            $reactionItem = null;
            if ($selectedReactionId > 0) {
                foreach ($reactionGallery as $rg) {
                    if ((int)($rg['id'] ?? 0) === $selectedReactionId) {
                        $reactionItem = $rg;
                        break;
                    }
                }
            }
            if (!$reactionItem) {
                $reactionItem = $reactionGallery[0];
            }
            $popupItems[] = [
                'type'         => 'reaction',
                'id'           => (int)($reactionItem['id'] ?? 0),
                'title'        => $reactionItem['title'] ?? 'KPSTA Reaction Poster',
                'description'  => '',
                'image_url'    => $reactionItem['image_url'] ?? null,
                'route'        => '/reaction-gallery',
                'button_label' => 'Reaction Gallery',
            ];
        }

        // News item
        if (in_array($popupMode, ['combination', 'both', 'news'], true) && !empty($latestNews)) {
            $newsItem = null;
            if ($selectedNewsId > 0) {
                foreach ($latestNews as $nw) {
                    if ((int)($nw['id'] ?? 0) === $selectedNewsId) {
                        $newsItem = $nw;
                        break;
                    }
                }
            }
            if (!$newsItem) {
                $newsItem = $latestNews[0];
            }
            $newsId = (int)($newsItem['id'] ?? 0);
            $popupItems[] = [
                'type'         => 'news',
                'id'           => $newsId,
                'title'        => $newsItem['title'] ?? 'KPSTA News Bulletin',
                'description'  => $newsItem['description'] ?? '',
                'image_url'    => $newsItem['image_url'] ?? null,
                'route'        => $newsId > 0 ? ('/news/' . $newsId) : '/news',
                'button_label' => 'Read News',
                'published_at' => $newsItem['published_at'] ?? null,
            ];
        }

        // Custom item
        if (in_array($popupMode, ['combination', 'both', 'custom'], true) && (!empty($customContent) || !empty($customImageUrl) || !empty($customFileUrl))) {
            $popupItems[] = [
                'type'         => 'custom',
                'id'           => 0,
                'title'        => $customTitle,
                'badge'        => $customBadge,
                'description'  => strip_tags($customContent),
                'content_html' => $customContent,
                'image_url'    => $customImageUrl,
                'file_url'     => $customFileUrl,
                'file_name'    => $customFileName,
                'button_label' => $customCtaText,
                'route'        => $customCtaUrl,
            ];
        }

        $reactionPopup = [
            'enabled'        => ($isPopupEnabled && $inDateRange && !empty($popupItems)),
            'raw_enabled'    => $isPopupEnabled,
            'in_schedule'    => $inDateRange,
            'duration'       => $popupDuration,
            'source'         => $popupMode,
            'mode'           => $popupMode,
            'device_target'  => $deviceTarget,
            'start_date'     => $startDate !== '' ? $startDate : null,
            'end_date'       => $endDate !== '' ? $endDate : null,
            'items'          => $popupItems,
            'custom'         => [
                'badge'      => $customBadge,
                'title'      => $customTitle,
                'content'    => $customContent,
                'image_url'  => $customImageUrl,
                'file_url'   => $customFileUrl,
                'file_name'  => $customFileName,
                'cta_text'   => $customCtaText,
                'cta_url'    => $customCtaUrl,
            ],
        ];

        $payload = [
            'sliders'            => $sliders,
            'flash_news'         => $flashNews,
            'latest_news'        => $latestNews,
            'circulars'          => $circulars,
            'gallery_highlights' => $galleryHighlights,
            'quick_links'        => $quickLinks,
            'reaction_gallery'   => $reactionGallery,
            'office_bearers'     => $officeBearers,
            'services'           => $services,
            'reaction_popup'     => $reactionPopup,
            'flash_news_speed'   => (int)($settingsModel->get('flash_news_speed_mobile') ?: 65),
        ];

        return $this->respondSuccess($payload, 'Homepage data retrieved successfully.');
    }

    /**
     * GET /api/v1/site-visitors
     * Return live visitor count from site_visitors table.
     */
    public function siteVisitors(): ResponseInterface
    {
        $flashNewsModel = model(FlashNews_model::class);
        $count = $flashNewsModel->getSiteVisitorsCount();
        if ($count === false) {
            $db = \Config\Database::connect();
            $row = $db->table('site_visitors')->get()->getRowArray();
            $count = (int)($row['count'] ?? 0);
        }

        return $this->respondSuccess(['visitors_count' => (int)$count], 'Site visitors count retrieved.');
    }
}

