<?php

//session_start(); //we need to start session in order to access it through CI

Class Home extends Membership_Controller {

    public function __construct() {
        parent::__construct();


        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');

        $this->load->model("Home_model");
        $this->load->model("membership/TeacherProcess_model");
        $this->load->model("membership/AauthGroup_model", "aauthGroup_model");
    }

    public function index() {
//        $data['news'] = $this->Home_model->getAll();

        $this->load->model("membership/WhatsNew_model", "whatsNew_model");
        $data['whatsNew'] = $this->whatsNew_model->getAllNews(['isPublish' => 1]);

        $this->load->model("OfficeBearer_model");
        $data['officeBearer'] = $this->OfficeBearer_model->getAll(array('isPublish' => TRUE, 'limit' => 3));
        
        $data['membership'] = $this->membershipcount();

        $this->load->view('membership/header');
        $this->load->view('membership/home/index', $data);
        $this->load->view('membership/footer');
    }
    
    function membershipcount(){
        //here "7" consider as Teacher group
        $group = $this->input->get("group");
        if(!$group || $group == 7){
            $group = $this->aauthGroupId;
        }
        
        if($this->input->get('office') && $this->input->get("group") != 7){
            $office = $this->input->get('office');
        }else{
            $office = $this->aauthOfficeId;
        }
        
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId($group, $office);
        $data['groupId'] = $getGroupId;
        $data['officeId'] = $getofficeId;
        $data['year'] = $this->input->get('year') ? $this->input->get('year') : $this->year;
        $this->load->model("membership/Teacher_model", "Teacher_model");
        $data['data'] = $this->Teacher_model->getDashboard($data);
        $data['table'] = $this->load->view('membership/home/membershipCount', $data, TRUE);
        
        if ($this->input->is_ajax_request()) {
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }
        
        return $data;
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

        $this->load->view('membership/header');
        $this->load->view('membership/home/changePassword', $data);
        $this->load->view('membership/footer');
    }

    function config() {
        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            redirect('membership/home');
        }

        $data = array();
        $this->load->model("membership/Config_model", "config_model");

        $this->form_validation->set_rules('enable_entry', 'Enable new entry', 'trim');
        $data['data'] = $this->config_model->getAll();

        if ($this->form_validation->run() == TRUE) {
            $data['data'] = array(
                "enable_entry" => $this->input->post('enable_entry') ? 1 : 0,
                "year" => $this->input->post('year')
            );

            $formYear = $data['data']['year'];

            if (2017 != $formYear) {
                $this->TeacherProcess_model->createProcessEntry($formYear);
            }

            $this->config_model->update($data['data']);


            $data['formValues']['success'] = "Changes are updated successfully !!!";
        }

        $this->load->view('membership/header');
        $this->load->view('membership/home/config', $data);
        $this->load->view('membership/footer');
    }

}
