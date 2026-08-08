<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("gallery_model");
    }

    public function index() {
        $album['limit'] = 20;
        $album['isPublish'] = TRUE;
        $album['year'] = $this->input->get('year');
        if (empty($album['year'])) {
            $album['year'] = date('Y');
        }

        $content['albums'] = $this->gallery_model->getAllAlbum($album);

        $this->load->view('header');
        $this->load->view('gallery/gallery', $content);
        $this->load->view('footer');
    }

    public function singleAlbum() {

        $album['albumId'] = $this->uri->segment(3);
        $content['images'] = $this->gallery_model->getAlbumImages($album);

        $albumData = $this->gallery_model->getAllAlbum(['albumId' => $this->uri->segment(3)]);
        if ($albumData) {
            $content['albumData'] = $albumData[0];
        }


        $header['special_css'] = ['plugins/prettyPhoto/prettyPhoto.css'];
        $footer['special_js'] = ['js/gallery-front.js', 'plugins/prettyPhoto/jquery.prettyPhoto.js'];

        $this->load->view('header', $header);
        $this->load->view('gallery/singleGallery', $content);
        $this->load->view('footer', $footer);
    }

}
