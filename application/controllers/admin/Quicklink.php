<?php

Class Quicklink extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Load form helper library
        $this->load->helper(array('form', 'url'));
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("Quicklink_model");
    }

    function getSegment3() {
        return $this->uri->segment(3);
    }

    public function index() {
        $data = array();
        //get Content
        $data['content'] = $this->getContent();
        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $data['form'] = $this->createForm(base_url('admin/quicklink/add'));

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/download.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/quicklink/quicklink', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {
        $this->load->library("pagination");

        $param['limit'] = 10;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->Quicklink_model->getAllCount($param);
        $config["base_url"] = base_url() . "admin/quicklink";
        $config["per_page"] = $param['limit'];

        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = ($temp <= $config["total_rows"]) ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $this->newUrl = $this->getNewUrl([
            'page' => $param['page']
        ]);

        $content['orders'] = $this->Quicklink_model->getAll($param);
        return $this->load->view('admin/quicklink/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add ") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/quicklink/add');
        $data['formValues'] = $formValues;


        return $this->load->view('admin/quicklink/form', $data, TRUE);
    }

    /*
     * This function for Insert latest news
     * @return json_encode response
     */

    public function add() {
        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $url = base_url('admin/quicklink/add');
            $data['form'] = $this->createForm($url, $formValues);
            echo json_encode($data);
            exit;
        }

        //Insert values
        $add = $this->Quicklink_model->add($formValues);
        if ($add) {
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
        } else {
            $data['code'] = 'success';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function formValidation() {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('path', 'Path', 'trim|required|callback_urlValidate');
        $this->form_validation->set_rules('is_publish', 'Publish', 'trim|required');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'description' => $this->input->post('description'),
            'path' => $this->input->post('path'),
            'is_publish' => $this->input->post('is_publish'),
            'position' => $this->input->post('position') ? $this->input->post('position') : 1000,
        ];

        return $formValues;
    }

    function urlValidate($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return TRUE;
        }
        $this->form_validation->set_message('urlValidate', 'website name should start with https:// or http:// or www.');
        return FALSE;
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);
        $formInfo = $this->Quicklink_model->getById($id);
        if ($formInfo) {
            $url = base_url('admin/quicklink/update/' . $id);

            $data['form'] = $this->createForm($url, $formInfo, 'Edit');
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

        $formValues = $this->formValidation();

        $id = $this->uri->segment(4);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/quicklink/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            echo json_encode($data);
            exit;
        }

        $orderInfo = $this->Quicklink_model->getById($id);
        if (!$orderInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        //update news
        $this->Quicklink_model->update($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    /**
     * Update Publish status
     * @return json_endcode  data
     */
    public function publish() {
        $id = $this->uri->segment(4);
        $orderInfo = $this->Quicklink_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->Quicklink_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    /**
     * Update Publish status
     * @return json_endcode  data
     */
    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $orderInfo = $this->Quicklink_model->getById($id);
        $delete = $this->Quicklink_model->delete($id);
        if ($delete && $orderInfo) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

}
