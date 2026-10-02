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
                'title'       => $s['heading'] ?? $s['title'] ?? '',
                'description' => $s['description'] ?? '',
                'image_url'   => $this->formatFileUrl('uploads/slider/' . ($s['image'] ?? '')),
                'link'        => $s['link'] ?? null,
                'created_at'  => $this->formatIsoDate($s['created_at'] ?? null),
            ];
        }, $rawSliders);

        // 2. Flash News
        $rawFlash = $flashNewsModel->getAll(['isPublish' => true]) ?: [];
        $flashNews = array_map(function ($f) {
            return [
                'id'         => (int)($f['id'] ?? 0),
                'title'      => $f['title'] ?? $f['news'] ?? '',
                'link'       => $f['link'] ?? null,
                'created_at' => $this->formatIsoDate($f['created_at'] ?? null),
            ];
        }, $rawFlash);

        // 3. Latest News
        $rawNews = $newsModel->getAllNews(['limit' => 5], true) ?: [];
        $latestNews = array_map(function ($n) {
            return [
                'id'           => (int)($n['id'] ?? 0),
                'title'        => $n['heading'] ?? $n['title'] ?? '',
                'description'  => $n['description'] ?? '',
                'image_url'    => !empty($n['image']) ? $this->formatFileUrl('uploads/news/' . $n['image']) : null,
                'published_at' => $this->formatIsoDate($n['news_date'] ?? $n['created_at'] ?? null),
            ];
        }, $rawNews);

        // 4. Latest Order & Circulars
        $rawOrders = $circularModel->getAllByType(['limit' => 6], true) ?: [];
        $circulars = array_map(function ($o) {
            return [
                'id'            => (int)($o['id'] ?? 0),
                'title'         => $o['title'] ?? '',
                'circular_no'   => $o['circular_no'] ?? null,
                'type'          => $o['type_name'] ?? $o['type'] ?? 'General',
                'category'      => $o['category_name'] ?? $o['category'] ?? '',
                'circular_date' => $this->formatIsoDate($o['circular_date'] ?? null),
                'file_url'      => !empty($o['file']) ? $this->formatFileUrl('uploads/order_circular/' . $o['file']) : null,
            ];
        }, $rawOrders);

        // 5. Gallery Highlights
        $rawImages = $galleryModel->getAllImages(['limit' => 12, 'isPublish' => true]) ?: [];
        $galleryHighlights = array_map(function ($img) {
            return [
                'id'         => (int)($img['id'] ?? 0),
                'album_id'   => (int)($img['album_id'] ?? 0),
                'album_name' => $img['album_name'] ?? '',
                'image_url'  => $this->formatFileUrl('uploads/gallery/' . ($img['image'] ?? '')),
                'caption'    => $img['caption'] ?? '',
            ];
        }, $rawImages);

        // 6. Quick Links
        $rawLinks = $quicklinkModel->getAll(['limit' => 8, 'isPublish' => true]) ?: [];
        $quickLinks = array_map(function ($q) {
            return [
                'id'    => (int)($q['id'] ?? 0),
                'title' => $q['title'] ?? '',
                'url'   => $q['url'] ?? $q['link'] ?? '',
                'icon'  => $q['icon'] ?? null,
            ];
        }, $rawLinks);

        // 7. Reaction Gallery
        $rawReactions = $reactionGalleryModel->getAll(['isPublish' => true, 'limit' => 6]) ?: [];
        $reactionGallery = array_map(function ($rg) {
            return [
                'id'        => (int)($rg['id'] ?? 0),
                'title'     => $rg['title'] ?? '',
                'image_url' => $this->formatFileUrl('uploads/reaction_gallery/' . ($rg['image'] ?? '')),
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
                'designation' => $ob['designation_name'] ?? $ob['designation'] ?? '',
                'phone'       => !empty($ob['phone']) ? (string)$ob['phone'] : null,
                'email'       => $ob['email'] ?? null,
                'photo_url'   => !empty($ob['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $ob['photo']) : null,
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

