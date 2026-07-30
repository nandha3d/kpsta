<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct() {
        parent::__construct();
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
