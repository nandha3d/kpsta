<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
//session_start(); //we need to start session in order to access it through CI

class AauthGroup extends AppController {

    public function ci3Init(): void {
        parent::ci3Init();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("aauthGroup_model");
    }

    public function index() {
        $data = false;

        $data['form'] = $this->createForm(base_url('admin/aauth/group/add'));
        $data['content'] = $this->getContent();

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $footer['special_js'] = ['js/news.js'];

        $this->load->view('admin/header');
        $this->load->view('admin/aauth/group/group', $data);
        $this->load->view('admin/footer', $footer);
    }

    public function createForm($url, $formValues = false, $title = "Add Latest group") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/aauth/group/add');
        $data['formValues'] = $formValues;
        return $this->load->view('admin/aauth/group/form', $data, TRUE);
    }

    function getContent($param = array()) {
         $this->load->library("pagination");

        $param['limit'] = 10;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->aauthGroup_model->getAllCount($param);
        $config["base_url"] = base_url() . "admin/aauth/group";
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

        $content['list'] = $this->aauthGroup_model->getAll($param);
        return $this->load->view('admin/aauth/group/content', $content, TRUE);
        
    }

    /*
     * This function for Insert latest group
     * @return json_encode response
     */

    public function add() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('definition', 'Definition', 'trim');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'name' => $this->input->post('name'),
            'definition' => $this->input->post('definition'),
        ];
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('admin/aauth/group/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        //Insert values
        $add = $this->aauthGroup_model->add($formValues);
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

        $id = $this->uri->segment(5);

        $groupInfo = $this->aauthGroup_model->getNews($id);
        if ($groupInfo) {
            $url = base_url('admin/aauth/group/update/' . $id);
            $data['form'] = $this->createForm($url, $groupInfo, 'Edit Group');
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

        $id = $this->uri->segment(5);
        $groupInfo = $this->aauthGroup_model->getNews($id);
        if (!$groupInfo) {
            $formValues['error'] = "Record not found!!!";
        }
        //validation FALSE
        if ($this->form_validation->run() == FALSE || !$groupInfo) {
            $data['code'] = 'error';
            $url = base_url('admin/aauth/group/update/' . $id);
            $data['form'] = $this->createForm($url, $groupInfo, 'Edit News');
            echo json_encode($data);
            exit;
        }
        //update group
        $this->aauthGroup_model->update($id, $formValues);
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

            $this->aauthGroup_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo 'false';
        exit;
    }

    public function search() {
        $data['code'] = 'success';
        $data['content'] = $this->getContent();

        echo json_encode($data);
        exit;
    }

    public function delete($id = null) {
        $data['code'] = 'error';
        if (!$id) {
            $id = $this->uri->segment(5) ?: ($this->uri->segment(4) ?: $this->input->post('id'));
        }
        $delete = $this->aauthGroup_model->delete($id);
        if ($delete) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

}
