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
        $this->load->model("News_model");
        $this->load->model("Gallery_model");
        $this->load->model("Download_model");
        $this->load->model("OrderCircular_model");
        $this->load->model("OfficeBearer_model");
        $this->load->model("Settings_model");

        // Counts come from the models so soft deleted and unpublished rows are
        // treated the same way the public site treats them.
        $activeTerm = $this->Settings_model->getActiveTerm();

        $data['active_term'] = $activeTerm;
        $data['total_users'] = $this->db->count_all('aauth_users');
        $data['total_news'] = $this->News_model->getAllNewsCount(array('isPublish' => TRUE));
        $data['total_gallery'] = $this->Gallery_model->getAllAlbumCount();
        $data['total_downloads'] = $this->Download_model->getAllCount(array(), TRUE);
        $data['total_orders'] = $this->OrderCircular_model->getAllByTypeCount(array(), TRUE);
        $data['total_office_bearers'] = $this->OfficeBearer_model->getAllCount(array(
            'isPublish' => TRUE,
            'is_former' => 0,
            'active_term' => $activeTerm,
            'level' => 'State',
        ));

        $data['recent_news'] = $this->News_model->getAllNews(array('limit' => 5));

        $header['page_title'] = 'Dashboard';

        $this->load->view('admin/header', $header);
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
