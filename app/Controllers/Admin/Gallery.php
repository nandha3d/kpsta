<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
//session_start(); //we need to start session in order to access it through CI

class Gallery extends AppController {

    public function ci3Init(): void {
        parent::ci3Init();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("gallery_model");
    }

    public function index() {
        $data = false;
        $data['form'] = $this->createForm(base_url('admin/gallery/add'));

        $data['content'] = $this->getContent();

        $this->load->view('admin/header');
        $this->load->view('admin/gallery/gallery', $data);
        $this->load->view('admin/footer');
    }

    function getContent($param = array()) {
        $this->load->library("pagination");

        $param['limit'] = 10;
        $param['offset'] = (is_numeric($this->uri->segment(3))) ? (int)$this->uri->segment(3) : 0;
        $config["total_rows"] = $this->gallery_model->getAllAlbumCount($param);

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];

        //pagination 
        $config["base_url"] = base_url() . "admin/news/";
        $config["per_page"] = $param['limit'];
        $config["uri_segment"] = 3;
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();

        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['albums'] = $this->gallery_model->getAllAlbum();

        return $this->load->view('admin/gallery/galleryContent', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add New Album") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/gallery/add');
        $data['formValues'] = $formValues;
        return $this->load->view('admin/gallery/galleryForm', $data, TRUE);
    }

    /*
     * This function for Insert latest news
     * @return json_encode response
     */

    public function add() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('publish', 'Publish', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'is_publish' => $this->input->post('publish')
        ];
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('admin/gallery/add'), $formValues);
            echo json_encode($data);
            exit;
        }
        //Insert values
        $formValues['created_at'] = date("Y-m-d H:i:s");
        $add = $this->gallery_model->addAlbum($formValues);
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
     * It create html form For editing Gallery Album
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);

        $albumInfo = $this->gallery_model->getAlbum($id);

        if ($albumInfo) {
            $url = base_url('admin/gallery/update/' . $id);
            $data['form'] = $this->createForm($url, $albumInfo, 'Edit Album Details');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update News info
     * @return json_endcode  data
     */

    /**
     * Update News info
     * @return json_endcode  data
     */
    public function update() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('publish', 'Publish', 'trim|required');
        $this->form_validation->set_error_delimiters("<p>", "</p>");
        $formValues = [
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'is_publish' => $this->input->post('publish'),
        ];

        $id = $this->uri->segment(4);
        $albumInfo = $this->gallery_model->getAlbum($id);
        if (!$albumInfo) {
            $formValues['error'] = "Record not found!!!";
        }
        //validation FALSE
        if ($this->form_validation->run() == FALSE || !$albumInfo) {
            $data['code'] = 'error';
            $url = base_url('admin/gallery/update/' . $id);
            $data['form'] = $this->createForm($url, $formValues, 'Edit News');
            echo json_encode($data);
            exit;
        }
        //update news
        $this->gallery_model->update($id, $formValues);
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
        echo fale;
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);

        $delete = $this->gallery_model->deleteAlbum($id);
        if ($delete) {
            $data['content'] = $this->getContent();
            $data['code'] = 'success';
        } else {
            $data['db_error'] = $this->db->error();
        }
        echo json_encode($data);
        exit;
    }

    ######################
    #Single Gallery
    ######################

    public function singleGallery() {
        $data = false;

        $data['form'] = $this->singleCreateForm(base_url('admin/gallery/' . $this->uri->segment(3) . '/upload'));

        $albumData = $this->gallery_model->getAllAlbum(['albumId' => $this->uri->segment(3)]);
        if ($albumData) {
            $data['albumData'] = $albumData[0];
        }

        $data['content'] = $this->getSingleContent();

        $header['special_css'] = ['css/imgareaselect-default.css'];
        $footer['special_js'] = ['js/gallery_single.js', 'js/jquery.imgareaselect.min.js'];


        $this->load->view('admin/header', $header);
        $this->load->view('admin/gallery/singleGallery', $data);
        $this->load->view('admin/footer', $footer);
    }

    /**
     * Move the posted image into the originals folder. Shared by the upload and
     * the edit-with-replacement paths so both accept the same file types and
     * report failures the same way.
     */
    private function storeUpload() {
        foreach (array(GALLERY_ORIGINAL, GALLERY_RESIZE, GALLERY_THUMB) as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        $config['upload_path'] = './' . GALLERY_ORIGINAL;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('image')) {
            // display_errors() is markup; the caller puts this straight into a
            // JS alert, so hand back plain text.
            return array(
                'code' => 'error',
                'data' => trim(strip_tags($this->upload->display_errors())),
            );
        }

        $uploadData = $this->upload->data();
        return array(
            'code' => 'success',
            'file_name' => $uploadData['file_name'],
            'data' => $uploadData,
        );
    }

    public function singleGalleryUpload() {
        $upload = $this->storeUpload();

        if ($upload['code'] !== 'success') {
            $data['code'] = 'error';
            $data['data'] = $upload['data'];
            echo json_encode($data);
            exit;
        }

        //save image details on database
        $this->gallery_model->addImage([
            'album_id' => $this->uri->segment(3),
            'image' => $upload['file_name'],
            'title' => $this->input->post('title')
        ]);

        $this->createThumbnail($upload['file_name']);

        $data['content'] = $this->getSingleContent();
        $data['code'] = 'success';
        $data['data'] = $upload['data'];

        echo json_encode($data);
        exit;
    }

    function createThumbnail($fileName) {
        // The cropping below calls copy(), getimagesize() and imagecreatefrom*()
        // straight on the source path. If that file is missing, each of those
        // raises a PHP warning, and CI renders warnings as an HTML block that
        // gets echoed BEFORE the json_encode() further down -- which is what
        // broke the save with "Unexpected token '<' ... is not valid JSON".
        // Bail out cleanly instead and let the caller report a real message.
        $source = GALLERY_ORIGINAL . '/' . $fileName;
        if (empty($fileName) || !is_file($source)) {
            return FALSE;
        }

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
            $w = 300;
        }
        //Selected Thumbnail Area height
        $h = (float)$this->input->post("h");
        if (!$h || $h == 0) {
            $h = 300;
        }

        $thumb_image_location = GALLERY_THUMB . '/' . $fileName;
        $large_image_location = $source;
        $resize_image_location = GALLERY_RESIZE . '/' . $fileName;

        //Resize Image
        copy($large_image_location, $resize_image_location);
        $width = $this->image_functions->getWidth($large_image_location);
        $height = $this->image_functions->getHeight($large_image_location);
        //Scale the image if it is greater than the width set above
        if (!$x2 || $x2 == 0) {
            $x2 = 300;
        }
        if (!$y2 || $y2 == 0) {
            $y2 = 250;
        }
        if ($width >= $x2) {
            $tempVal = $width / $x2;
            $scale = $x2 / $width;
            //  $resized_image = $this->image_functions->resizeImage($large_image_location, $width, $height, $scale);
        } else {
            $tempVal = $height / $y2;

            $scale = 1;
            //$resized_image = $this->image_functions->resizeImage($large_image_location, $width, $height, $scale);
        }
        //End of Resize
        //
//            testing new methond
        //Set New width and height for Thumbnail According to Original Image width and Height
        $w = $h = $tempVal * $w;

        $x1PercentageTemp = $x1 / $x2;
        $x1 = ceil($x1PercentageTemp * $width);

        $y1PercentageTemp = $y1 / $y2;
        $y1 = ceil($y1PercentageTemp * $height);



        //Starting to create Thumbnail
        $thumb_width = 300;
        $scale = $thumb_width / $w;
        $this->image_functions->resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);
        //End of Thumbnail
        return TRUE;
    }

    public function singleCreateForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['formValues'] = $formValues;
        $data['addUrl'] = base_url('admin/gallery/' . $this->uri->segment(3) . '/upload');
        return $this->load->view('admin/gallery/singleGalleryForm', $data, TRUE);
    }

    /*
     * It create html form For editing Gallery Album
     * @return json_endcode  data
     */

    public function imageEdit() {
        $data['code'] = 'error';

        $albumId = $this->uri->segment(3);
        $imageId = $this->uri->segment(5);

        $imageInfo = $this->gallery_model->getImage($albumId, $imageId);

        if ($imageInfo) {
            $url = base_url('admin/gallery/' . $albumId . '/update/' . $imageId);
            $data['form'] = $this->singleCreateForm($url, $imageInfo, 'Edit Image');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    public function imageUpdate() {
        $data['code'] = 'error';

        $albumId = $this->uri->segment(3);
        $imageId = $this->uri->segment(5);

        $imageInfo = $this->gallery_model->getImage($albumId, $imageId);

        if (!$imageInfo) {
            $data['data'] = 'Image not found.';
            echo json_encode($data);
            exit;
        }

        $fileName = $imageInfo['image'];

        // The edit dialog offers a file picker, so honour it: a newly chosen
        // file replaces the stored one. Previously anything picked here was
        // silently ignored and only the crop was re-run.
        if (!empty($_FILES['image']['name'])) {
            $replacement = $this->storeUpload();
            if ($replacement['code'] !== 'success') {
                $data['data'] = $replacement['data'];
                echo json_encode($data);
                exit;
            }
            $this->deleteFile(GALLERY_THUMB . '/' . $fileName);
            $this->deleteFile(GALLERY_ORIGINAL . '/' . $fileName);
            $fileName = $replacement['file_name'];
        }

        // The title field was never being saved either.
        $this->gallery_model->updateImage($albumId, $imageId, array(
            'image' => $fileName,
            'title' => $this->input->post('title'),
        ));

        if (!$this->createThumbnail($fileName)) {
            $data['data'] = 'The image file is missing from the server, so the '
                          . 'thumbnail could not be rebuilt. Re-upload the image.';
            $data['content'] = $this->getSingleContent();
            echo json_encode($data);
            exit;
        }

        $data['code'] = 'success';
        $data['data'] = 'Saved Successfully';
        $data['content'] = $this->getSingleContent();
        echo json_encode($data);
        exit;
    }

    function getSingleContent() {
        $param['albumId'] = $this->uri->segment(3);
        $content['images'] = $this->gallery_model->getAlbumImages($param);

        return $this->load->view('admin/gallery/singleGalleryContent', $content, TRUE);
    }

    function makeAlbumCover() {
        $data['code'] = 'error';

        $albumId = $this->uri->segment(3);
        $imageId = $this->uri->segment(5);

        $makeAlbumCover = $this->gallery_model->makeAlbumCover($albumId, $imageId);
        if ($makeAlbumCover) {
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    public function imageDelete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(5);
        $albumId = $this->uri->segment(3);
        $info = $this->gallery_model->getImage($albumId, $id);

        $delete = $this->gallery_model->deleteImage($albumId, $id);
        if ($delete && $info) {

            $this->deleteFile(GALLERY_THUMB . '/' . $info['image']);
            $this->deleteFile(GALLERY_ORIGINAL . '/' . $info['image']);

            $data['content'] = $this->getSingleContent();
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }

}
