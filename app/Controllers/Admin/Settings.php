<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
class Settings extends AppController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model("Settings_model");
    }

    public function index() {
        $data['rollover_month'] = $this->Settings_model->get('rollover_month');
        if (!$data['rollover_month']) {
            $data['rollover_month'] = 2; // Default February
        }

        $popupEnabled = $this->Settings_model->get('reaction_popup_enabled');
        $data['reaction_popup_enabled'] = ($popupEnabled === false || $popupEnabled === null) ? 1 : (int)$popupEnabled;

        $popupDuration = $this->Settings_model->get('reaction_popup_duration');
        $data['reaction_popup_duration'] = ($popupDuration !== false && $popupDuration !== null) ? (int)$popupDuration : 10;

        $popupSource = $this->Settings_model->get('reaction_popup_source');
        $data['reaction_popup_source'] = in_array($popupSource, ['both', 'reaction_gallery', 'news'], true) ? $popupSource : 'both';

        $popupStart = $this->Settings_model->get('reaction_popup_start_date');
        $data['reaction_popup_start_date'] = ($popupStart !== false && $popupStart !== null) ? trim((string)$popupStart) : '';

        $popupEnd = $this->Settings_model->get('reaction_popup_end_date');
        $data['reaction_popup_end_date'] = ($popupEnd !== false && $popupEnd !== null) ? trim((string)$popupEnd) : '';

        $webTickerSpeed = $this->Settings_model->get('flash_news_speed_web');
        $data['flash_news_speed_web'] = ($webTickerSpeed !== false && $webTickerSpeed !== null) ? (int)$webTickerSpeed : 7;

        $mobileTickerSpeed = $this->Settings_model->get('flash_news_speed_mobile');
        $data['flash_news_speed_mobile'] = ($mobileTickerSpeed !== false && $mobileTickerSpeed !== null) ? (int)$mobileTickerSpeed : 65;

        if ($this->input->post()) {
            $month = $this->input->post('rollover_month');
            if ($month >= 1 && $month <= 12) {
                $this->Settings_model->update('rollover_month', $month);
            }

            $webSpeedInput = (int)$this->input->post('flash_news_speed_web');
            if ($webSpeedInput < 1) {
                $webSpeedInput = 7;
            }
            $this->Settings_model->update('flash_news_speed_web', $webSpeedInput);

            $mobileSpeedInput = (int)$this->input->post('flash_news_speed_mobile');
            if ($mobileSpeedInput < 10) {
                $mobileSpeedInput = 65;
            }
            $this->Settings_model->update('flash_news_speed_mobile', $mobileSpeedInput);

            $popupEnabledInput = $this->input->post('reaction_popup_enabled') ? 1 : 0;
            $this->Settings_model->update('reaction_popup_enabled', $popupEnabledInput);

            $durationInput = (int)$this->input->post('reaction_popup_duration');
            if ($durationInput < 1) {
                $durationInput = 20;
            }
            $this->Settings_model->update('reaction_popup_duration', $durationInput);

            $sourceInput = trim((string)$this->input->post('reaction_popup_source'));
            if (!in_array($sourceInput, ['both', 'reaction_gallery', 'news'], true)) {
                $sourceInput = 'both';
            }
            $this->Settings_model->update('reaction_popup_source', $sourceInput);

            $startDateInput = $this->sanitizeDateInput((string)$this->input->post('reaction_popup_start_date'));
            $endDateInput = $this->sanitizeDateInput((string)$this->input->post('reaction_popup_end_date'));
            if ($startDateInput !== '' && $endDateInput !== '' && $endDateInput < $startDateInput) {
                $this->session->set_flashdata('error', 'Popup End Date cannot be earlier than Start Date.');
                redirect('admin/settings');
            }
            $this->Settings_model->update('reaction_popup_start_date', $startDateInput);
            $this->Settings_model->update('reaction_popup_end_date', $endDateInput);

            $this->session->set_flashdata('success', 'Settings updated successfully.');
            redirect('admin/settings');
        }

        $this->load->view('admin/header');
        $this->load->view('admin/settings/index', $data);
        $this->load->view('admin/footer');
    }

    private function sanitizeDateInput(string $raw): string {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $trimmed);
        return ($dt && $dt->format('Y-m-d') === $trimmed) ? $trimmed : '';
    }
}
