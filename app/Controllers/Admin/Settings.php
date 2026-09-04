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

        if ($this->input->post()) {
            $month = $this->input->post('rollover_month');
            if ($month >= 1 && $month <= 12) {
                $this->Settings_model->update('rollover_month', $month);
                $this->session->set_flashdata('success', 'Settings updated successfully.');
                redirect('admin/settings');
            }
        }

        $this->load->view('admin/header');
        $this->load->view('admin/settings/index', $data);
        $this->load->view('admin/footer');
    }
}
