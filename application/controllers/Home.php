<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller {

    public function __construct() {
        parent::__construct();

        // Load form helper library
        $this->load->helper(array('form', 'url'));

        $this->load->model("OrderCircular_model");
        $this->load->model("news_model");
        $this->load->model("gallery_model");
        $this->load->model("Quicklink_model");
        $this->load->model("FlashNews_model");
        $this->load->model("Slider_model");
        $this->load->model("ReactionGallery_model");
        $this->load->model("OfficeBearer_model");
        $this->load->model("Settings_model");
    }

    /**
     * Web cache Enabled for HOme page
     * Pls refer My_controller contruct function for deleting home page
     */
    public function index() {

        $content['orders'] = $this->OrderCircular_model->getAllByType(['limit' => 6], TRUE);

        $content['listNews'] = $this->news_model->getAllNews(['limit' => 3], TRUE);

        $content['images'] = $this->gallery_model->getAllImages(['limit' => 20, 'isPublish' => TRUE,]);

        $content['quicklink'] = $this->Quicklink_model->getAll([ "limit" => 8, "isPublish" => TRUE]);

        $content['sliderImages'] = $this->Slider_model->getAll([ 'isPublish' => TRUE, 'limit' => 5, 'show_on_home' => 1]);

        $content['reactionGalleryImages'] = $this->ReactionGallery_model->getAll([ 'isPublish' => TRUE, 'limit' => 5]);

        $content['news'] = $this->FlashNews_model->getAll(array('isPublish' => TRUE));

        $active_term = $this->Settings_model->getActiveTerm();
        $content['officeBearer'] = $this->OfficeBearer_model->getAll(array('isPublish' => TRUE, 'is_former' => 0, 'limit' => 3, 'active_term' => $active_term, 'level' => 'State', 'designation' => '1,2,3', 'sort' => 'primary'));


        if (ENVIRONMENT == "development") {
            $this->output->delete_cache(base_url($this->uri->uri_string()));
        } else {
            $this->output->cache(2000);
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
            $this->output->set_header("Pragma: no-cache");
        }



        $this->load->view('header');
        $this->load->view('home/index', $content);
        $this->load->view('footer');
    }

    public function service_corner() {
        $this->load->model('ServiceCorner_model');
        $content['services'] = $this->ServiceCorner_model->getAll(array('status' => 1));
        
        $this->load->view('header');
        $this->load->view('home/service_corner', $content);
        $this->load->view('footer');
    }

    public function service_corner_details($service_id = null) {
        $this->load->model('ServiceCorner_model');
        $content = array();
        if ($service_id) {
            $service = $this->ServiceCorner_model->getAll(array('id' => $service_id));
            if ($service) {
                $content['service'] = $service;
                $content['rules'] = $this->ServiceCorner_model->getRulesByServiceId($service_id);
            }
        }
        if (empty($content['service'])) {
            $services = $this->ServiceCorner_model->getAll(array('status' => 1));
            if (!empty($services)) {
                $content['service'] = $services[0];
                $content['rules'] = $this->ServiceCorner_model->getRulesByServiceId($services[0]['id']);
            }
        }
        $this->load->view('header');
        $this->load->view('home/service_corner_details', $content);
        $this->load->view('footer');
    }

    public function former_leaders() {
        // Deliberately no active_term filter: former leaders belong to past
        // terms by definition, so restricting to the current term would hide
        // every one of them.
        $content['former_leaders'] = $this->OfficeBearer_model->getAll(array('isPublish' => TRUE, 'is_former' => 1, 'limit' => 500));
        $this->load->view('header');
        $this->load->view('home/former_leaders', $content);
        $this->load->view('footer');
    }

    public function memorandums() {
        $this->load->view('header');
        $this->load->view('home/memorandums');
        $this->load->view('footer');
    }


    function siteVisitors() {
        $data['count'] = $this->FlashNews_model->getSiteVisitorsCount();
        $data['code'] = 'success';
        echo json_encode($data);
        exit;
    }

}
