<?php

//session_start(); //we need to start session in order to access it through CI

Class AauthGroupToMenu extends MY_Controller {

    public function __construct() {
        parent::__construct();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("aauthGroupToMenu_model");
    }

    public function index() {
        $data = false;

        $data['content'] = "";

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }



        $data['group'] = $this->aauthGroupToMenu_model->getAllGroup();
        $data['group'] = ['' => '- - - SELECT GROUP - - -'] + $data['group'];

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/groupToMenu.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/aauth/groupToMenu/groupToMenu', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {
        $this->load->library("pagination");

        $groupId = $this->input->post('groupId');
        
        $content['menuId'] = $this->aauthGroupToMenu_model->getMenuIdGroup($groupId);
        $content['list'] = $this->aauthGroupToMenu_model->getMenuList();

        return $this->load->view('admin/aauth/groupToMenu/content', $content, TRUE);
    }

    /*
     * This function for Insert latest group
     * @return json_encode response
     */

    public function add() {
        //validation FALSE
        if ($this->input->post('group') < 1) {
            $data['code'] = 'error';
            $data['message'] = "Pls select a group";
            echo json_encode($data);
            exit;
        }

        $formValues = array(
            'group' => $this->input->post('group'),
            'menu' => $this->input->post('menu')
        );

        //Insert values
        $add = $this->aauthGroupToMenu_model->add($formValues);
        if ($add) {
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
            $data['code'] = 'success';
        } else {
            $data['code'] = 'error';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    public function menu() {
        $data['code'] = 'success';
        $data['content'] = $this->getContent();
        echo json_encode($data);
        exit;
    }

}
