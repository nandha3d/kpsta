<?php

//session_start(); //we need to start session in order to access it through CI

Class AauthUsers extends Membership_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library("Aauth");

        $this->load->model("membership/AauthGroup_model", "aauthGroup_model");
        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');
    }

    public function index() {
        $data = false;

        $data['userForm'] = $this->createForm(base_url('membership/aauth/add'));
        $data['content'] = $this->getContent();


        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/membership.js'];

        $this->load->view('membership/header', $header);
        $this->load->view('membership/aauth/user/list', $data);
        $this->load->view('membership/footer', $footer);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('membership/aauth/add');
        $data['formValues'] = $formValues;
        $data['officeSelect'] = [ '' => '- - - SELECT GROUP - - -'];
        $data['officeSearch'] = array();

        $exclude = [5, 6];
        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            $exclude = [5, 6, 7];
        }
        $group = $this->aauthGroup_model->getAllGroup(false, false, $exclude);

        $data['groupSelect'] = $this->createSelectDropDown($group, 'name', '- - - SELECT GROUP - - -');

        if (isset($formValues['group_id']) && $formValues['group_id']) {
            $getOffice = $this->getOffice($formValues['group_id'], TRUE, false);
            $data['officeSelect'] = $getOffice['officeSelect'];
            $data['officeLabel'] = $getOffice['officeLabel'];
        }

        $office = $this->getOffice($this->input->get('group'), TRUE, false);
        if (isset($office['officeSelect']) && is_array($office['officeSelect'])) {
            $data['officeSearch'] = $office['officeSelect'];
        }
        $data['groupSearch'] = $this->createSelectDropDown($group, 'name');

        return $this->load->view('membership/aauth/user/form', $data, TRUE);
    }

    function getContent($getGroupIdDefault = false, $getOfficeIdDefault = false) {
        $this->load->model("membership/Main_model", "main_model");
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId($getGroupIdDefault, $getOfficeIdDefault);

        $content['groupId'] = $param['groupId'] = $getGroupId;
        $content['officeId'] = $param['officeId'] = $getofficeId;

        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));
        $param['limit'] = 15;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        $this->newUrl = $this->getNewUrl([
            'group' => $getGroupId,
            'office' => $getofficeId
                ]
        );

        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            $param['compiledSelect'] = TRUE;
            $param['compiledSelectQuery'] = $this->getOffice($getGroupId, TRUE, false, $param);
        }
        //PAGINATION CONFIGS
        $config["total_rows"] = $this->main_model->getListUserCount($param);
        $config["base_url"] = $this->getNewUrl([
            'group' => $getGroupId,
            'office' => $getofficeId
                ], ['page']
        );

        $config["per_page"] = $param['limit'];

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();
        $content['config'] = $config;


        $content['content'] = $this->main_model->getListUser($param);
        return $this->load->view('membership/aauth/user/content', $content, TRUE);
    }

    public function add() {

        $formValues = $this->formValidation();

        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('membership/aauth/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $formValues['username'] = $this->generateUserName($formValues['group_id'], $formValues['office_id'], false);

        if ($formValues['username'] == FALSE) {
            $formValues['error'] = 'Office Code is Empty!... Please update office code';
            $data['form'] = $this->createForm(base_url('membership/aauth/add'), $formValues);
            $data['code'] = 'error';
            echo json_encode($data);
            exit;
        }

        $newUser = $this->aauth->create_user(
                $formValues['email'], $formValues['password'], $formValues['username'], $formValues['group_id'], $formValues['office_id'], $formValues['name']
        );

        if (!$newUser) {
            $data['code'] = 'error';
            $formValues['error'] = $this->aauth->get_errors_array();
            $data['form'] = $this->createForm(base_url('membership/aauth/add'), $formValues);
        } else {
            $data['code'] = 'success';
            $data['content'] = $this->getContent($this->input->post('group_id'));
            $data['lastId'] = $newUser;
        }


        echo json_encode($data);
        exit;
    }

    function formValidation() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim');
//        $this->form_validation->set_rules('username', 'username', 'trim|required');
        $this->form_validation->set_rules('group_id', 'Group', 'trim|required');
        $this->form_validation->set_rules('office_id', 'Office', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");


        $formValues = [
            'email' => $this->input->post('email'),
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password'),
            'group_id' => $this->input->post('group_id'),
            'office_id' => $this->input->post('office_id'),
            'banned' => $this->input->post('status'),
            'name' => $this->input->post('name'),
        ];

        return $formValues;
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
            $userInfo['disabled'] = true;
            $url = base_url('membership/aauth/update/' . $id);
            $data['form'] = $this->createForm($url, $userInfo, 'Edit Details');
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
        $id = $this->uri->segment(4);
        $userInfo = $this->aauth->get_user($id, 'array');
        $_POST['group_id'] = $userInfo['group_id'];
        $_POST['office_id'] = $userInfo['office_id'];

        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('membership/aauth/update/' . $id);
            $data['code'] = 'error';
            $data['message'] = 'VALIDATION';
            $formValues['disabled'] = true;
            $formValues['username'] = $userInfo['username'];
            $data['form'] = $this->createForm($url, $formValues, 'Edit Details');
            echo json_encode($data);
            exit;
        }

        $formValues['username'] = $userInfo['username'];
        $update = $this->aauth->update_user(
                $id, $formValues['email'], $formValues['password'], $formValues['username'], $formValues['group_id'], $formValues['office_id'], $formValues['name']
        );

        if ($userInfo['banned'] != $formValues['banned']) {
            if ($formValues['banned']) {
                $this->aauth->ban_user($id);
            } else {
                $this->aauth->unban_user($id);
            }
        }


        if (!$update) {
            $data['code'] = 'error';
            $data['data'] = $this->aauth->get_errors_array();
        } else {
            $data['code'] = 'success';
            $data['data'] = 'Changes updated';
            $data['content'] = $this->getContent($this->input->post('group_id'), $this->input->post('office_id'));
            $data['lastId'] = $id;
        }

        echo json_encode($data);
        exit;
    }

}
