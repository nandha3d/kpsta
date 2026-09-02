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

        // Every year that has albums, not just the one being viewed -- the
        // picker is built from this.
        $content['galleryYears'] = $this->gallery_model->getAlbumYears();

        $album['year'] = $this->input->get('year');
        if (empty($album['year']) || !in_array((int) $album['year'], $content['galleryYears'], TRUE)) {
            // Default to the most recent year that actually has albums. Falling
            // back to the current year showed an empty gallery whenever nothing
            // had been published this calendar year, with no way to reach the
            // years that did have albums.
            $album['year'] = !empty($content['galleryYears'])
                ? $content['galleryYears'][0]
                : date('Y');
        }

        $content['activeYear'] = $album['year'];
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
