<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
/**
 * This controller used as common for Different menu and pages 
 * Refer the getMenuType method to know about it
 */
class Download extends AppController {

    private $menuType;

    public function ci3Init(): void {
        parent::ci3Init();
        // Load form helper library
        $this->load->helper(array('form', 'url'));
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("Download_model");

        $this->menuType = $this->getMenuType($this->getSegment3());
    }

    function getSegment3() {
        if (in_array($this->uri->segment(2), ['notice_poster', 'melakal', 'official_outlook', 'memorandums'])) {
            return $this->uri->segment(2);
        } else {
            return $this->uri->segment(3);
        }
    }

    function getSegment5() {
        if (in_array($this->uri->segment(2), ['notice_poster', 'melakal', 'official_outlook', 'memorandums'])) {
            return $this->uri->segment(4);
        } else {
            return $this->uri->segment(5);
        }
    }

    public function index() {
        $data = array();

        $segment = $this->getMenuType($this->getSegment3());
        $data['contentTitle'] = $segment['contentTitle'];

        $data['content'] = $this->getContent();

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $data['form'] = $this->createForm(base_url('admin/' . $this->menuType['route'] . '/add'));

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/download.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/download/download', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {

        $this->load->library("pagination");
        $content['menuType'] = $this->menuType;

        $param['type'] = $this->menuType['type'];
        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));
        $param['limit'] = 20;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->Download_model->getAllCount($param);
        $config["base_url"] = base_url() . "admin/" . $this->menuType['route'];
        $config["per_page"] = $param['limit'];

        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();


        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $this->newUrl = $this->getNewUrl([
            'page' => $param['page']
        ]);

        $content['orders'] = $this->Download_model->getAll($param);
        return $this->load->view('admin/download/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/' . $this->menuType['route'] . '/add');
        $data['formValues'] = $formValues;
        $data['menuType'] = $this->menuType;
        $data['category'] = $this->Download_model->getAllCategory();
        $data['category'] = ['' => '- - - SELECT CATEGORY - - -'] + $data['category'];

        return $this->load->view('admin/download/form', $data, TRUE);
    }

    function fileNameValidate($val) {
        if (!$val) {
            $this->form_validation->set_message('fileNameValidate', 'Pls upload PDF file ');
            return FALSE;
        }
    }

    function urlValidate($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return TRUE;
        }
        $this->form_validation->set_message('urlValidate', 'website name should start with https:// or http:// or www.');
        return FALSE;
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
            $url = base_url('admin/' . $this->menuType['route'] . '/add');
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
                $formValues['path'] = $pdfName;
                if (is_readable($file)) {
                    //rename(FILE_UPLOAD_PATH_TEMP . $pdfName, DOWNLOAD_PATH . $pdfName);
                } else {
                    
                }
            } else {
                //remove file from temp if exist
                if (is_readable($file)) {
                    unlink($file);
                }
            }
        }


        //check Download Type
        $segment = $this->getMenuType($this->getSegment3());
        $formValues['type'] = $segment['type'];

        if (!empty($formValues['category'])) {
            $formValues['category'] = $this->Download_model->getOrAddCategory($formValues['category']);
        }

        //Insert values
        $add = $this->Download_model->add($formValues);
        if ($add) {
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
        } else {
            $data['code'] = 'error';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function formValidation() {
        $this->form_validation->set_rules('category', 'category', 'trim');
        //$this->form_validation->set_rules('date', 'Date', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('upload_type', 'Upload Type', 'trim|required');
        $this->form_validation->set_rules('is_publish', 'Publish', 'trim|required');

        if ($this->input->post('upload_type') == 'url') {
            $this->form_validation->set_rules('path', 'Website URL ', 'trim|required|callback_urlValidate');
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
        $config['upload_path'] = './' . DOWNLOAD_PATH;
        $config['allowed_types'] = 'pdf|jpg|jpeg|xls|xlsx|doc|docx|exe|xlsm';
        $config['max_size'] = 0;
//        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);

        if (!is_dir(FILE_UPLOAD_PATH_TEMP)) {
            mkdir(FILE_UPLOAD_PATH_TEMP, 0777, true);
        }
        if (!is_dir(DOWNLOAD_PATH)) {
            mkdir(DOWNLOAD_PATH, 0777, true);
        }
        //check if exits already . then remove the file
        $pdfName = posted_filename('pdfName');

        if ($pdfName !== '') {
            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;
            if (is_file($file)) {
                unlink($file);
            }

            $file = DOWNLOAD_PATH . $pdfName;
            if (is_file($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {

                }
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

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->getSegment5();
        $formInfo = $this->Download_model->getById($id);
        if ($formInfo) {
            $formInfo['pdfName'] = $formInfo['path'];
            $formInfo['path'] = $formInfo['upload_type'] != "file" ? $formInfo['path'] : '';

            $url = base_url('admin/' . $this->menuType['route'] . '/update/' . $id);

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

        $id = $this->getSegment5();
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/' . $this->menuType['route'] . '/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            echo json_encode($data);
            exit;
        }

        $orderInfo = $this->Download_model->getById($id);
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
//                rename(FILE_UPLOAD_PATH_TEMP . $pdfName, DOWNLOAD_PATH . $pdfName);
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

        if (!empty($formValues['category'])) {
            $formValues['category'] = $this->Download_model->getOrAddCategory($formValues['category']);
        }

        //update news
        $this->Download_model->update($id, $formValues);
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
        $id = $this->getSegment5();
        $orderInfo = $this->Download_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->Download_model->publish($id, $publish);
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
        $id = $this->getSegment5();
        if ($this->deleteOne($id)) {
            $data['code'] = 'success';
            $data['lastId'] = $id;
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

    ############################################
    ## Add and Edit category                  ##
    ############################################

    public function category() {

        $data = array();

        $data['form'] = $this->createCategoryForm(base_url('admin/' . $this->menuType['route'] . '/category/add'));
        $data['content'] = $this->getCategoryContent();

        $this->load->view('admin/header');
        $this->load->view('admin/download/category/category', $data);
        $this->load->view('admin/footer');
    }

    public function createCategoryForm($url, $formValues = false, $title = "Add Category") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/' . $this->menuType['route'] . '/category/add');
        $data['formValues'] = $formValues;
        return $this->load->view('admin/download/category/form', $data, TRUE);
    }

    public function categoryAdd() {

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $formValues = ['name' => $this->input->post('name')];

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm(base_url('admin/' . $this->menuType['route'] . '/category/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        //Insert values
        $add = $this->Download_model->categoryAdd($formValues);
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
        $content['categories'] = $this->Download_model->getAllcategory();
        return $this->load->view('admin/download/category/content', $content, TRUE);
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function categoryEdit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(5);
        $categoryInfo = $this->Download_model->getCategoryById($id);

        if ($categoryInfo) {

            $url = base_url('admin/' . $this->menuType['route'] . '/category/update/' . $id);
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
            $url = base_url('admin/' . $this->menuType['route'] . '/category/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm($url, $formValues, 'Edit Category');
            echo json_encode($data);
            exit;
        }

        $categoryInfo = $this->Download_model->getCategoryById($id);
        if (!$categoryInfo) {
            $data['code'] = 'error';
            $data['message'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        $this->Download_model->categoryUpdate($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getCategoryContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    function getMenuType($url) {
        switch ($url) {
            case 'act_rules':
                $data['type'] = 1;
                $data['contentTitle'] = 'Act & Rules';
                $data['route'] = 'download/' . $this->getSegment3();
                $data['category'] = false;
                break;
            case 'softwares':
                $data['type'] = 2;
                $data['contentTitle'] = 'Software';
                $data['route'] = 'download/' . $this->getSegment3();
                $data['category'] = false;
                break;
            case 'fonts':
                $data['type'] = 3;
                $data['contentTitle'] = 'Fonts';
                $data['route'] = 'download/' . $this->getSegment3();
                $data['category'] = false;
                break;
            case 'forms':
                $data['type'] = 4;
                $data['contentTitle'] = 'Forms';
                $data['route'] = 'download/' . $this->getSegment3();
                $data['category'] = true;
                break;
            case 'notice_poster':
                $data['type'] = 5;
                $data['contentTitle'] = 'Notics & Posters';
                $data['route'] = 'notice_poster';
                $data['category'] = false;
                break;

            case 'melakal':
                $data['type'] = 6;
                $data['contentTitle'] = 'Melakal';
                $data['route'] = 'melakal';
                $data['category'] = true;
                break;

            case 'memorandums':
            case 'official_outlook':
                $data['type'] = 7;
                $data['contentTitle'] = 'Memorandums';
                $data['route'] = $this->uri->segment(2) === 'official_outlook' ? 'official_outlook' : 'memorandums';
                $data['category'] = false;
                break;
            case 'academic_corner':
                $data['type'] = 8;
                $data['contentTitle'] = 'Academic Corner';
                $data['route'] = 'download/' . $this->getSegment3();
                $data['category'] = true;
                break;    


            default:
                $data['type'] = 0;
                $data['contentTitle'] = 'Downloads';
                $data['route'] = 'download';
                $data['category'] = true;
                break;
        }
        return $data;
    }


    /**
     * Remove one row plus its uploaded file. Shared by the row Delete button
     * and the "Delete Selected" toolbar action.
     */
    protected function deleteOne($id) {
        $orderInfo = $this->Download_model->getById($id);
        if (!$orderInfo || !$this->Download_model->delete($id)) {
            return FALSE;
        }
        if ($orderInfo['upload_type'] == "file") {
            $this->deleteFile(DOWNLOAD_PATH . '/' . $orderInfo['path']);
        }
        return TRUE;
    }

    public function batchDelete() {
        $result = $this->runBatchDelete(function ($id) {
            return $this->deleteOne($id);
        });
        $result['content'] = $this->getContent();
        echo json_encode($result);
        exit;
    }

}
