<?php

namespace App\Controllers\Membership;

use App\Controllers\MembershipController;
//session_start(); //we need to start session in order to access it through CI

class WhatsNew extends MembershipController {

    public function ci3Init(): void {
        parent::ci3Init();

        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            redirect('membership/home');
        }

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("membership/WhatsNew_model", "whatsNew_model");
    }

    public function index() {
        $data = false;

        $data['form'] = $this->createForm(base_url('membership/whats_new/add'));
        $data['content'] = $this->getContent();

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/whatsNew.js'];

        $this->load->view('membership/header', $header);
        $this->load->view('membership/whatsNew/index', $data);
        $this->load->view('membership/footer', $footer);
    }

    public function createForm($url, $formValues = false, $title = "Add news") {
         $this->load->model("membership/AauthGroup_model", "aauthGroup_model");
        $group = $this->aauthGroup_model->getAllGroup();
        $data['groupSelect'] = $this->createSelectDropDown($group, 'name', 'ALL');

        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('membership/whats_new/add');
        $data['formValues'] = $formValues;
        return $this->load->view('membership/whatsNew/newsForm', $data, TRUE);
    }

    function getContent($param = array()) {
        $this->load->library("pagination");
        $param['search'] = trim((string)$this->input->get('search'));

        $param['limit'] = 10;
        $param['offset'] = 0;
        $config["total_rows"] = $this->whatsNew_model->getAllNewsCount($param);

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = ( $temp <= $config["total_rows"] ) ? $temp : $config["total_rows"];

        //pagination 
        $config["base_url"] = base_url() . "membership/whats_new/";
        $config["per_page"] = $param['limit'];
//        $config["uri_segment"] = 3;
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();

        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['listNews'] = $this->whatsNew_model->getAllNews($param);

        return $this->load->view('membership/whatsNew/newsContent', $content, TRUE);
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
            $data['form'] = $this->createForm(base_url('membership/whats_new/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        if (isset($_FILES['file'])) {
            $upload = $this->uploadFile();

            if ($upload['code'] == 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $this->data['error'];
                $data['form'] = $this->createForm(base_url('membership/whats_new/add'), $formValues);
                echo json_encode($data);
                exit;
            }

            $formValues['file_name'] = $upload['data']['file_name'];
        }

        //Insert values
        $formValues['created_at'] = date("Y-m-d H:i:s");
        $add = $this->whatsNew_model->addNews($formValues);
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

    function uploadFile() {
        //upload pdf
        $config['upload_path'] = './' . MEMBERSHIP_PATH;
        $config['allowed_types'] = 'pdf|jpg|jpeg|xls|xlsx|doc|docx|exe|xlsm|zip';
        $config['max_size'] = 0;
//        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);

        if (!is_dir(MEMBERSHIP_PATH)) {
            mkdir(MEMBERSHIP_PATH, 0777, true);
        }


        if (!$this->upload->do_upload('file')) {
            $this->data['error'][] = $this->upload->display_errors();
            $data['code'] = 'error';
            $data['data'] = $this->data['error'];
        } else {
            $data['code'] = 'success';
            $data['data'] = $this->upload->data();
        }

        return $data;
    }

    /*
     * It create html form For editing New
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);

        $newsInfo = $this->whatsNew_model->getNews($id);
        if ($newsInfo) {
            $url = base_url('membership/whats_new/update/' . $id);
            $data['form'] = $this->createForm($url, $newsInfo, 'Edit News');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    function formValidation() {
        $this->form_validation->set_rules('content', 'Content', 'trim|required');
        $this->form_validation->set_rules('publish', 'Publish', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        $formValues = [
            'content' => $this->input->post('content'),
            'publish' => $this->input->post('publish'),
        ];

        $formValues['position'] = $this->input->post('position') ? $this->input->post('position') : 25;
        $formValues['group_id'] = $this->input->post('group_id') ? $this->input->post('group_id') : 0;
        return $formValues;
    }

    /**
     * Update News info
     * @return json_endcode  data
     */
    public function update() {
        $formValues = $this->formValidation();
        $id = $this->uri->segment(4);
        $news = $this->whatsNew_model->getNews($id);
        if (!$news) {
            $formValues['error'] = "Record not found!!!";
        }
        //validation FALSE
        if ($this->form_validation->run() == FALSE || !$news) {
            $data['code'] = 'error';
            $url = base_url('membership/whats_new/update/' . $id);
            $data['form'] = $this->createForm($url, $news, 'Edit News');
            echo json_encode($data);
            exit;
        }


        if ($news['file_name']) {
            $this->deleteFile(MEMBERSHIP_PATH . $news['file_name']);
        }

        if (isset($_FILES['file'])) {
            $upload = $this->uploadFile();

            if ($upload['code'] == 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $this->data['error'];
                $data['form'] = $this->createForm(base_url('membership/whats_new/add'), $formValues);
                echo json_encode($data);
                exit;
            }
            $formValues['file_name'] = $upload['data']['file_name'];
        }
        //update news
        $this->whatsNew_model->update($id, $formValues);
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

            $this->whatsNew_model->publish($id, $publish);
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

    public function fileRemove() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $news = $this->whatsNew_model->getNews($id);
        $deleteFile = $this->whatsNew_model->deleteFile($id);
        if ($deleteFile && $this->deleteFile(MEMBERSHIP_PATH . $news['file_name'])) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $news = $this->whatsNew_model->getNews($id);
        if ($news['file_name']) {
            $this->deleteFile(MEMBERSHIP_PATH . $news['file_name']);
        }
        $delete = $this->whatsNew_model->delete($id);
        if ($delete && $news) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

}
