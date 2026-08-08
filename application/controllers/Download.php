<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Download_model");
    }

    function getSegment2() {
        if (in_array($this->uri->segment(1), ['notice_poster', 'melakal', 'official_outlook'])) {
            return $this->uri->segment(1);
        } else {
            return $this->uri->segment(2);
        }
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('download/actRules');
        $this->load->view('footer');
    }

    public function download() {
        $segment = $this->getMenuType($this->getSegment2());
        $param['type'] = $segment['type'];
        $content['type'] = $segment['type'];
        $content['contentTitle'] = $segment['contentTitle'];

        $param['limit'] = 15;
        $param['offset'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $param['isPublish'] = TRUE;
        $param['category'] = $this->input->get('category');

        $content['selectedCategory'] = $param['category'];
        $content['selectedCategoryName'] = $this->Download_model->getCategoryById($param['category']);
        $content['categories'] = $this->Download_model->getFormsCategory($param);
        $content['urlString'] = base_url($this->uri->uri_string());



        $config["total_rows"] = $this->Download_model->getAllCount($param, TRUE);

        $config['suffix'] = '?category=' . $param['category'];
        $config['first_url'] = '0?category=' . $param['category'];
        $config["base_url"] = base_url() . $segment['baseUrl'];
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();


        $content['data'] = $this->Download_model->getAll($param, TRUE);




        $this->load->view('header');
        $this->load->view('download/download', $content);
        $this->load->view('footer');
    }

    public function forms() {
        $segment = $this->getMenuType($this->getSegment2());

        $content['contentTitle'] = $segment['contentTitle'];
        $content['route'] = isset($segment['route']) ? $segment['route'] : '';

        $param['category'] = $this->input->get('category');
        $param['search'] = $this->input->get('search');

        $param['limit'] = 30;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;
        $param['type'] = $segment['type'];
        $content['categories'] = $this->Download_model->getFormsCategory($param);

        if (empty($param['category']) && !empty($content['categories'])) {
            $param['category'] = $content['categories'][0]['id'];
        }

        $config["total_rows"] = $this->Download_model->getAllCount($param, TRUE);
        if ($param['category'] > 0) {
            $config['suffix'] = '&category=' . $param['category'];
            $config['first_url'] = '?category=' . $param['category'];
        }
        $config["base_url"] = base_url() . $segment['baseUrl'];
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['selectedCategory'] = $param['category'];
        $content['selectedCategoryName'] = $this->Download_model->getCategoryById($param['category']);
        $content['search'] = $param['search'];

        $content['urlString'] = base_url($this->uri->uri_string());
        $content['base_url'] = $config["base_url"] ;

        $forms = $this->Download_model->getAll($param, TRUE);
        $content['forms'] = array();
        try {
            foreach ($forms as $form) {
                $content['forms'][$form['category']][] = $form;
            }
        } catch (\Exception $exc) {
            
        }



        $this->load->view('header');
        $this->load->view('download/forms', $content);
        $this->load->view('footer');
    }

    function getMenuType($url) {
        switch ($url) {
            case 'act_rules':
                $data['type'] = 1;
                $data['contentTitle'] = 'Act & Rules';
                $data['baseUrl'] = 'download/act_rules';
                $data['category'] = false;
                break;
            case 'softwares':
                $data['type'] = 2;
                $data['contentTitle'] = 'Software';
                $data['baseUrl'] = 'download/softwares';
                $data['category'] = false;
                break;
            case 'fonts':
                $data['type'] = 3;
                $data['contentTitle'] = 'Fonts';
                $data['baseUrl'] = 'download/fonts';
                $data['category'] = false;
                break;
            case 'forms':
                $data['type'] = 4;
                $data['contentTitle'] = 'Forms';
                $data['baseUrl'] = 'download/forms';
                $data['category'] = true;
                break;
            case 'notice_poster':
                $data['type'] = 5;
                $data['contentTitle'] = 'Notics & Posters';
                $data['baseUrl'] = 'notice_poster';
                $data['category'] = false;
                break;

            case 'melakal':
                $data['type'] = 6;
                $data['contentTitle'] = 'Melakal';
                $data['route'] = 'melakal';
                $data['category'] = true;
                $data['baseUrl'] = 'melakal';
                break;

            case 'official_outlook':
                $data['type'] = 7;
                $data['contentTitle'] = 'Official outlook';
                $data['route'] = 'official_outlook';
                $data['baseUrl'] = 'official_outlook';
                $data['category'] = false;
                break;
                
            case 'academic_corner':
                $data['type'] = 8;
                $data['contentTitle'] = 'Academic Corner';
                $data['route'] = 'academic_corner';
                $data['baseUrl'] = 'download/academic_corner';
                $data['category'] = true;
                break;    

            default:
                $data['type'] = 0;
                break;
        }
        return $data;
    }

}
