<?php

//session_start(); //we need to start session in order to access it through CI

Class News extends MY_Controller {

    public function __construct() {
        parent::__construct();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("news_model");
    }

    public function index() {
        $data = false;

        $data['form'] = $this->createForm(base_url('admin/news/add'));
        $data['content'] = $this->getContent();

        $footer['special_js'] = ['js/news.js'];
        
        $this->load->view('admin/header');
        $this->load->view('admin/news/news', $data);
        $this->load->view('admin/footer', $footer);
    }

    public function createForm($url, $formValues = false, $title = "Add Latest news", $error = null) {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/news/add');
        $data['formValues'] = $formValues;
        if ($error) $data['error'] = $error;
        return $this->load->view('admin/news/newsForm', $data, TRUE);
    }

    function getContent($param = array()) {
        $this->load->library("pagination");
        $param['search'] = trim((string)$this->input->get('search'));

        $param['limit'] = 10;
        if ($this->uri->segment(3) == 'search') {
            $param['offset'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        } else {
            $param['offset'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }
        $config["total_rows"] = $this->news_model->getAllNewsCount($param);

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = ( $temp <= $config["total_rows"] ) ? $temp : $config["total_rows"];

        //pagination 
        $config["base_url"] = base_url() . "admin/news/";
        $config["per_page"] = $param['limit'];
//        $config["uri_segment"] = 3;
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();

        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['listNews'] = $this->news_model->getAllNews($param);

        return $this->load->view('admin/news/newsContent', $content, TRUE);
    }

    /*
     * This function for Insert latest news
     * @return json_encode response
     */

    public function add() {
        $this->form_validation->set_rules('heading', 'Heading', 'trim|required');
        $this->form_validation->set_rules('content', 'Content', 'trim|required');
        $this->form_validation->set_rules('publish', 'Publish', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        
        //set all form values into array
        $formValues = [
            'heading' => $this->input->post('heading'),
            'content' => $this->input->post('content'),
            'publish' => $this->input->post('publish')
        ];
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('admin/news/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        // Image Upload
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path']          = './uploads/news/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
            $config['max_size']             = 5000;
            $config['encrypt_name']         = TRUE;
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $formValues['image'] = $upload_data['file_name'];
            } else {
                $data['code'] = 'error';
                $data['form'] = $this->createForm(base_url('admin/news/add'), $formValues, 'Add Latest news', $this->upload->display_errors('',''));
                echo json_encode($data);
                exit;
            }
        }

        //Insert values
        $formValues['created_at'] = date("Y-m-d H:i:s");
        $add = $this->news_model->addNews($formValues);
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

        $newsInfo = $this->news_model->getNews($id);
        if ($newsInfo) {
            $url = base_url('admin/news/update/' . $id);
            $data['form'] = $this->createForm($url, $newsInfo, 'Edit News');
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
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->form_validation->set_rules('heading', 'Heading', 'trim|required');
        $this->form_validation->set_rules('content', 'Content', 'trim|required');
        $this->form_validation->set_rules('publish', 'Publish', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        $formValues = [
            'heading' => $this->input->post('heading'),
            'content' => $this->input->post('content'),
            'publish' => $this->input->post('publish'),
        ];

        $id = $this->uri->segment(4);
        $newsInfo = $this->news_model->getNews($id);
        if (!$newsInfo) {
            $formValues['error'] = "Record not found!!!";
        }
        //validation FALSE
        if ($this->form_validation->run() == FALSE || !$newsInfo) {
            $data['code'] = 'error';
            $url = base_url('admin/news/update/' . $id);
            $data['form'] = $this->createForm($url, $newsInfo, 'Edit News');
            echo json_encode($data);
            exit;
        }

        // Image Upload
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path']          = './uploads/news/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
            $config['max_size']             = 5000;
            $config['encrypt_name']         = TRUE;
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $formValues['image'] = $upload_data['file_name'];
                
                // Delete old image
                if (!empty($newsInfo['image']) && file_exists('./uploads/news/' . $newsInfo['image'])) {
                    unlink('./uploads/news/' . $newsInfo['image']);
                }
            } else {
                $data['code'] = 'error';
                $url = base_url('admin/news/update/' . $id);
                $data['form'] = $this->createForm($url, $newsInfo, 'Edit News', $this->upload->display_errors('',''));
                echo json_encode($data);
                exit;
            }
        }

        //update news
        $this->news_model->update($id, $formValues);
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

            $this->news_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
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
        $orderInfo = $this->news_model->getNews($id);
        $delete = $this->news_model->delete($id);
        if ($delete && $orderInfo) {
            // Delete image file if exists
            if (!empty($orderInfo['image']) && file_exists('./uploads/news/' . $orderInfo['image'])) {
                unlink('./uploads/news/' . $orderInfo['image']);
            }
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

}
