<?php

//session_start(); //we need to start session in order to access it through CI

Class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();


        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');

        $this->load->model("Home_model");
    }

    public function index() {
        // Fetch dynamic counts for the dashboard
        $data['total_users'] = $this->db->count_all('aauth_users');
        $data['total_news'] = $this->db->count_all('news');
        $data['total_gallery'] = $this->db->count_all('gallery');
        $data['total_downloads'] = $this->db->count_all('download');

        $this->load->view('admin/header');
        $this->load->view('admin/home/index', $data);
        $this->load->view('admin/footer');
    }

    function changePassword() {
        $data = array();
        $this->form_validation->set_rules('current_password', 'Confirm password', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]');

        if ($this->form_validation->run() == TRUE) {
            $this->load->library("Aauth");

            $userId = $this->session->userdata('id');
            $userInfo = $this->aauth->get_user($userId);
            $password = $this->input->post('password');
            $passwordHash = $this->aauth->hash_password($this->input->post('current_password'), $userId);
            
            $aauthCheck = $this->aauth->verify_password($passwordHash, $userInfo->pass);
           
            if (!$aauthCheck) {
                $data['formValues']['error'] = "Current password is wrong!!! Pls try again or  contact site admin";
            } else {
                $this->aauth->update_user($userId, FALSE, $password);
                $data['formValues']['success'] = "Password change successfully !!!";
            }
        }

        $this->load->view('admin/header');
        $this->load->view('admin/home/changePassword', $data);
        $this->load->view('admin/footer');
    }

    public function flashNewsSave() {
        $data['code'] = 'error';
        $this->form_validation->set_rules('id', 'ID', 'trim');
        $this->form_validation->set_rules('news', 'News', 'trim');

        $formValues = [
            'news_id' => $this->input->post('id'),
            'news' => $this->input->post('news')
        ];
        if ($this->Home_model->updateNews($formValues)) {
            $data['code'] = 'success';
        }

        echo json_encode($data);
        exit;
    }

}
