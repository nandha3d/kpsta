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

    /*
     * It create html form For editing New
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);

        $groupInfo = $this->aauthGroupToMenu_model->getNews($id);
        if ($groupInfo) {
            $url = base_url('admin/group/update/' . $id);
            $data['form'] = $this->createForm($url, $groupInfo, 'Edit News');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update News info
     * @return json_endcode  data
     */
    public function update() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('definition', 'Definition', 'trim');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        $formValues = [
            'name' => $this->input->post('name'),
            'definition' => $this->input->post('definition'),
        ];

        $id = $this->uri->segment(4);
        $groupInfo = $this->aauthGroupToMenu_model->getNews($id);
        if (!$groupInfo) {
            $formValues['error'] = "Record not found!!!";
        }
        //validation FALSE
        if ($this->form_validation->run() == FALSE || !$groupInfo) {
            $data['code'] = 'error';
            $url = base_url('admin/group/update/' . $id);
            $data['form'] = $this->createForm($url, $groupInfo, 'Edit News');
            echo json_encode($data);
            exit;
        }
        //update group
        $this->aauthGroupToMenu_model->update($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['data'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    /**
     * Update Publish status
     * @return json_endcode  data
     */
    public function publish() {
        $this->form_validation->set_rules('_id', ' ', 'trim|required');
        $this->form_validation->set_rules('publish', 'Heading', 'trim|required');
        if ($this->form_validation->run()) {
            $id = $this->input->post('_id');
            $publish = $this->input->post('publish');

            $this->aauthGroupToMenu_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo fale;
        exit;
    }

    public function search() {
        $data['code'] = 'success';
        $data['content'] = $this->getContent();

        echo json_encode($data);
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $orderInfo = $this->aauthGroupToMenu_model->getNews($id);
        $delete = $this->aauthGroup_model->delete($id);
        if ($delete && $orderInfo) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

    public function menu() {

        $data['code'] = 'success';
        $data['content'] = $this->getContent();
        echo json_encode($data);
        exit;


        echo "<pre>";
        print_r($info);
        exit;
    }

}
