<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdayapakaSabham extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("AdayapakaSabham_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index() {

        $param['isPublish'] = TRUE;
        $content['content'] = $this->AdayapakaSabham_model->getAll($param);
//        echo '<pre>';
//        print_r($content['content']);
//        exit;


        $this->load->view('header');
        $this->load->view('adayapakaSabham/adayapakaSabham', $content);
        $this->load->view('footer');
    }

}
