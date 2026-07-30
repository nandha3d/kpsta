<?php

Class Slider extends MY_Controller {

    private $imageWidthThumb, $imageHeightThumb;

    public function __construct() {
        parent::__construct();
        // Load form helper library
        $this->load->helper(array('form', 'url'));
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("Slider_model");

        $this->imageWidthThumb = 1920;
        $this->imageHeightThumb = 621;
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

        $data['form'] = $this->createForm(base_url('admin/slider/add'));

        $header['special_css'] = ['plugins/select2/select2.min.css', 'css/imgareaselect-default.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/jquery.imgareaselect.min.js', 'js/slider.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/slider/slider', $data);
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
        $config["total_rows"] = $this->Slider_model->getAllCount($param);
        $config["base_url"] = base_url() . "admin/slider";
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

        $content['orders'] = $this->Slider_model->getAll($param);
        return $this->load->view('admin/slider/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add Order || circular") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/slider/add');
        $data['formValues'] = $formValues;


        return $this->load->view('admin/slider/form', $data, TRUE);
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
            $url = base_url('admin/slider/add');
            $data['form'] = $this->createForm($url, $formValues);
            echo json_encode($data);
            exit;
        }


        //TODO
        $uploadData = $this->uploadImage();
        if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
            $data['code'] = 'error';
            $formValues['error'] = $uploadData['error'];
            $data['form'] = $this->createForm(base_url('admin/slider/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $formValues['image'] = $uploadData['file_name'];
        //Insert values
        $add = $this->Slider_model->add($formValues);
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
        $this->form_validation->set_rules('is_publish', 'Publish', 'trim|required');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $heading_pages = $this->input->post('heading_pages') ? implode(',', $this->input->post('heading_pages')) : '';
        $formValues = [
            'description' => $this->input->post('description'),
            'is_publish' => $this->input->post('is_publish'),
            'show_on_home' => $this->input->post('show_on_home') ? 1 : 0,
            'is_heading_bg' => $this->input->post('is_heading_bg') ? 1 : 0,
            'heading_pages' => $this->input->post('chk_individual') ? $heading_pages : '',
            'position' => $this->input->post('position'),
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
        $formInfo = $this->Slider_model->getById($id);
        if ($formInfo) {
            $url = base_url('admin/slider/update/' . $id);

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
            $url = base_url('admin/slider/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            echo json_encode($data);
            exit;
        }

        $formInfo = $this->Slider_model->getById($id);
        if (!$formInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        //check whether  image is change while editing, then upload new image
        if (isset($_FILES['image']) && $_FILES['image']) {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadData['error'];
                $data['form'] = $this->createForm(base_url('admin/slider/update/' . $id), $formValues, 'Edit');
                echo json_encode($data);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        } else if ($this->input->post("w") > 0) {
            $this->createThumbnail($formInfo['image']);
        }

        //update news
        $this->Slider_model->update($id, $formValues);
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
        $orderInfo = $this->Slider_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->Slider_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    function uploadImage() {
        if (!is_dir(SLIDER_IMAGES)) {
            mkdir(SLIDER_IMAGES, 0777, true);
        }

        //upload pdf
        $config['upload_path'] = './' . SLIDER_IMAGES;
        $config['allowed_types'] = 'jpg|jpeg|png';
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

        $thumb_image_location = SLIDER_IMAGES . '/' . $fileName;
        $large_image_location = SLIDER_IMAGES . '/' . $fileName;

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
        $w = ($tempVal * $w) < $this->imageWidthThumb ? ($tempVal * $w) : $this->imageWidthThumb;
        $h = ($tempVal * $h) < $this->imageHeightThumb ? ($tempVal * $h) : $this->imageHeightThumb;

        $x1PercentageTemp = $x1 / $x2;
        $x1 = ceil($x1PercentageTemp * $width);

        $y1PercentageTemp = $y1 / $y2;
        $y1 = ceil($y1PercentageTemp * $height);

        //Starting to create Thumbnail
        $thumb_width = $this->imageWidthThumb;
        $scale = $thumb_width / $w;
        $this->image_functions->resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);
        //End of Thumbnail
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $info = $this->Slider_model->getById($id);
        $delete = $this->Slider_model->delete($id);
        if ($delete && $info) {

            $this->deleteFile(SLIDER_IMAGES . '/' . $info['image']);

            $data['content'] = $this->getContent();
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }

}


