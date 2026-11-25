<?php

namespace App\Controllers\Api\V1\Admin;

use App\Controllers\Api\V1\BaseApiController;
use App\Models\Download_model;
use App\Models\FlashNews_model;
use App\Models\Gallery_model;
use App\Models\News_model;
use App\Models\OfficeBearer_model;
use App\Models\OrderCircular_model;
use App\Models\Quicklink_model;
use App\Models\ResultLink_model;
use App\Models\Slider_model;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseApiController
{
    /**
     * GET /api/v1/admin/dashboard
     * Admin dashboard summary metrics and counts.
     */
    public function dashboard(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $newsCount = $db->table('news')->countAllResults();
        $circularCount = $db->table('order_circular')->where('is_delete !=', 1)->countAllResults();
        $downloadCount = $db->table('download')->where('is_delete !=', 1)->countAllResults();
        $albumCount = $db->table('gallery_album')->countAllResults();
        $bearerCount = $db->table('office_bearer')->countAllResults();
        $stateLeadersCount = $db->table('office_bearer')->where('level', 'State')->where('is_former', 0)->countAllResults();
        $formerStateLeadersCount = $db->table('office_bearer')
            ->where('level', 'State')
            ->groupStart()
                ->where('is_former', 1)
                ->orWhere("(previous_positions IS NOT NULL AND previous_positions != '' AND previous_positions != '[]')", null, false)
            ->groupEnd()
            ->countAllResults();
        $districtLeadersCount = $db->table('office_bearer')->where('level', 'District')->where('is_former', 0)->countAllResults();
        $teacherCount = $db->table('teacher_details')->countAllResults();
        $sliderCount = $db->table('slider')->where('is_delete !=', 1)->countAllResults();
        $flashCount = $db->table('flash_news')->where('is_delete !=', 1)->countAllResults();

        $payload = [
            'total_news'                 => $newsCount,
            'news_count'                 => $newsCount,
            'total_circulars'            => $circularCount,
            'circulars_count'            => $circularCount,
            'total_downloads'            => $downloadCount,
            'downloads_count'            => $downloadCount,
            'total_gallery_albums'       => $albumCount,
            'albums_count'               => $albumCount,
            'total_office_bearers'       => $bearerCount,
            'office_bearers_count'       => $bearerCount,
            'state_leaders_count'        => $stateLeadersCount,
            'former_state_leaders_count' => $formerStateLeadersCount,
            'district_leaders_count'     => $districtLeadersCount,
            'total_teachers'             => $teacherCount,
            'teachers_count'             => $teacherCount,
            'total_sliders'              => $sliderCount,
            'sliders_count'              => $sliderCount,
            'total_flash_news'           => $flashCount,
            'flash_news_count'           => $flashCount,
        ];

        return $this->respondSuccess($payload, 'Admin dashboard summary retrieved.');
    }

    /**
     * POST /api/v1/admin/media/upload
     * Controlled, authenticated file and image upload.
     */
    public function uploadMedia(): ResponseInterface
    {
        $folder = $this->request->getPost('folder') ?: 'general';
        $allowedFolders = ['news', 'order_circular', 'download', 'gallery', 'office_bearer', 'slider', 'service_rules', 'editorial_board', 'general'];
        if (!in_array($folder, $allowedFolders, true)) {
            $folder = 'general';
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->respondValidationFailed([
                'file' => ['A valid file must be uploaded.'],
            ]);
        }

        // Validate MIME type and size (max 25MB for PDFs/documents, 10MB for images)
        $mime = $file->getMimeType();
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
            'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip', 'application/x-zip-compressed'
        ];

        if (!in_array($mime, $allowedMimes, true)) {
            return $this->respondError('File type not allowed. Allowed types: JPG, PNG, WEBP, GIF, PDF, DOC, DOCX, ZIP.', 'FILE_INVALID', null, 422);
        }

        $targetDir = FCPATH . 'uploads/' . $folder . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Secure randomized filename
        $ext = $file->getClientExtension() ?: 'bin';
        $safeName = md5(uniqid((string)mt_rand(), true)) . '.' . $ext;

        if (!$file->move($targetDir, $safeName)) {
            return $this->respondError('Failed to save file to server.', 'FILE_UPLOAD_FAILED', null, 500);
        }

        $url = base_url('uploads/' . $folder . '/' . $safeName);

        return $this->respondCreated([
            'filename'  => $safeName,
            'folder'    => $folder,
            'url'       => $url,
            'mime_type' => $mime,
            'size'      => $file->getSize(),
        ], 'Media uploaded successfully.');
    }

    // ==========================================
    // NEWS CRUD
    // ==========================================

    public function listNews(): ResponseInterface
    {
        $model = model(News_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'  => $perPage,
            'offset' => $offset,
            'search' => $this->request->getGet('search') ?: false,
        ];

        $raw = $model->getAllNews($params) ?: [];
        $total = (int)$model->getAllNewsCount($params);

        $items = array_map(function ($n) {
            return [
                'id'           => (int)$n['id'],
                'title'        => $n['heading'] ?? '',
                'description'  => $n['description'] ?? '',
                'content'      => $n['content'] ?? '',
                'image_url'    => !empty($n['image']) ? $this->formatFileUrl('uploads/news/' . $n['image']) : null,
                'is_publish'   => (bool)($n['publish'] ?? false),
                'published_at' => $this->formatIsoDate($n['news_date'] ?? $n['created_at'] ?? null),
            ];
        }, $raw);

        return $this->respondSuccess($items, 'News list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    public function createNews(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $title = trim((string)($json['title'] ?? ''));

        if (empty($title)) {
            return $this->respondValidationFailed(['title' => ['Title is required.']]);
        }

        $data = [
            'heading'     => $title,
            'description' => $json['description'] ?? '',
            'content'     => $json['content'] ?? '',
            'image'       => $json['image'] ?? '',
            'publish'     => !empty($json['is_publish']) ? 1 : 0,
            'news_date'   => !empty($json['news_date']) ? date('Y-m-d', strtotime($json['news_date'])) : date('Y-m-d'),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $model = model(News_model::class);
        $id = $model->addNews($data);

        return $this->respondCreated(['id' => (int)$id], 'News created successfully.');
    }

    public function updateNews(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $model = model(News_model::class);

        $news = $model->getNews($id);
        if (!$news) {
            return $this->respondNotFound('News article not found.');
        }

        $update = [];
        if (isset($json['title'])) {
            $update['heading'] = trim((string)$json['title']);
        }
        if (isset($json['description'])) {
            $update['description'] = $json['description'];
        }
        if (isset($json['content'])) {
            $update['content'] = $json['content'];
        }
        if (isset($json['image'])) {
            $update['image'] = $json['image'];
        }
        if (isset($json['is_publish'])) {
            $update['publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('news')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'News updated successfully.');
    }

    public function deleteNews(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('news')->where('id', $id)->delete();
        return $this->respondSuccess(null, 'News deleted successfully.');
    }

    public function publishNews(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1;

        $db = \Config\Database::connect();
        $db->table('news')->where('id', $id)->update(['publish' => $status]);

        return $this->respondSuccess(['id' => $id, 'is_publish' => (bool)$status], 'Publish status updated.');
    }

    // ==========================================
    // ORDER CIRCULARS CRUD
    // ==========================================

    public function listCirculars(): ResponseInterface
    {
        $model = model(OrderCircular_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'  => $perPage,
            'offset' => $offset,
            'search' => $this->request->getGet('search') ?: false,
        ];
        if ($this->request->getGet('type')) {
            $params['type'] = (int)$this->request->getGet('type');
        }

        $raw = $model->getAllByType($params, false) ?: [];
        $total = (int)$model->getAllByTypeCount($params, false);

        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'title'       => $row['description'] ?? '',
                'category_id' => !empty($row['raw_category']) ? (int)$row['raw_category'] : null,
                'category'    => $row['category'] ?? '',
                'date'        => $this->formatIsoDate($row['date_unformat'] ?? null) ?: ($row['date'] ?? null),
                'is_publish'  => (bool)($row['is_publish'] ?? false),
                'file_url'    => !empty($row['path']) ? $this->formatFileUrl('uploads/order_circular/' . $row['path']) : null,
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Circulars list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    public function getCircular(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $row = $db->table('order_circular')->where('id', $id)->where('is_delete !=', 1)->get()->getRowArray();

        if (!$row) {
            return $this->respondNotFound('Order circular not found.');
        }

        $typeId = (int)($row['type'] ?? 1);
        $typeName = ($typeId === 2 ? 'hse' : ($typeId === 3 ? 'vhse' : 'general'));

        $formatted = [
            'id'          => (int)$row['id'],
            'title'       => $row['description'] ?? '',
            'type_id'     => $typeId,
            'type'        => $typeName,
            'category_id' => (int)($row['category'] ?? 0),
            'date'        => $this->formatIsoDate($row['date'] ?? null),
            'upload_type' => $row['upload_type'] ?? 'file',
            'is_publish'  => !empty($row['is_publish']),
            'file_url'    => !empty($row['path']) ? $this->formatFileUrl('uploads/order_circular/' . $row['path']) : null,
        ];

        return $this->respondSuccess($formatted, 'Order circular retrieved.');
    }

    public function createCircular(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $title = trim((string)($json['title'] ?? ''));

        if (empty($title)) {
            return $this->respondValidationFailed(['title' => ['Title is required.']]);
        }

        $typeVal = $json['type'] ?? 1;
        if (is_string($typeVal)) {
            switch (strtolower(trim($typeVal))) {
                case 'hse': $typeVal = 2; break;
                case 'vhse': $typeVal = 3; break;
                default: $typeVal = is_numeric($typeVal) ? (int)$typeVal : 1; break;
            }
        }

        $data = [
            'description' => $title,
            'type'        => (int)$typeVal,
            'category'    => (int)($json['category_id'] ?? 0),
            'date'        => !empty($json['date']) ? date('Y-m-d', strtotime($json['date'])) : date('Y-m-d'),
            'path'        => $json['path'] ?? '',
            'upload_type' => $json['upload_type'] ?? 'file',
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('order_circular')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Order circular created successfully.');
    }

    public function updateCircular(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['title'])) {
            $update['description'] = trim((string)$json['title']);
        }
        if (isset($json['type'])) {
            $typeVal = $json['type'];
            if (is_string($typeVal)) {
                switch (strtolower(trim($typeVal))) {
                    case 'hse': $typeVal = 2; break;
                    case 'vhse': $typeVal = 3; break;
                    default: $typeVal = is_numeric($typeVal) ? (int)$typeVal : 1; break;
                }
            }
            $update['type'] = (int)$typeVal;
        }
        if (isset($json['category_id'])) {
            $update['category'] = (int)$json['category_id'];
        }
        if (isset($json['date'])) {
            $update['date'] = date('Y-m-d', strtotime($json['date']));
        }
        if (isset($json['path'])) {
            $update['path'] = $json['path'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('order_circular')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Order circular updated.');
    }

    public function deleteCircular(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('order_circular')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Order circular marked as deleted.');
    }

    public function publishCircular(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1;

        $db = \Config\Database::connect();
        $db->table('order_circular')->where('id', $id)->update(['is_publish' => $status]);

        return $this->respondSuccess(['id' => $id, 'is_publish' => (bool)$status], 'Publish status updated.');
    }

    // ==========================================
    // DOWNLOADS CRUD
    // ==========================================

    public function listDownloads(): ResponseInterface
    {
        $model = model(Download_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'  => $perPage,
            'offset' => $offset,
            'search' => $this->request->getGet('search') ?: false,
        ];
        if ($this->request->getGet('type')) {
            $params['type'] = (int)$this->request->getGet('type');
        }

        $raw = $model->getAll($params, false) ?: [];
        $total = (int)$model->getAllCount($params, false);

        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'title'       => $row['description'] ?? '',
                'category_id' => !empty($row['category_id']) ? (int)$row['category_id'] : null,
                'category'    => $row['category'] ?? '',
                'date'        => $this->formatIsoDate($row['date_unformat'] ?? null) ?: ($row['date'] ?? null),
                'is_publish'  => (bool)($row['is_publish'] ?? false),
                'file_url'    => !empty($row['path']) ? $this->formatFileUrl('uploads/download/' . $row['path']) : null,
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Downloads list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    public function createDownload(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $title = trim((string)($json['title'] ?? ''));

        if (empty($title)) {
            return $this->respondValidationFailed(['title' => ['Title is required.']]);
        }

        $data = [
            'description' => $title,
            'type'        => (int)($json['type'] ?? 4),
            'category'    => (int)($json['category_id'] ?? 0),
            'date'        => !empty($json['date']) ? date('Y-m-d', strtotime($json['date'])) : date('Y-m-d'),
            'path'        => $json['path'] ?? '',
            'upload_type' => $json['upload_type'] ?? 'file',
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('download')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Download item created successfully.');
    }

    public function updateDownload(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['title'])) {
            $update['description'] = trim((string)$json['title']);
        }
        if (isset($json['type'])) {
            $update['type'] = (int)$json['type'];
        }
        if (isset($json['category_id'])) {
            $update['category'] = (int)$json['category_id'];
        }
        if (isset($json['date'])) {
            $update['date'] = date('Y-m-d', strtotime($json['date']));
        }
        if (isset($json['path'])) {
            $update['path'] = $json['path'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('download')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Download item updated.');
    }

    public function deleteDownload(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('download')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Download marked as deleted.');
    }

    public function publishDownload(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1;

        $db = \Config\Database::connect();
        $db->table('download')->where('id', $id)->update(['is_publish' => $status]);

        return $this->respondSuccess(['id' => $id, 'is_publish' => (bool)$status], 'Publish status updated.');
    }

    // ==========================================
    // OFFICE BEARERS CRUD
    // ==========================================

    public function officeBearerMetadata(): ResponseInterface
    {
        $model = model(OfficeBearer_model::class);
        $model->ensureCoreDesignations();
        $districtModel = model(\App\Models\District_model::class);

        $sectionsPayload = [];
        foreach (['state', 'former', 'district'] as $secKey) {
            $meta = $model->getSectionMeta($secKey);
            $desigMap = $model->getDesignationsForSection($secKey);
            $labelToId = [];
            foreach ($desigMap as $dId => $dLabel) {
                $labelToId[strtolower($dLabel)] = (int)$dId;
            }

            $rolesList = [];
            foreach ($meta['roles'] as $slug => $role) {
                $rolesList[] = [
                    'slug'           => $slug,
                    'label'          => $role['label'],
                    'designation_id' => $labelToId[strtolower($role['label'])] ?? 0,
                    'position'       => (int)$role['position'],
                ];
            }

            $sectionsPayload[$secKey] = [
                'key'       => $meta['key'],
                'title'     => $meta['title'],
                'level'     => $meta['level'],
                'is_former' => (bool)$meta['is_former'],
                'roles'     => $rolesList,
            ];
        }

        $districtRows = $districtModel->getAllDistrict(['limit' => 50]) ?: [];
        $districts = array_map(function ($d) {
            return [
                'id'   => (int)($d['id'] ?? 0),
                'name' => $d['district'] ?? '',
            ];
        }, $districtRows);

        return $this->respondSuccess([
            'sections'  => $sectionsPayload,
            'districts' => $districts,
        ], 'Office bearer metadata retrieved.');
    }

    public function listOfficeBearers(): ResponseInterface
    {
        $model = model(OfficeBearer_model::class);
        $section = strtolower(trim((string)($this->request->getGet('section') ?? '')));
        $roleSlug = strtolower(trim((string)($this->request->getGet('role') ?? '')));
        $district = trim((string)($this->request->getGet('district') ?? ''));
        $search = trim((string)($this->request->getGet('search') ?? ''));

        $params = [
            'limit' => 500,
        ];

        if ($section !== '' && in_array($section, ['state', 'former', 'district'], true)) {
            $meta = $model->getSectionMeta($section);
            $params['level'] = $meta['level'];
            $params['is_former'] = $meta['is_former'];
            if ($roleSlug !== '') {
                $model->applySectionRoleParam($params, $section, $roleSlug);
            }
            if ($section === 'district' && $district !== '') {
                $params['exact_district'] = $district;
            }
        } else {
            if ($this->request->getGet('level')) {
                $params['level'] = $this->request->getGet('level');
            }
            if ($this->request->getGet('is_former') !== null && $this->request->getGet('is_former') !== '') {
                $params['is_former'] = (int)$this->request->getGet('is_former');
            }
            if ($district !== '') {
                $params['district'] = $district;
            }
        }

        if ($search !== '') {
            $params['search'] = $search;
        }

        $raw = $model->getAll($params) ?: [];
        $items = array_map(function ($row) use ($section) {
            $desigName = $row['designation'] ?? '';
            $roleLabel = $desigName;
            if (($section === 'district' || ($row['level'] ?? '') === 'District') && strtoupper(trim((string)$desigName)) === 'GENERAL SECRETARY') {
                $roleLabel = 'Secretary';
            }

            $prevPos = [];
            if (!empty($row['previous_positions'])) {
                $decoded = is_array($row['previous_positions']) ? $row['previous_positions'] : json_decode((string)$row['previous_positions'], true);
                if (is_array($decoded)) {
                    $prevPos = $decoded;
                }
            }

            $imgFile = !empty($row['image']) ? $row['image'] : (!empty($row['photo']) ? $row['photo'] : '');

            return [
                'id'                 => (int)$row['id'],
                'name'               => $row['name'] ?? '',
                'designation_id'     => (int)($row['designationId'] ?? $row['designation'] ?? 0),
                'designation'        => $roleLabel,
                'phone'              => !empty($row['phone']) ? (string)$row['phone'] : null,
                'email'              => $row['email'] ?? null,
                'level'              => $row['level'] ?? 'State',
                'section_heading'    => $row['section_heading'] ?? null,
                'district'           => ($row['level'] ?? 'State') === 'District' ? ($row['section_heading'] ?? null) : null,
                'year'               => $row['year'] ?? null,
                'position'           => (int)($row['position'] ?? 25),
                'is_former'          => (bool)($row['is_former'] ?? false),
                'is_publish'         => (bool)($row['is_publish'] ?? false),
                'previous_positions' => $prevPos,
                'photo_url'          => $imgFile !== '' ? $this->formatFileUrl('uploads/office_bearer/' . $imgFile) : null,
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Office bearers retrieved.');
    }

    public function createOfficeBearer(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $name = trim((string)($json['name'] ?? ''));

        if (empty($name)) {
            return $this->respondValidationFailed(['name' => ['Name is required.']]);
        }

        $model = model(OfficeBearer_model::class);
        $model->ensureCoreDesignations();

        $section = strtolower(trim((string)($json['section'] ?? '')));
        if ($section !== '' && in_array($section, ['state', 'former', 'district'], true)) {
            $meta = $model->getSectionMeta($section);
            $level = $meta['level'];
            $isFormer = $meta['is_former'];
        } else {
            $level = $json['level'] ?? 'State';
            $isFormer = !empty($json['is_former']) ? 1 : 0;
            $section = $level === 'District' ? 'district' : ($isFormer ? 'former' : 'state');
        }

        $designationId = (int)($json['designation_id'] ?? $json['designation'] ?? 1);
        $allowed = $model->getDesignationsForSection($section);
        if (!isset($allowed[$designationId])) {
            return $this->respondValidationFailed(['designation_id' => ['Selected role is not valid for this section.']]);
        }

        $sectionHeading = trim((string)($json['section_heading'] ?? $json['district'] ?? ''));
        if ($level === 'District') {
            if ($sectionHeading === '') {
                return $this->respondValidationFailed(['district' => ['District is required for District Leaders.']]);
            }
        } else if ($level === 'State' && $sectionHeading === '') {
            $sectionHeading = $model->getDefaultSectionHeadingForDesignation($designationId);
        }

        if ($model->isSingleDesignation($designationId, $isFormer, $level, $level === 'District' ? $sectionHeading : null)) {
            return $this->respondValidationFailed([
                'designation_id' => ['This single-seat designation already exists in this scope. Please edit the existing leader instead.'],
            ]);
        }

        $imgFile = trim((string)($json['image'] ?? $json['photo'] ?? ''));

        $data = [
            'name'            => $name,
            'designation'     => $designationId,
            'phone'           => !empty($json['phone']) ? trim((string)$json['phone']) : null,
            'email'           => !empty($json['email']) ? trim((string)$json['email']) : null,
            'level'           => $level,
            'section_heading' => $sectionHeading,
            'year'            => trim((string)($json['year'] ?? '')),
            'is_former'       => $isFormer,
            'is_publish'      => isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1,
            'image'           => $imgFile,
            'position'        => (int)($json['position'] ?? 25),
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('office_bearer')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Office bearer created successfully.');
    }

    public function updateOfficeBearer(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $existing = $db->table('office_bearer')->where('id', $id)->get()->getRowArray();
        if (!$existing) {
            return $this->respondNotFound('Office bearer not found.');
        }

        $model = model(OfficeBearer_model::class);
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();

        $section = strtolower(trim((string)($json['section'] ?? '')));
        $update = [];

        if ($section !== '' && in_array($section, ['state', 'former', 'district'], true)) {
            $meta = $model->getSectionMeta($section);
            $update['level'] = $meta['level'];
            $update['is_former'] = $meta['is_former'];
        } else {
            if (isset($json['level'])) {
                $update['level'] = $json['level'];
            }
            if (isset($json['is_former'])) {
                $update['is_former'] = !empty($json['is_former']) ? 1 : 0;
            }
        }

        if (isset($json['name'])) {
            $name = trim((string)$json['name']);
            if ($name === '') {
                return $this->respondValidationFailed(['name' => ['Name cannot be empty.']]);
            }
            $update['name'] = $name;
        }
        if (isset($json['designation_id']) || isset($json['designation'])) {
            $update['designation'] = (int)($json['designation_id'] ?? $json['designation']);
        }
        if (array_key_exists('phone', $json)) {
            $update['phone'] = !empty($json['phone']) ? trim((string)$json['phone']) : null;
        }
        if (array_key_exists('email', $json)) {
            $update['email'] = $json['email'] !== null ? trim((string)$json['email']) : null;
        }
        if (isset($json['district']) || isset($json['section_heading'])) {
            $update['section_heading'] = trim((string)($json['district'] ?? $json['section_heading']));
        }
        if (isset($json['year'])) {
            $update['year'] = trim((string)$json['year']);
        }
        if (isset($json['position'])) {
            $update['position'] = (int)$json['position'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }
        if (isset($json['image']) || isset($json['photo'])) {
            $update['image'] = trim((string)($json['image'] ?? $json['photo']));
        }

        $effectiveLevel = $update['level'] ?? ($existing['level'] ?? 'State');
        $effectiveFormer = $update['is_former'] ?? (int)($existing['is_former'] ?? 0);
        $effectiveDesig = $update['designation'] ?? (int)($existing['designation'] ?? 0);
        $effectiveHeading = $update['section_heading'] ?? ($existing['section_heading'] ?? '');

        if ($effectiveLevel === 'District' && trim((string)$effectiveHeading) === '') {
            return $this->respondValidationFailed(['district' => ['District is required for District Leaders.']]);
        }

        if ($model->isSingleDesignation($effectiveDesig, $effectiveFormer, $effectiveLevel, $effectiveLevel === 'District' ? $effectiveHeading : null, $id)) {
            return $this->respondValidationFailed([
                'designation_id' => ['This single-seat designation already exists in this scope.'],
            ]);
        }

        if (!empty($update)) {
            $db->table('office_bearer')->where('id', $id)->update($update);
        }

        return $this->respondSuccess(['id' => $id], 'Office bearer updated.');
    }

    public function deleteOfficeBearer(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('office_bearer')->where('id', $id)->delete();
        return $this->respondSuccess(null, 'Office bearer deleted.');
    }

    // ==========================================
    // FLASH NEWS CRUD
    // ==========================================

    public function listFlashNews(): ResponseInterface
    {
        $model = model(FlashNews_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'  => $perPage,
            'offset' => $offset,
            'search' => $this->request->getGet('search') ?: false,
        ];
        if ($this->request->getGet('news_type')) {
            $params['news_type'] = (int)$this->request->getGet('news_type');
        }

        $raw = $model->getAll($params) ?: [];
        $total = (int)$model->getAllCount($params, false);

        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'description' => $row['description'] ?? '',
                'path'        => $row['path'] ?? '',
                'position'    => (int)($row['position'] ?? 0),
                'news_type'   => (int)($row['news_type'] ?? 1),
                'is_publish'  => (bool)($row['is_publish'] ?? false),
                'file_url'    => !empty($row['path']) ? $this->formatFileUrl('uploads/flash_news/' . $row['path']) : null,
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Flash news list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    public function createFlashNews(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $description = trim((string)($json['description'] ?? ''));

        if (empty($description)) {
            return $this->respondValidationFailed(['description' => ['Description is required.']]);
        }

        $data = [
            'description' => $description,
            'path'        => $json['path'] ?? '',
            'position'    => (int)($json['position'] ?? 1000),
            'news_type'   => (int)($json['news_type'] ?? 1),
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('flash_news')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Flash news created successfully.');
    }

    public function updateFlashNews(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['description'])) {
            $update['description'] = trim((string)$json['description']);
        }
        if (isset($json['path'])) {
            $update['path'] = $json['path'];
        }
        if (isset($json['position'])) {
            $update['position'] = (int)$json['position'];
        }
        if (isset($json['news_type'])) {
            $update['news_type'] = (int)$json['news_type'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('flash_news')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Flash news updated.');
    }

    public function deleteFlashNews(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('flash_news')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Flash news marked as deleted.');
    }

    public function publishFlashNews(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1;

        $db = \Config\Database::connect();
        $db->table('flash_news')->where('id', $id)->update(['is_publish' => $status]);

        return $this->respondSuccess(['id' => $id, 'is_publish' => (bool)$status], 'Publish status updated.');
    }

    // ==========================================
    // SLIDERS CRUD
    // ==========================================

    public function listSliders(): ResponseInterface
    {
        $model = model(Slider_model::class);
        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $params = [
            'limit'  => $perPage,
            'offset' => $offset,
            'search' => $this->request->getGet('search') ?: false,
        ];

        $raw = $model->getAll($params) ?: [];
        $total = (int)$model->getAllCount($params, false);

        $items = array_map(function ($row) {
            return [
                'id'            => (int)$row['id'],
                'description'   => $row['description'] ?? '',
                'image'         => $row['image'] ?? '',
                'position'      => (int)($row['position'] ?? 0),
                'show_on_home'  => (bool)($row['show_on_home'] ?? false),
                'is_heading_bg' => (bool)($row['is_heading_bg'] ?? false),
                'heading_pages' => $row['heading_pages'] ?? '',
                'is_publish'    => (bool)($row['is_publish'] ?? false),
                'image_url'     => !empty($row['image']) ? $this->formatFileUrl('uploads/slider/' . $row['image']) : null,
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Sliders list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    public function createSlider(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $image = trim((string)($json['image'] ?? ''));

        if (empty($image)) {
            return $this->respondValidationFailed(['image' => ['Slider image is required.']]);
        }

        $data = [
            'description'   => $json['description'] ?? '',
            'image'         => $image,
            'position'      => (int)($json['position'] ?? 1000),
            'show_on_home'  => !empty($json['show_on_home']) ? 1 : 0,
            'is_heading_bg' => !empty($json['is_heading_bg']) ? 1 : 0,
            'heading_pages' => $json['heading_pages'] ?? '',
            'is_publish'    => !empty($json['is_publish']) ? 1 : 0,
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('slider')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Slider created successfully.');
    }

    public function updateSlider(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['description'])) {
            $update['description'] = $json['description'];
        }
        if (isset($json['image'])) {
            $update['image'] = $json['image'];
        }
        if (isset($json['position'])) {
            $update['position'] = (int)$json['position'];
        }
        if (isset($json['show_on_home'])) {
            $update['show_on_home'] = !empty($json['show_on_home']) ? 1 : 0;
        }
        if (isset($json['is_heading_bg'])) {
            $update['is_heading_bg'] = !empty($json['is_heading_bg']) ? 1 : 0;
        }
        if (isset($json['heading_pages'])) {
            $update['heading_pages'] = $json['heading_pages'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('slider')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Slider updated.');
    }

    public function deleteSlider(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('slider')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Slider marked as deleted.');
    }

    public function publishSlider(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = isset($json['is_publish']) ? (!empty($json['is_publish']) ? 1 : 0) : 1;

        $db = \Config\Database::connect();
        $db->table('slider')->where('id', $id)->update(['is_publish' => $status]);

        return $this->respondSuccess(['id' => $id, 'is_publish' => (bool)$status], 'Publish status updated.');
    }

    // ==========================================
    // GALLERY CRUD & IMAGES
    // ==========================================

    public function listGalleries(): ResponseInterface
    {
        $model = model(Gallery_model::class);
        $year = $this->request->getGet('year');
        $params = [];
        if ($year) {
            $params['year'] = $year;
        }

        $raw = $model->getAllAlbum($params) ?: [];
        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'gu_id'       => $row['guId'] ?? '',
                'name'        => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'image_count' => (int)($row['iCount'] ?? 0),
                'cover_image' => !empty($row['coverImage']) ? $this->formatFileUrl('uploads/gallery/' . $row['coverImage']) : null,
                'created_at'  => $this->formatIsoDate($row['created_at'] ?? null),
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Gallery albums retrieved.');
    }

    public function createGallery(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $name = trim((string)($json['name'] ?? ''));

        if (empty($name)) {
            return $this->respondValidationFailed(['name' => ['Album name is required.']]);
        }

        $data = [
            'name'        => $name,
            'description' => $json['description'] ?? '',
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'gu_id'       => md5(uniqid((string)mt_rand(), true)),
        ];

        $db = \Config\Database::connect();
        $db->table('gallery_album')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id, 'gu_id' => $data['gu_id']], 'Gallery album created.');
    }

    public function updateGallery(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['name'])) {
            $update['name'] = trim((string)$json['name']);
        }
        if (isset($json['description'])) {
            $update['description'] = $json['description'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('gallery_album')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Gallery album updated.');
    }

    public function deleteGallery(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $album = $db->table('gallery_album')->where('id', $id)->get()->getRowArray();
        if ($album && !empty($album['gu_id'])) {
            $db->table('gallery_images')->where('album_id', $album['gu_id'])->delete();
        }
        $db->table('gallery_album')->where('id', $id)->delete();
        return $this->respondSuccess(null, 'Gallery album and images deleted.');
    }

    public function uploadGalleryImage(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $album = $db->table('gallery_album')->where('id', $id)->get()->getRowArray();
        if (!$album) {
            return $this->respondNotFound('Gallery album not found.');
        }

        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $image = trim((string)($json['image'] ?? ''));
        if (empty($image)) {
            return $this->respondValidationFailed(['image' => ['Image filename is required.']]);
        }

        $existingCount = $db->table('gallery_images')->where('album_id', $album['gu_id'])->countAllResults();
        $isCover = ($existingCount === 0 || !empty($json['is_cover'])) ? 1 : 0;

        $imageData = [
            'album_id'   => $album['gu_id'],
            'image'      => $image,
            'title'      => $json['title'] ?? '',
            'is_cover'   => $isCover,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('gallery_images')->insert($imageData);
        $imageId = $db->insertID();

        return $this->respondCreated(['id' => (int)$imageId, 'album_id' => $album['gu_id']], 'Image added to gallery.');
    }

    public function deleteGalleryImage(int $id, int $imageId): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('gallery_images')->where('id', $imageId)->delete();
        return $this->respondSuccess(null, 'Gallery image deleted.');
    }

    // ==========================================
    // QUICK LINKS CRUD
    // ==========================================

    public function listQuickLinks(): ResponseInterface
    {
        $model = model(Quicklink_model::class);
        $params = ['limit' => 200];
        $raw = $model->getAll($params) ?: [];
        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'title'       => $row['description'] ?? '',
                'url'         => $row['path'] ?? '',
                'position'    => (int)($row['position'] ?? 0),
                'is_publish'  => (bool)($row['is_publish'] ?? false),
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Quick links retrieved.');
    }

    public function createQuickLink(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $title = trim((string)($json['title'] ?? $json['description'] ?? ''));
        $url = trim((string)($json['url'] ?? $json['path'] ?? ''));

        if (empty($title)) {
            return $this->respondValidationFailed(['title' => ['Title is required.']]);
        }

        $data = [
            'description' => $title,
            'path'        => $url,
            'position'    => (int)($json['position'] ?? 1000),
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('quick_link')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Quick link created.');
    }

    public function updateQuickLink(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['title']) || isset($json['description'])) {
            $update['description'] = trim((string)($json['title'] ?? $json['description']));
        }
        if (isset($json['url']) || isset($json['path'])) {
            $update['path'] = trim((string)($json['url'] ?? $json['path']));
        }
        if (isset($json['position'])) {
            $update['position'] = (int)$json['position'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('quick_link')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Quick link updated.');
    }

    public function deleteQuickLink(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('quick_link')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Quick link marked as deleted.');
    }

    // ==========================================
    // RESULT LINKS CRUD
    // ==========================================

    public function listResultLinks(): ResponseInterface
    {
        $model = model(ResultLink_model::class);
        $params = ['limit' => 200];
        $raw = $model->getAll($params) ?: [];
        $items = array_map(function ($row) {
            return [
                'id'          => (int)$row['id'],
                'title'       => $row['description'] ?? '',
                'url'         => $row['path'] ?? '',
                'position'    => (int)($row['position'] ?? 0),
                'is_publish'  => (bool)($row['is_publish'] ?? false),
            ];
        }, $raw);

        return $this->respondSuccess($items, 'Result links retrieved.');
    }

    public function createResultLink(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $title = trim((string)($json['title'] ?? $json['description'] ?? ''));
        $url = trim((string)($json['url'] ?? $json['path'] ?? ''));

        if (empty($title)) {
            return $this->respondValidationFailed(['title' => ['Title is required.']]);
        }

        $data = [
            'description' => $title,
            'path'        => $url,
            'position'    => (int)($json['position'] ?? 1000),
            'is_publish'  => !empty($json['is_publish']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->table('result_link')->insert($data);
        $id = $db->insertID();

        return $this->respondCreated(['id' => (int)$id], 'Result link created.');
    }

    public function updateResultLink(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $update = [];
        if (isset($json['title']) || isset($json['description'])) {
            $update['description'] = trim((string)($json['title'] ?? $json['description']));
        }
        if (isset($json['url']) || isset($json['path'])) {
            $update['path'] = trim((string)($json['url'] ?? $json['path']));
        }
        if (isset($json['position'])) {
            $update['position'] = (int)$json['position'];
        }
        if (isset($json['is_publish'])) {
            $update['is_publish'] = !empty($json['is_publish']) ? 1 : 0;
        }

        $db = \Config\Database::connect();
        $db->table('result_link')->where('id', $id)->update($update);

        return $this->respondSuccess(['id' => $id], 'Result link updated.');
    }

    public function deleteResultLink(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('result_link')->where('id', $id)->update(['is_delete' => 1]);
        return $this->respondSuccess(null, 'Result link marked as deleted.');
    }

    /**
     * GET /api/v1/admin/settings
     * Global settings including Reaction Gallery Auto-Popup.
     */
    public function getSettings(): ResponseInterface
    {
        $settingsModel = model(\App\Models\Settings_model::class);
        $rollover = $settingsModel->get('rollover_month') ?: 2;
        $popupEnabled = $settingsModel->get('reaction_popup_enabled');
        $popupDuration = $settingsModel->get('reaction_popup_duration');
        $popupSource = $settingsModel->get('reaction_popup_source');
        $popupStart = $settingsModel->get('reaction_popup_start_date');
        $popupEnd = $settingsModel->get('reaction_popup_end_date');

        return $this->respondSuccess([
            'rollover_month'            => (int)$rollover,
            'reaction_popup_enabled'    => ($popupEnabled === false || $popupEnabled === null) ? 1 : (int)$popupEnabled,
            'reaction_popup_duration'   => ($popupDuration !== false && $popupDuration !== null) ? (int)$popupDuration : 20,
            'reaction_popup_source'     => in_array($popupSource, ['both', 'reaction_gallery', 'news'], true) ? $popupSource : 'both',
            'reaction_popup_start_date' => ($popupStart !== false && $popupStart !== null) ? trim((string)$popupStart) : '',
            'reaction_popup_end_date'   => ($popupEnd !== false && $popupEnd !== null) ? trim((string)$popupEnd) : '',
        ], 'Settings retrieved successfully.');
    }

    /**
     * POST /api/v1/admin/settings
     * Update global settings including Reaction Gallery Auto-Popup.
     */
    public function updateSettings(): ResponseInterface
    {
        $settingsModel = model(\App\Models\Settings_model::class);
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (isset($json['rollover_month'])) {
            $month = (int)$json['rollover_month'];
            if ($month >= 1 && $month <= 12) {
                $settingsModel->update('rollover_month', $month);
            }
        }

        if (isset($json['reaction_popup_enabled'])) {
            $enabled = !empty($json['reaction_popup_enabled']) ? 1 : 0;
            $settingsModel->update('reaction_popup_enabled', $enabled);
        }

        if (isset($json['reaction_popup_duration'])) {
            $duration = (int)$json['reaction_popup_duration'];
            if ($duration < 1) $duration = 20;
            $settingsModel->update('reaction_popup_duration', $duration);
        }

        if (isset($json['reaction_popup_source'])) {
            $source = trim((string)$json['reaction_popup_source']);
            if (!in_array($source, ['both', 'reaction_gallery', 'news'], true)) {
                $source = 'both';
            }
            $settingsModel->update('reaction_popup_source', $source);
        }

        if (array_key_exists('reaction_popup_start_date', $json)) {
            $start = trim((string)$json['reaction_popup_start_date']);
            $dt = $start !== '' ? \DateTimeImmutable::createFromFormat('Y-m-d', $start) : false;
            $settingsModel->update('reaction_popup_start_date', ($dt && $dt->format('Y-m-d') === $start) ? $start : '');
        }

        if (array_key_exists('reaction_popup_end_date', $json)) {
            $end = trim((string)$json['reaction_popup_end_date']);
            $dt = $end !== '' ? \DateTimeImmutable::createFromFormat('Y-m-d', $end) : false;
            $settingsModel->update('reaction_popup_end_date', ($dt && $dt->format('Y-m-d') === $end) ? $end : '');
        }

        return $this->respondSuccess(null, 'Settings updated successfully.');
    }
}
