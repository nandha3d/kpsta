<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;

class PopupWindow extends AppController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->helper(['form', 'url']);
        $this->load->library(['form_validation', 'session']);
        $this->load->model('Settings_model');
        $this->load->model('ReactionGallery_model');
        $this->load->model('News_model');
    }

    public function index() {
        $data = [];
        $data['page_title'] = 'Popup Window Management';

        // 1. General & Scheduling Settings
        $popupEnabled = $this->Settings_model->get('popup_enabled');
        if ($popupEnabled === false || $popupEnabled === null) {
            // Check legacy key fallback
            $legacyEnabled = $this->Settings_model->get('reaction_popup_enabled');
            $data['popup_enabled'] = ($legacyEnabled === false || $legacyEnabled === null) ? 1 : (int)$legacyEnabled;
        } else {
            $data['popup_enabled'] = (int)$popupEnabled;
        }

        $popupDuration = $this->Settings_model->get('popup_duration');
        if ($popupDuration === false || $popupDuration === null) {
            $legacyDur = $this->Settings_model->get('reaction_popup_duration');
            $data['popup_duration'] = ($legacyDur !== false && $legacyDur !== null) ? (int)$legacyDur : 10;
        } else {
            $data['popup_duration'] = (int)$popupDuration;
        }

        $popupMode = $this->Settings_model->get('popup_mode');
        if ($popupMode === false || $popupMode === null) {
            $legacySrc = $this->Settings_model->get('reaction_popup_source');
            $data['popup_mode'] = in_array($legacySrc, ['both', 'reaction_gallery', 'news', 'custom', 'combination'], true) ? ($legacySrc === 'both' ? 'combination' : $legacySrc) : 'combination';
        } else {
            $data['popup_mode'] = (string)$popupMode;
        }

        $popupStart = $this->Settings_model->get('popup_start_date');
        if ($popupStart === false || $popupStart === null) {
            $data['popup_start_date'] = (string)($this->Settings_model->get('reaction_popup_start_date') ?: '');
        } else {
            $data['popup_start_date'] = (string)$popupStart;
        }

        $popupEnd = $this->Settings_model->get('popup_end_date');
        if ($popupEnd === false || $popupEnd === null) {
            $data['popup_end_date'] = (string)($this->Settings_model->get('reaction_popup_end_date') ?: '');
        } else {
            $data['popup_end_date'] = (string)$popupEnd;
        }

        $data['popup_device_target'] = (string)($this->Settings_model->get('popup_device_target') ?: 'both');

        // 2. Specific Selection IDs
        $data['popup_reaction_id'] = (int)($this->Settings_model->get('popup_reaction_id') ?: 0);
        $data['popup_news_id'] = (int)($this->Settings_model->get('popup_news_id') ?: 0);

        // 3. Custom Announcement / Content Fields
        $data['popup_custom_badge'] = (string)($this->Settings_model->get('popup_custom_badge') ?: 'Announcement');
        $data['popup_custom_title'] = (string)($this->Settings_model->get('popup_custom_title') ?: 'KPSTA Special Notification');
        $data['popup_custom_content'] = (string)($this->Settings_model->get('popup_custom_content') ?: '');
        $data['popup_custom_image'] = (string)($this->Settings_model->get('popup_custom_image') ?: '');
        $data['popup_custom_file'] = (string)($this->Settings_model->get('popup_custom_file') ?: '');
        $data['popup_custom_file_name'] = (string)($this->Settings_model->get('popup_custom_file_name') ?: '');
        $data['popup_custom_cta_text'] = (string)($this->Settings_model->get('popup_custom_cta_text') ?: 'View More');
        $data['popup_custom_cta_url'] = (string)($this->Settings_model->get('popup_custom_cta_url') ?: '');

        // 4. Fetch available Reaction Gallery items for dropdown
        $data['reaction_items'] = $this->ReactionGallery_model->getAll(['limit' => 40, 'isPublish' => 1]);

        // 5. Fetch available News items for dropdown
        if (method_exists($this->News_model, 'getAllNews')) {
            $data['news_items'] = $this->News_model->getAllNews(['limit' => 40, 'isPublish' => 1]);
        } elseif (method_exists($this->News_model, 'getAll')) {
            $data['news_items'] = $this->News_model->getAll(['limit' => 40, 'isPublish' => 1]);
        } else {
            $data['news_items'] = [];
        }

        // 6. Calculate Schedule Status
        $today = date('Y-m-d');
        $statusBadge = ['class' => 'label-success', 'text' => 'Active & Live (Visible to visitors)'];
        if ($data['popup_enabled'] == 0) {
            $statusBadge = ['class' => 'label-default', 'text' => 'Disabled'];
        } elseif ($data['popup_end_date'] !== '' && $today > $data['popup_end_date']) {
            $statusBadge = ['class' => 'label-danger', 'text' => 'Expired (Past pre-determined end date: ' . htmlspecialchars($data['popup_end_date']) . ')'];
        } elseif ($data['popup_start_date'] !== '' && $today < $data['popup_start_date']) {
            $statusBadge = ['class' => 'label-warning', 'text' => 'Scheduled (Will display starting: ' . htmlspecialchars($data['popup_start_date']) . ')'];
        }
        $data['status_badge'] = $statusBadge;

        $this->load->view('admin/header', ['page_title' => 'Popup Window']);
        $this->load->view('admin/popupWindow/index', $data);
        $this->load->view('admin/footer');
    }

    public function save() {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('admin/popup_window');
            return;
        }

        $enabled = $this->input->post('popup_enabled') ? 1 : 0;
        $mode = trim((string)$this->input->post('popup_mode'));
        if (!in_array($mode, ['combination', 'reaction_gallery', 'news', 'custom'], true)) {
            $mode = 'combination';
        }

        $duration = (int)$this->input->post('popup_duration');
        if ($duration < 1) $duration = 10;

        $startDate = $this->sanitizeDateInput((string)$this->input->post('popup_start_date'));
        $endDate = $this->sanitizeDateInput((string)$this->input->post('popup_end_date'));

        if ($startDate !== '' && $endDate !== '' && $endDate < $startDate) {
            $this->session->set_flashdata('error', 'End Date cannot be earlier than Start Date.');
            redirect('admin/popup_window');
            return;
        }

        $deviceTarget = trim((string)$this->input->post('popup_device_target'));
        if (!in_array($deviceTarget, ['both', 'web_only', 'app_only'], true)) {
            $deviceTarget = 'both';
        }

        $reactionId = (int)$this->input->post('popup_reaction_id');
        $newsId = (int)$this->input->post('popup_news_id');

        $customBadge = trim((string)$this->input->post('popup_custom_badge'));
        $customTitle = trim((string)$this->input->post('popup_custom_title'));
        $customContent = (string)$this->input->post('popup_custom_content');
        $customCtaText = trim((string)$this->input->post('popup_custom_cta_text'));
        $customCtaUrl = trim((string)$this->input->post('popup_custom_cta_url'));

        // Handle Custom Image Upload
        $uploadDir = FCPATH . 'uploads/popup/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $existingImage = (string)($this->Settings_model->get('popup_custom_image') ?: '');
        if ($this->input->post('remove_custom_image')) {
            if ($existingImage && file_exists(FCPATH . $existingImage)) {
                @unlink(FCPATH . $existingImage);
            }
            $existingImage = '';
        }

        if (isset($_FILES['custom_image']) && !empty($_FILES['custom_image']['name']) && $_FILES['custom_image']['error'] === UPLOAD_ERR_OK) {
            $imgExt = strtolower(pathinfo($_FILES['custom_image']['name'], PATHINFO_EXTENSION));
            if (in_array($imgExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $newImgName = 'popup_img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $imgExt;
                $destImg = $uploadDir . $newImgName;
                if (move_uploaded_file($_FILES['custom_image']['tmp_name'], $destImg)) {
                    if ($existingImage && file_exists(FCPATH . $existingImage)) {
                        @unlink(FCPATH . $existingImage);
                    }
                    $existingImage = 'uploads/popup/' . $newImgName;
                }
            }
        }

        // Handle Custom File / Downloader Upload
        $existingFile = (string)($this->Settings_model->get('popup_custom_file') ?: '');
        $existingFileName = (string)($this->Settings_model->get('popup_custom_file_name') ?: '');

        if ($this->input->post('remove_custom_file')) {
            if ($existingFile && file_exists(FCPATH . $existingFile)) {
                @unlink(FCPATH . $existingFile);
            }
            $existingFile = '';
            $existingFileName = '';
        }

        if (isset($_FILES['custom_file']) && !empty($_FILES['custom_file']['name']) && $_FILES['custom_file']['error'] === UPLOAD_ERR_OK) {
            $fileOrigName = $_FILES['custom_file']['name'];
            $fileExt = strtolower(pathinfo($fileOrigName, PATHINFO_EXTENSION));
            $allowedDocs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'png'];
            if (in_array($fileExt, $allowedDocs, true)) {
                $newFileName = 'popup_doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
                $destFile = $uploadDir . $newFileName;
                if (move_uploaded_file($_FILES['custom_file']['tmp_name'], $destFile)) {
                    if ($existingFile && file_exists(FCPATH . $existingFile)) {
                        @unlink(FCPATH . $existingFile);
                    }
                    $existingFile = 'uploads/popup/' . $newFileName;
                    $existingFileName = $fileOrigName;
                }
            }
        }

        // Save into settings table
        $this->Settings_model->update('popup_enabled', $enabled);
        $this->Settings_model->update('popup_mode', $mode);
        $this->Settings_model->update('popup_duration', $duration);
        $this->Settings_model->update('popup_start_date', $startDate);
        $this->Settings_model->update('popup_end_date', $endDate);
        $this->Settings_model->update('popup_device_target', $deviceTarget);
        $this->Settings_model->update('popup_reaction_id', $reactionId);
        $this->Settings_model->update('popup_news_id', $newsId);

        $this->Settings_model->update('popup_custom_badge', $customBadge);
        $this->Settings_model->update('popup_custom_title', $customTitle);
        $this->Settings_model->update('popup_custom_content', $customContent);
        $this->Settings_model->update('popup_custom_image', $existingImage);
        $this->Settings_model->update('popup_custom_file', $existingFile);
        $this->Settings_model->update('popup_custom_file_name', $existingFileName);
        $this->Settings_model->update('popup_custom_cta_text', $customCtaText);
        $this->Settings_model->update('popup_custom_cta_url', $customCtaUrl);

        // Keep legacy sync for backward compatibility
        $this->Settings_model->update('reaction_popup_enabled', $enabled);
        $this->Settings_model->update('reaction_popup_duration', $duration);
        $this->Settings_model->update('reaction_popup_source', $mode === 'combination' ? 'both' : $mode);
        $this->Settings_model->update('reaction_popup_start_date', $startDate);
        $this->Settings_model->update('reaction_popup_end_date', $endDate);

        // Invalidate Home Page Caches
        $this->deleteCachedPage('/home');
        $this->deleteCachedPage('/');

        $this->session->set_flashdata('success', 'Popup Window configuration saved successfully.');
        redirect('admin/popup_window');
    }

    private function sanitizeDateInput(string $value): string {
        $trimmed = trim($value);
        if ($trimmed === '') return '';
        $dt = \DateTime::createFromFormat('Y-m-d', $trimmed);
        return ($dt && $dt->format('Y-m-d') === $trimmed) ? $trimmed : '';
    }
}
