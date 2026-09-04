<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
//session_start(); //we need to start session in order to access it through CI

class AdayapakaSabham extends AppController {

    private $imageWidthThumb, $imageHeightThumb;

    public function ci3Init(): void {
        parent::ci3Init();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("adayapakaSabham_model");


        $this->imageWidthThumb = 300;
        $this->imageHeightThumb = 410;
    }

    public function index() {
        $data = false;
        $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/add'));
        $data['content'] = $this->getContent();


        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }


        $header['special_css'] = ['css/imgareaselect-default.css'];
        $footer['special_js'] = ['js/adayapakaSabham.js', 'js/jquery.imgareaselect.min.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/adayapakaSabham/adayapakaSabham', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent() {

        $this->load->library("pagination");

        $param['search'] = trim((string)$this->input->get('search'));
        $param['limit'] = 15;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->adayapakaSabham_model->getAllCount($param);
        $config["base_url"] = base_url('admin/adayapaka_sabham');
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

        $content['content'] = $this->adayapakaSabham_model->getAll($param);
        return $this->load->view('admin/adayapakaSabham/content', $content, TRUE);
    }

    function formValidation() {
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('upload_type', 'Upload Type', 'trim|required');
        if ($this->input->post('upload_type') == 'url') {
            $this->form_validation->set_rules('path', 'Website URL ', 'trim|required');
        } else if ($this->input->post('upload_type') == 'file') {
            $this->form_validation->set_rules('file', 'upload PDF/Image', 'trim');
        }
        $this->form_validation->set_rules('image', 'Image', 'trim');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'description' => $this->input->post('description'),
            'upload_type' => $this->input->post('upload_type'),
            'path' => $this->input->post('path'),
            'image' => $this->input->post('image'),
        ];
        return $formValues;
    }

    function imageValidate($val) {

        if (!isset($_FILES['image'])) {
            $this->form_validation->set_message('imageValidate', 'Pls upload PDF file ');
            return FALSE;
        }
        return TRUE;
    }

    function fileValidate($val) {

        if (!isset($_FILES['file'])) {
            $this->form_validation->set_message('fileValidate', 'Pls upload PDF file ');
            return FALSE;
        }
        return TRUE;
    }

    public function add() {
        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['formError'] = form_error();
            $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        //upload image
        $uploadImage = $this->uploadImage();
        if (isset($uploadImage['code']) && $uploadImage['code'] === 'error') {
            $data['code'] = 'error';
            $formValues['error'] = $uploadImage['error'];
            $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        $formValues['image'] = $uploadImage['file_name'];

        //upload image
        if ($this->input->post('upload_type') == "file") {
            //upload PDF/Image
            $uploadFile = $this->uploadFile();
            if (isset($uploadFile['code']) && $uploadFile['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadFile['error'];
                $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/add'), $formValues);
                echo json_encode($data);
                exit;
            }
            $formValues['path'] = $uploadFile['file_name'];
        }

        //save image details on database
        $add = $this->adayapakaSabham_model->add($formValues);
        if ($add) {
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
            $data['code'] = 'success';
        } else {
            $data['code'] = 'error';
            $data['data'] = 'Failed to save to database';
        }
        echo json_encode($data);
        exit;
    }



    function uploadFile() {
        if (!is_dir(ADAYAPAKA_SABHAM_FILE)) {
            mkdir(ADAYAPAKA_SABHAM_FILE, 0777, true);
        }
        //upload pdf
        $config = array();
        $config['upload_path'] = './' . ADAYAPAKA_SABHAM_FILE;
        $config['allowed_types'] = 'pdf|jpg|jpeg';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file')) {
            $error_msg = $this->upload->display_errors();
            $data['code'] = 'error';
            $data['error'] = $error_msg;
            return $data;
        } else {
            $uploadData = $this->upload->data();
        }
        return $uploadData;
    }

    function uploadImage() {
        if (!is_dir(ADAYAPAKA_SABHAM_IMAGE)) {
            mkdir(ADAYAPAKA_SABHAM_IMAGE, 0777, true);
        }
        //upload pdf
        $config = array();
        $config['upload_path'] = './' . ADAYAPAKA_SABHAM_IMAGE;
        $config['allowed_types'] = 'jpg|jpeg';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);
        if (!$this->upload->do_upload('image')) {
            $error_msg = $this->upload->display_errors();
            $data['code'] = 'error';
            $data['error'] = $error_msg;
            return $data;
        } else {
            $uploadData = $this->upload->data();
        }

        $this->createThumbnail($uploadData['file_name']);

        return $uploadData;
    }

    function createThumbnail($fileName) {
        $this->load->library('image_functions');
        //set form data in variables
        //X-source -starting point
        $x1 = (float)$this->input->post("x1");
        //Y-source -starting point
        $y1 = (float)$this->input->post("y1");
        //resizing image width[The image show is left side]
        $x2 = (float)$this->input->post("x2");
        //resize image height[The image show is left side]
        $y2 = (float)$this->input->post("y2");
        //Selected Thumbnail Area width
        $w = (float)$this->input->post("w");
        if (!$w || $w == 0) {
            $w = $this->imageWidthThumb;
        }
        //Selected Thumbnail Area height
        $h = (float)$this->input->post("h");
        if (!$h || $h == 0) {
            $h = $this->imageHeightThumb;
        }

        $thumb_image_location = ADAYAPAKA_SABHAM_IMAGE . '/' . $fileName;
        $large_image_location = ADAYAPAKA_SABHAM_IMAGE . '/' . $fileName;

        //Resize Image
        $width = $this->image_functions->getWidth($large_image_location);
        $height = $this->image_functions->getHeight($large_image_location);
        //Scale the image if it is greater than the width set above
        if (!$x2 || $x2 == 0) {
            $x2 = $this->imageWidthThumb;
        }
        if (!$y2 || $y2 == 0) {
            $y2 = $this->imageHeightThumb;
        }
        if ($width >= $x2) {
            $tempVal = $width / $x2;
            $scale = $x2 / $width;
        } else {
            $tempVal = $height / $y2;
            $scale = 1;
        }
        //Set New width and height for Thumbnail According to Original Image width and Height
        $w = $tempVal * $w;
        $h = $tempVal * $h;

        $x1PercentageTemp = $x1 / $x2;
        $x1 = ceil($x1PercentageTemp * $width);

        $y1PercentageTemp = $y1 / $y2;
        $y1 = ceil($y1PercentageTemp * $height);

        //Starting to create Thumbnail
        $thumb_width = $this->imageWidthThumb;
        $scale = $w ? $thumb_width / $w : 1;
        $this->image_functions->resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);
        //End of Thumbnail
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['formValues'] = $formValues;
        $data['addUrl'] = base_url('admin/adayapaka_sabham/add');
        return $this->load->view('admin/adayapakaSabham/form', $data, TRUE);
    }

    /*
     * It create html form For editing Gallery Album
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);

        $formInfo = $this->adayapakaSabham_model->getById($id);

        if ($formInfo) {
            $url = base_url('admin/adayapaka_sabham/update/' . $id);
            $data['form'] = $this->createForm($url, $formInfo, 'Edit');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    public function update() {
        $data['code'] = 'error';
        $formValues = $this->formValidation();
        $id = $this->uri->segment(4);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/adayapaka_sabham/update/' . $id);
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $formInfo = $this->adayapakaSabham_model->getById($id);
        if (!$formInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        unset($formValues['image']);
        if (isset($_FILES['image']) && $_FILES['image']) {

            $this->deleteFile(ADAYAPAKA_SABHAM_IMAGE . '/' . $formInfo['image']);

            $uploadImage = $this->uploadImage();
            if (isset($uploadImage['code']) && $uploadImage['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadImage['error'];
                $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/update/' . $id), $formValues, 'Edit');
                echo json_encode($data);
                exit;
            }
            $formValues['image'] = $uploadImage['file_name'];
        } else if ($this->input->post("w") > 0) {
            $this->createThumbnail($formInfo['image']);
        }

        if ($this->input->post('upload_type') == "file" && isset($_FILES['file']) && $_FILES['file']) {
            $this->deleteFile(ADAYAPAKA_SABHAM_FILE . '/' . $formInfo['path']);
            //upload PDF/Image
            $uploadFile = $this->uploadFile();
            if (isset($uploadFile['code']) && $uploadFile['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadFile['error'];
                $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/update/' . $id), $formValues, 'Edit');
                echo json_encode($data);
                exit;
            }
            $formValues['path'] = $uploadFile['file_name'];
        } else if ($this->input->post('upload_type') !== "url") {
            unset($formValues['path']);
        }

        $this->adayapakaSabham_model->update($id, $formValues);
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
        $orderInfo = $this->adayapakaSabham_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->adayapakaSabham_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        if ($this->deleteOne($id)) {
            $data['content'] = $this->getContent();
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }


    /**
     * Remove one row plus its file and image. Shared by the row Delete button
     * and the "Delete Selected" toolbar action.
     */
    protected function deleteOne($id) {
        $info = $this->adayapakaSabham_model->getById($id);
        if (!$info || !$this->adayapakaSabham_model->delete($id)) {
            return FALSE;
        }
        if ($info['upload_type'] == "file") {
            $this->deleteFile(ADAYAPAKA_SABHAM_FILE . '/' . $info['path']);
        }
        $this->deleteFile(ADAYAPAKA_SABHAM_IMAGE . '/' . $info['image']);
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
