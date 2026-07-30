<?php

Class Forms extends MY_Controller {

    public function __construct() {
        parent::__construct();
// Load form helper library
        $this->load->helper(array('form', 'url'));
// Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("Forms_model");
    }

    public function index() {
        $data = array();

        $data['content'] = $this->getContent();

        $url = base_url('admin/forms/add');
        $data['form'] = $this->createForm($url);

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/forms.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/forms/forms', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {
        $this->load->library("pagination");

        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));

        $param['limit'] = 10;
        if ($this->uri->segment(3) == 'search') {
            $param['offset'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        } else {
            $param['offset'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }
        $config["total_rows"] = $this->Forms_model->getAllCount($param);

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];

        //pagination 
        $config["base_url"] = base_url() . "admin/forms/";
        $config["per_page"] = $param['limit'];
        //$config["uri_segment"] = 5;
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();

        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['orders'] = $this->Forms_model->getAll($param);

        return $this->load->view('admin/forms/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add Form / Poster") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/forms/add');
        $data['formValues'] = $formValues;
        $data['category'] = $this->Forms_model->getAllCategory();
        $data['category'] = ['' => '- - - SELECT CATEGORY - - -'] + $data['category'];

        return $this->load->view('admin/forms/form', $data, TRUE);
    }

    function fileNameValidate($val) {
        if (!$val) {
            $this->form_validation->set_message('fileNameValidate', 'Pls upload PDF file ');
            return FALSE;
        }
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
            $url = base_url('admin/forms/add');
            $data['form'] = $this->createForm($url, $formValues);
            echo json_encode($data);
            exit;
        }


        //check upload type
        //for pdf, move the pdf from temp to orginal folder
        $pdfName = $this->input->post('pdfName');
        if ($pdfName) {
            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;

            if ($formValues['upload_type'] == 'file') {
                if (is_readable($file)) {
                    rename(FILE_UPLOAD_PATH_TEMP . $pdfName, DOWNLOAD_PATH . $pdfName);
                    $formValues['path'] = $pdfName;
                } else {
                    
                }
            } else {
                //remove file from temp if exist
                if (is_readable($file)) {
                    unlink($file);
                }
            }
        }



        //Insert values
        $add = $this->Forms_model->add($formValues);
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
        $this->form_validation->set_rules('category', 'category', 'trim|required');
        //$this->form_validation->set_rules('date', 'Date', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('upload_type', 'Upload Type', 'trim|required');
        $this->form_validation->set_rules('is_publish', 'Publish', 'trim|required');

        if ($this->input->post('upload_type') == 'url') {
            $this->form_validation->set_rules('path', 'Website URL ', 'trim|required');
        } else if ($this->input->post('upload_type') == 'file') {
            $this->form_validation->set_rules('pdfName', 'upload PDF', 'trim|callback_fileNameValidate');
        }

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'category' => $this->input->post('category'),
            'date' => $this->input->post('date'),
            'description' => $this->input->post('description'),
            'upload_type' => $this->input->post('upload_type'),
            'path' => $this->input->post('path'),
            'pdfName' => $this->input->post('pdfName'),
            'is_publish' => $this->input->post('is_publish'),
        ];



        return $formValues;
    }

    public function fileUpload() {
        //upload pdf
        $config['upload_path'] = './' . FILE_UPLOAD_PATH_TEMP;
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);

        if (!is_dir(FILE_UPLOAD_PATH_TEMP)) {
            mkdir(FILE_UPLOAD_PATH_TEMP, 0777, true);
        }
        if (!is_dir(DOWNLOAD_PATH)) {
            mkdir(DOWNLOAD_PATH, 0777, true);
        }
        //check if exits already . then remove the file
        $file = FILE_UPLOAD_PATH_TEMP . $_POST['pdfName'];
        if ($_POST['pdfName'] && is_file($file)) {
            unlink($file);
        }

        if (is_file(DOWNLOAD_PATH . $_POST['pdfName'])) {
            unlink(DOWNLOAD_PATH . $_POST['pdfName']);
        }

        $file = DOWNLOAD_PATH . $_POST['pdfName'];
        if ($_POST['pdfName'] && is_file($file)) {
            try {
                unlink($file);
            } catch (\Exception $exc) {
                
            }
        }

        if (!$this->upload->do_upload('file')) {
            $error_msg = $this->upload->display_errors();
            $data['code'] = 'error';
            $data['data'] = $error_msg;
        } else {
            $data['code'] = 'success';
            $data['data'] = $this->upload->data();
        }
        echo json_encode($data);
        exit;
    }

    public function fileRemove() {
        $pdf = $this->input->post('pdfName');
        //check if exits already . then remove the file
        $file = FILE_UPLOAD_PATH_TEMP . $pdf;
        if ($pdf && is_file($file)) {
            try {
                unlink($file);
            } catch (\Exception $exc) {
                
            }
        }
        $file = DOWNLOAD_PATH . $pdf;
        if ($pdf && is_file($file)) {
            try {
                unlink($file);
            } catch (\Exception $exc) {
                
            }
        }

        $data['code'] = 'success';
        echo json_encode($data);
        exit;
    }

    public function search() {


        $data['code'] = 'success';
        $data['content'] = $this->getContent();
        echo json_encode($data);
        exit;
    }

    

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);
        $formInfo = $this->Forms_model->getById($id);
        if ($formInfo) {
            $formInfo['pdfName'] = $formInfo['path'];
            $formInfo['path'] = $formInfo['upload_type'] != "file" ? $formInfo['path'] : '';

            $url = base_url('admin/forms/update/' . $id);

            $data['form'] = $this->createForm($url, $formInfo, 'Edit Form');
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
            $url = base_url('admin/forms/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit Forms');
            echo json_encode($data);
            exit;
        }

        $orderInfo = $this->Forms_model->getById($id);
        if (!$orderInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }


        //check upload type
        //for pdf, move the pdf from temp to orginal folder
        $pdfName = $this->input->post('pdfName');
        if ($formValues['upload_type'] == 'file') {
            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;
            if ($pdfName && is_readable($file)) {
                rename(FILE_UPLOAD_PATH_TEMP . $pdfName, DOWNLOAD_PATH . $pdfName);
            }
            $formValues['path'] = $pdfName;
        } else if ($formValues['upload_type'] == 'url' && $orderInfo['upload_type'] == 'file') {
            //remove Already  exist
            $file = DOWNLOAD_PATH . $pdfName;
            if ($pdfName && is_readable($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {
                    
                }
            }
        } else {
            //remove file from temp if exist
            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;
            if ($pdfName && is_readable($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {
                    
                }
            }
        }

        //update news
        $this->Forms_model->update($id, $formValues);
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
        $orderInfo = $this->Forms_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->Forms_model->publish($id, $publish);
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
        $orderInfo = $this->Forms_model->getById($id);
        if ($id && $orderInfo) {
            $this->Forms_model->delete($id);
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }

    ############################################
    ## Add and Edit category                  ##
    ############################################

    public function category() {

        $data = array();

        $data['form'] = $this->createCategoryForm(base_url('admin/forms/category/add'));
        $data['content'] = $this->getCategoryContent();

        $this->load->view('admin/header');
        $this->load->view('admin/forms/category/category', $data);
        $this->load->view('admin/footer');
    }

    public function createCategoryForm($url, $formValues = false, $title = "Add Category") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/forms/category/add');
        $data['formValues'] = $formValues;
        return $this->load->view('admin/forms/category/form', $data, TRUE);
    }

    public function categoryAdd() {

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $formValues = ['name' => $this->input->post('name')];

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm(base_url('admin/forms/category/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        //Insert values
        $add = $this->Forms_model->categoryAdd($formValues);
        if ($add) {
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getCategoryContent();
        } else {
            $data['code'] = 'success';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function getCategoryContent() {
        $content['categories'] = $this->Forms_model->getAllcategory();
        return $this->load->view('admin/forms/category/content', $content, TRUE);
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function categoryEdit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(5);
        $categoryInfo = $this->Forms_model->getCategoryById($id);

        if ($categoryInfo) {

            $url = base_url('admin/forms/category/update/' . $id);
            $data['form'] = $this->createCategoryForm($url, $categoryInfo, 'Edit Category');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update News info
     * @return json_endcode  data
     */
    public function categoryUpdate() {


        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $formValues = ['name' => $this->input->post('name')];

        $id = $this->uri->segment(5);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/forms/category/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm($url, $formValues, 'Edit Category');
            echo json_encode($data);
            exit;
        }

        $categoryInfo = $this->Forms_model->getCategoryById($id);
        if (!$categoryInfo) {
            $data['code'] = 'error';
            $data['message'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }



        //update category
        $this->Forms_model->categoryUpdate($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getCategoryContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

}


