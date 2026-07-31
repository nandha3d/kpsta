<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OfficeBearer extends MY_Controller {

    private $imageWidthThumb, $imageHeightThumb;

    public function __construct() {
        parent::__construct();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("OfficeBearer_model");

        $this->imageWidthThumb = 173;
        $this->imageHeightThumb = 214;
    }

    public function index() {

        $data['form'] = $this->createForm(base_url('admin/office_bearer/add'));
        $data['content'] = $this->getContent();

        $data['designations'] = $this->OfficeBearer_model->getAllDesignation();
        $data['section_headings'] = $this->OfficeBearer_model->getAllSectionHeadings();
        $data['districts'] = [
            'Thiruvananthapuram' => 'Thiruvananthapuram',
            'Kollam' => 'Kollam',
            'Pathanamthitta' => 'Pathanamthitta',
            'Alappuzha' => 'Alappuzha',
            'Kottayam' => 'Kottayam',
            'Idukki' => 'Idukki',
            'Ernakulam' => 'Ernakulam',
            'Thrissur' => 'Thrissur',
            'Palakkad' => 'Palakkad',
            'Malappuram' => 'Malappuram',
            'Kozhikode' => 'Kozhikode',
            'Wayanad' => 'Wayanad',
            'Kannur' => 'Kannur',
            'Kasaragod' => 'Kasaragod'
        ];

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $header['special_css'] = ['plugins/select2/select2.min.css', 'css/imgareaselect-default.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/jquery.imgareaselect.min.js', 'js/officeBearer.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/officeBearer/officeBearer', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {

        $this->load->library("pagination");

        $param['search'] = trim((string)$this->input->get('search'));
        $param['designation'] = $this->input->get('designation');
        $param['level'] = $this->input->get('level');
        $param['district'] = $this->input->get('district');
        $param['is_former'] = $this->input->get('is_former');
        $param['is_publish'] = $this->input->get('is_publish');
        $param['sort'] = $this->input->get('sort') ? $this->input->get('sort') : 'position-asc';

        $param['limit'] = 15;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->OfficeBearer_model->getAllCount($param);
        $config["base_url"] = base_url('admin/office_bearer');
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
            'page' => $param['page'],
            'search' => $param['search'],
            'designation' => $param['designation'],
            'level' => $param['level'],
            'district' => $param['district'],
            'is_former' => $param['is_former'],
            'is_publish' => $param['is_publish'],
            'sort' => $param['sort']
        ]);

        $content['content'] = $this->OfficeBearer_model->getAll($param);
        return $this->load->view('admin/officeBearer/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/office_bearer/add');
        $data['formValues'] = $formValues;
        $data['designation'] = $this->OfficeBearer_model->getAllDesignation();
        $data['designation'] = ['' => '- - - SELECT DESIGNATION - - -'] + $data['designation'];

        $data['section_headings'] = $this->OfficeBearer_model->getAllSectionHeadings();
        $data['section_headings'] = ['' => '- - - SELECT OR TYPE SECTION HEADING - - -'] + $data['section_headings'];
        
        $data['districts'] = [
            '' => '- - - SELECT DISTRICT - - -',
            'Thiruvananthapuram' => 'Thiruvananthapuram',
            'Kollam' => 'Kollam',
            'Pathanamthitta' => 'Pathanamthitta',
            'Alappuzha' => 'Alappuzha',
            'Kottayam' => 'Kottayam',
            'Idukki' => 'Idukki',
            'Ernakulam' => 'Ernakulam',
            'Thrissur' => 'Thrissur',
            'Palakkad' => 'Palakkad',
            'Malappuram' => 'Malappuram',
            'Kozhikode' => 'Kozhikode',
            'Wayanad' => 'Wayanad',
            'Kannur' => 'Kannur',
            'Kasaragod' => 'Kasaragod'
        ];

        return $this->load->view('admin/officeBearer/form', $data, TRUE);
    }

    public function add() {
        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $formValues['designation'] = $this->OfficeBearer_model->getOrAddDesignation($formValues['designation']);

        //check whether the post is single person or mulitple
        if ($this->OfficeBearer_model->isSingleDesignation($formValues['designation'])) {
            $data['code'] = 'error';
            $formValues['error'] = 'This designation is already exist. Please update the record';
            $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
            echo json_encode($data);
            exit;
        }


        $uploadData = $this->uploadImage();
        if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
            $data['code'] = 'error';
            $formValues['error'] = $uploadData['error'];
            $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        //Insert values
        $formValues['image'] = $uploadData['file_name'];
        $add = $this->OfficeBearer_model->add($formValues);
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

    function uploadImage() {
        if (!is_dir(OFFICE_BEARER)) {
            mkdir(OFFICE_BEARER, 0777, true);
        }

        $config['upload_path'] = './' . OFFICE_BEARER;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);
        
        if (!$this->upload->do_upload('image')) {
            $data['code'] = 'error';
            $data['error'] = $this->upload->display_errors('', '');
            return $data;
        } else {
            $uploadData = $this->upload->data();
            $this->createThumbnail($uploadData['file_name']);
            $uploadData['code'] = 'success';
            return $uploadData;
        }
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

        $thumb_image_location = OFFICE_BEARER . '/' . $fileName;
        $large_image_location = OFFICE_BEARER . '/' . $fileName;

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
        $scale = $thumb_width / $w;
        $this->image_functions->resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);
        //End of Thumbnail
    }

    function formValidation() {


        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'Designation', 'trim|required');
        $this->form_validation->set_rules('section_heading', 'Section Heading', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'name' => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'level' => $this->input->post('level') ? $this->input->post('level') : 'State',
            'section_heading' => $this->input->post('section_heading'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone') ? $this->input->post('phone') : NULL,
            'is_publish' => $this->input->post('is_publish'),
            'position' => $this->input->post('position') ? $this->input->post('position') : 100,
            'year' => $this->input->post('year'),
            'is_former' => $this->input->post('is_former') ? $this->input->post('is_former') : 0
        ];

        return $formValues;
    }

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);
        $formInfo = $this->OfficeBearer_model->getById($id);
        if ($formInfo) {
            $url = base_url('admin/office_bearer/update/' . $id);
            $data['form'] = $this->createForm($url, $formInfo, 'Edit Form');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    public function update() {

        $formValues = $this->formValidation();

        $id = $this->uri->segment(4);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/office_bearer/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            echo json_encode($data);
            exit;
        }

        $formValues['designation'] = $this->OfficeBearer_model->getOrAddDesignation($formValues['designation']);

        $formInfo = $this->OfficeBearer_model->getById($id);
        if (!$formInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        //check whether  image is change while editing, then upload new image
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadData['error'];
                $url = base_url('admin/office_bearer/update/' . $id);
                $data['form'] = $this->createForm($url, $formValues, 'Edit');
                echo json_encode($data);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        } else if ($this->input->post("w") > 0) {
            $this->createThumbnail($formInfo['image']);
        }

        //update 
        $this->OfficeBearer_model->update($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    public function publish() {
        $id = $this->uri->segment(4);
        $orderInfo = $this->OfficeBearer_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->OfficeBearer_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $info = $this->OfficeBearer_model->getById($id);
        $delete = $this->OfficeBearer_model->delete($id);
        if ($delete && $info) {

            $this->deleteFile(OFFICE_BEARER . '/' . $info['image']);

            $data['content'] = $this->getContent();
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }

}
