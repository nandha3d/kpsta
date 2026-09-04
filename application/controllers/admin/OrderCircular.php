<?php

Class OrderCircular extends MY_Controller {

    private $segment3;

    public function __construct() {
        parent::__construct();
// Load form helper library
        $this->load->helper(array('form', 'url'));
// Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("OrderCircular_model");

        $this->segment3 = $this->uri->segment(3);
    }

    public function index() {
        $data = array();
        $segment = $this->getType($this->uri->segment(3));
        $data['contentTitle'] = $segment['contentTitle'];

        $data['content'] = $this->getContent();


        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $url = base_url('admin/order-circular/' . $this->segment3 . '/add');
        $data['form'] = $this->createForm($url);

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/order_circular.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/orderCircular/general', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {
        $content['seg3'] = $this->uri->segment(3);
        $segment = $this->getType($content['seg3']);
        $this->load->library("pagination");

        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));

        $param['type'] = $segment['type'];
        $param['limit'] = 10;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;
        $config["total_rows"] = $this->OrderCircular_model->getAllByTypeCount($param);

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];

        //pagination 
        $config["base_url"] = base_url() . "admin/order-circular/" . $content['seg3'];
        $config["per_page"] = $param['limit'];
        //$config["uri_segment"] = 5;
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();

        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['orders'] = $this->OrderCircular_model->getAllByType($param);

        $this->newUrl = $this->getNewUrl([ 'page' => $param['page']]);

        return $this->load->view('admin/orderCircular/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add Order || circular") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/order-circular/' . $this->segment3 . '/add');
        $data['formValues'] = $formValues;
        $data['category'] = $this->OrderCircular_model->getAllCategory();
        $data['category'] = ['' => '- - - SELECT CATEGORY - - -'] + $data['category'];

        return $this->load->view('admin/orderCircular/form', $data, TRUE);
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
            $url = base_url('admin/order-circular/' . $this->segment3 . '/add');
            $data['form'] = $this->createForm($url, $formValues);
            echo json_encode($data);
            exit;
        }


        //check upload type
        //for pdf, move the pdf from temp to orginal folder
        $pdfName = $this->input->post('pdfName');
        if ($formValues['upload_type'] == 'file') {
//            rename(FILE_UPLOAD_PATH_TEMP . $pdfName, ORDER_CIRCULAR_PATH . $pdfName);
            $formValues['path'] = $pdfName;
        } else {
            //remove file from temp if exist
//            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;
            $file = ORDER_CIRCULAR_PATH . $pdfName;
            if ($pdfName && is_readable($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {
                    
                }
            }
        }



        //check order type [eg: General, hsse, vhse]
        $segment = $this->getType($this->uri->segment(3));
        $formValues['type'] = $segment['type'];
        //Insert values
        $add = $this->OrderCircular_model->add($formValues);
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
        $this->form_validation->set_rules('date', 'Date', 'trim|required');
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

    
    /**
     * This function used to upload FILE
     * 
     * 
     */
    public function fileUpload() {
        //Intially file was upload to Temp. while save the temp file moved to original foler
        //To change file into original file name, temp folder is avoided from this, so now file is uploaded directly to the original folder
        $config['upload_path'] = './' . ORDER_CIRCULAR_PATH;
//        $config['upload_path'] = './' . FILE_UPLOAD_PATH_TEMP;
        $config['allowed_types'] = 'pdf|jpg|jpeg|xls|xlxs|doc|docx|zip';
        $config['max_size'] = 0;
//        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);

//        if (!is_dir(FILE_UPLOAD_PATH_TEMP)) {
//            mkdir(FILE_UPLOAD_PATH_TEMP, 0777, true);
//        }
        if (!is_dir(ORDER_CIRCULAR_PATH)) {
            mkdir(ORDER_CIRCULAR_PATH, 0777, true);
        }
        //check if exits already . then remove the file
        $pdfName = posted_filename('pdfName');
        if ($pdfName !== '') {
//            $this->deleteFile(FILE_UPLOAD_PATH_TEMP . $pdfName);

            $this->deleteFile(ORDER_CIRCULAR_PATH . $pdfName);
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
        $this->deleteFile(FILE_UPLOAD_PATH_TEMP . $pdf);

        $this->deleteFile(ORDER_CIRCULAR_PATH . $pdf);

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

    function getType($url) {
        switch ($url) {
            case 'general':
                $data['type'] = 1;
                $data['contentTitle'] = 'General';
                break;
            case 'hse':
                $data['type'] = 2;
                $data['contentTitle'] = 'HSE';
                break;
            case 'vhse':
                $data['type'] = 3;
                $data['contentTitle'] = 'VHSE';
                break;

            default:
                $data['type'] = 0;
                $data['contentTitle'] = 'Order & Circular';
                break;
        }
        return $data;
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(5);
        $orderInfo = $this->OrderCircular_model->getById($id);
        if ($orderInfo) {
            $orderInfo['pdfName'] = $orderInfo['path'];
            $orderInfo['path'] = $orderInfo['upload_type'] != "file" ? $orderInfo['path'] : '';

            $seg3 = $this->uri->segment(3);
            $url = base_url('admin/order-circular/' . $seg3 . '/update/' . $id);


            $data['form'] = $this->createForm($url, $orderInfo, 'Edit Order || circular');
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

        $id = $this->uri->segment(5);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $seg3 = $this->uri->segment(3);
            $url = base_url('admin/order-circular/' . $seg3 . '/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit Order || circular');
            echo json_encode($data);
            exit;
        }

        $orderInfo = $this->OrderCircular_model->getById($id);
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
//                rename(FILE_UPLOAD_PATH_TEMP . $pdfName, ORDER_CIRCULAR_PATH . $pdfName);
            }
            $formValues['path'] = $pdfName;
        } else if ($formValues['upload_type'] == 'url' && $orderInfo['upload_type'] == 'file') {
            //remove Already  exist
            $file = ORDER_CIRCULAR_PATH . $pdfName;
            if ($pdfName && is_readable($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {
                    
                }
            }
        } else {
            //remove file from temp if exist
            $file = ORDER_CIRCULAR_PATH . $pdfName;
//            $file = FILE_UPLOAD_PATH_TEMP . $pdfName;
            if ($pdfName && is_readable($file)) {
                try {
                    unlink($file);
                } catch (\Exception $exc) {
                    
                }
            }
        }

        //update news
        $this->OrderCircular_model->update($id, $formValues);
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
        $id = $this->uri->segment(5);
        $orderInfo = $this->OrderCircular_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->OrderCircular_model->publish($id, $publish);
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
        $id = $this->uri->segment(5);
        if ($this->deleteOne($id)) {
            $data['content'] = $this->getContent();
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

        $data['form'] = $this->createCategoryForm(base_url('admin/order-circular/category/add'));
        $data['content'] = $this->getCategoryContent();

        $this->load->view('admin/header');
        $this->load->view('admin/orderCircular/category/category', $data);
        $this->load->view('admin/footer');
    }

    public function createCategoryForm($url, $formValues = false, $title = "Add Category") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/order-circular/category/add');
        $data['formValues'] = $formValues;
        return $this->load->view('admin/orderCircular/category/form', $data, TRUE);
    }

    public function categoryAdd() {

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $formValues = ['name' => $this->input->post('name')];

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm(base_url('admin/order-circular/category/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        //Insert values
        $add = $this->OrderCircular_model->categoryAdd($formValues);
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
        $content['categories'] = $this->OrderCircular_model->getAllcategory();
        return $this->load->view('admin/orderCircular/category/content', $content, TRUE);
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function categoryEdit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(5);
        $categoryInfo = $this->OrderCircular_model->getCategoryById($id);

        if ($categoryInfo) {

            $url = base_url('admin/order-circular/category/update/' . $id);
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
            $url = base_url('admin/order-circular/category/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createCategoryForm($url, $formValues, 'Edit Category');
            echo json_encode($data);
            exit;
        }

        $categoryInfo = $this->OrderCircular_model->getCategoryById($id);
        if (!$categoryInfo) {
            $data['code'] = 'error';
            $data['message'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }



        //update category
        $this->OrderCircular_model->categoryUpdate($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getCategoryContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }


    /**
     * Remove one row plus its uploaded file. Shared by the row Delete button
     * and the "Delete Selected" toolbar action.
     */
    protected function deleteOne($id) {
        $orderInfo = $this->OrderCircular_model->getById($id);
        if (!$orderInfo || !$this->OrderCircular_model->delete($id)) {
            return FALSE;
        }
        if ($orderInfo['upload_type'] == "file") {
            $this->deleteFile(ORDER_CIRCULAR_PATH . '/' . $orderInfo['path']);
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


