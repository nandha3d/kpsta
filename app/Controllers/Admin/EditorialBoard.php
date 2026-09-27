<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;

class EditorialBoard extends AppController {

    public function ci3Init(): void {
        parent::ci3Init();

        // Load helpers & libraries
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');

        // Load model
        $this->load->model("EditorialBoard_model");
    }

    public function index() {
        $data = [];

        // Check if request is AJAX
        if ($this->input->is_ajax_request()) {
            $data['content'] = $this->getContent();
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $data['content'] = $this->getContent();
        $data['form'] = $this->createForm(base_url('admin/editorial_board/add'));
        $data['designations'] = $this->EditorialBoard_model->getDistinctDesignations();

        $header['page_title'] = 'Editorial Board Members';
        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/editorialBoard/editorialBoard', $data);
        $this->load->view('admin/footer', $footer);
    }

    public function getContent($param = []) {
        $this->load->library("pagination");

        $param['limit'] = 15;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['designation'] = trim((string)$this->input->get('designation'));
        $param['sort'] = trim((string)$this->input->get('sort'));
        if (empty($param['sort'])) {
            $param['sort'] = 'position-asc';
        }

        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        // Pagination Config
        $config["total_rows"] = $this->EditorialBoard_model->getAllCount($param);
        $config["base_url"] = base_url('admin/editorial_board');
        $config["per_page"] = $param['limit'];

        $configBootstrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootstrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = ($temp <= $config["total_rows"]) ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $this->newUrl = $this->getNewUrl([
            'page' => $param['page'],
            'search' => $param['search'],
            'designation' => $param['designation'],
            'sort' => $param['sort']
        ]);

        $content['members'] = $this->EditorialBoard_model->getAll($param);
        return $this->load->view('admin/editorialBoard/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add Member") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/editorial_board/add');
        $data['formValues'] = $formValues;
        $data['existingDesignations'] = $this->EditorialBoard_model->getDistinctDesignations();

        return $this->load->view('admin/editorialBoard/form', $data, TRUE);
    }

    public function add() {
        $formValues = $this->formValidation();

        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $url = base_url('admin/editorial_board/add');
            $data['form'] = $this->createForm($url, $formValues, 'Add Member');
            echo json_encode($data);
            exit;
        }

        $add = $this->EditorialBoard_model->add($formValues);
        if ($add) {
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
            $data['message'] = 'Editorial board member added successfully';
        } else {
            $data['code'] = 'error';
            $data['message'] = 'Unable to add member, please try again';
        }
        echo json_encode($data);
        exit;
    }

    public function edit($id = null) {
        $data['code'] = 'error';
        $id = $id ?: $this->uri->segment(4);
        $member = $this->EditorialBoard_model->getById($id);
        if ($member) {
            $url = base_url('admin/editorial_board/update/' . $id);
            $data['form'] = $this->createForm($url, $member, 'Edit Member');
            $data['code'] = 'success';
        } else {
            $data['message'] = 'Member not found';
        }
        echo json_encode($data);
        exit;
    }

    public function update($id = null) {
        $formValues = $this->formValidation();
        $id = $id ?: $this->uri->segment(4);

        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/editorial_board/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit Member');
            echo json_encode($data);
            exit;
        }

        $member = $this->EditorialBoard_model->getById($id);
        if (!$member) {
            $data['code'] = 'error';
            $data['message'] = 'Member not found';
            echo json_encode($data);
            exit;
        }

        $this->EditorialBoard_model->update($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['message'] = 'Member updated successfully';

        echo json_encode($data);
        exit;
    }

    public function publish($id = null) {
        $id = $id ?: $this->uri->segment(4);
        $member = $this->EditorialBoard_model->getById($id);
        if ($id && $member) {
            $publish = ($member['is_publish'] == 1) ? 0 : 1;
            $this->EditorialBoard_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    public function delete($id = null) {
        $data['code'] = 'error';
        $id = $id ?: $this->uri->segment(4);
        $member = $this->EditorialBoard_model->getById($id);
        if ($member) {
            $this->EditorialBoard_model->delete($id);
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
            $data['message'] = 'Member deleted successfully';
        }
        echo json_encode($data);
        exit;
    }

    public function batchDelete() {
        $result = $this->runBatchDelete(function ($id) {
            return $this->EditorialBoard_model->delete($id);
        });
        $result['content'] = $this->getContent();
        echo json_encode($result);
        exit;
    }

    protected function formValidation() {
        $this->form_validation->set_rules('name', 'Member Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'Designation / Member Position', 'trim|required');
        $this->form_validation->set_rules('position', 'Display Order', 'trim|numeric');
        $this->form_validation->set_rules('is_publish', 'Publish Status', 'trim');

        $this->form_validation->set_error_delimiters("<p class='text-danger'>", "</p>");

        $pos = $this->input->post('position');
        return [
            'name' => trim((string)$this->input->post('name')),
            'designation' => trim((string)$this->input->post('designation')),
            'phone' => trim((string)$this->input->post('phone')),
            'email' => trim((string)$this->input->post('email')),
            'position' => ($pos !== '' && $pos !== null) ? (int)$pos : 1000,
            'is_publish' => $this->input->post('is_publish') !== null ? (int)$this->input->post('is_publish') : 1,
        ];
    }
}
