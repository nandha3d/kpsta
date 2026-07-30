<?php

//session_start(); //we need to start session in order to access it through CI

Class AauthUsers extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library("Aauth");

        $this->load->model("aauthGroup_model");
        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');
    }

    public function index() {
        $data = false;

        $data['userForm'] = $this->createUserForm();
        $data['content'] = $this->getContent();

        $this->load->view('admin/header');
        $this->load->view('admin/aauth/user/list', $data);
        $this->load->view('admin/footer');
    }

    public function createUserForm() {
        $userInfo['title'] = 'New user';
        $userInfo['formId'] = 'addNewUserForm';
        $userInfo['group'] = 'addNewUserForm';
        $userInfo['groupSelect'] = $this->aauthGroup_model->getAllGroup();

        return $this->load->view('admin/aauth/user/form', $userInfo, TRUE);
    }

    function getContent() {
        $content['content'] = $this->aauth->list_users(FALSE, FALSE, FALSE, TRUE);
        return $this->load->view('admin/aauth/user/content', $content, TRUE);
    }

    public function add() {

        $this->form_validation->set_rules('email', 'email', 'trim');
        $this->form_validation->set_rules('username', 'username', 'trim|required');
        $this->form_validation->set_rules('password', 'password', 'trim|required');
        $this->form_validation->set_rules('group', 'Group', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");

        if ($this->form_validation->run() == FALSE) {
            $data['message'] = 'error';
            $data['data'] = array(validation_errors());
            echo json_encode($data);
            exit;
        }

        $email = $this->input->post('email');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $groupId = $this->input->post('group');
        $newUser = $this->aauth->create_user($email, $password, $username, $groupId);

        if (!$newUser) {
            $data['message'] = 'error';
            $data['data'] = $this->aauth->get_errors_array();
        } else {
            $data['message'] = 'success';
            $data['content'] = $this->getContent();
            $data['lastId'] = $newUser;
        }


        echo json_encode($data);
    }

    function formValidation() {
        
    }

    /*
     * It create html form For editing User info 
     * @return json_endcode  data
     */

    public function edit() {
        $data['message'] = 'error';
        $this->form_validation->set_rules('_id', ' ', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode($data);
            exit;
        }
        $id = $this->input->post('_id');
        $userInfo = $this->aauth->get_user($id, 'array');
        if ($userInfo) {
//            $userInfo = $this->aauth->get_user_groups($id);
            $userInfo['groupSelect'] = $this->aauthGroup_model->getAllGroup();
            //$userInfo['groupId'] = $userGroup->name;
            $userInfo['title'] = 'Edit user';
            $userInfo['formId'] = 'editUserForm';
            $data['form'] = $this->load->view('admin/aauth/user/form', $userInfo, TRUE);
            $data['message'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update Admin user login info
     * @return json_endcode  data
     */
    public function update() {
        $this->form_validation->set_rules('_id', ' ', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim');
        $this->form_validation->set_rules('username', 'username', 'trim');
        $this->form_validation->set_rules('password', 'password', 'trim');
        $this->form_validation->set_rules('status', 'status', 'trim|required');
        $this->form_validation->set_rules('group', 'group', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        if ($this->form_validation->run() == FALSE) {
            $data['message'] = 'error';
            $data['data'] = array(validation_errors());
            echo json_encode($data);
            exit;
        }

        $user_id = $this->input->post('_id');
        $email = $this->input->post('email') ? $this->input->post('email') : FALSE;
        $pass = $this->input->post('password') ? $this->input->post('password') : FALSE;
        $username = $this->input->post('username') ? $this->input->post('username') : FALSE;
        $status = $this->input->post('status');
        $group = $this->input->post('group');

        $userInfo = $this->aauth->get_user($user_id, 'array');
        $update = $this->aauth->update_user($user_id, $email, $pass, $username, $group);

        if ($userInfo['banned'] != $status && $status) {
            $this->aauth->ban_user($user_id);
        } else if ($userInfo['banned'] != $status && !$status) {
            $this->aauth->unban_user($user_id);
        }


        if (!$update) {
            $data['message'] = 'error';
            $data['data'] = $this->aauth->get_errors_array();
        } else {
            $data['message'] = 'success';
            $data['data'] = 'Changes updated';
            $data['content'] = $this->getContent();
            $data['lastId'] = $user_id;
        }








        echo json_encode($data);
        exit;
    }

}
