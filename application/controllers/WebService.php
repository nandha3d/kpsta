<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class WebService extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("WebService_model");

        $this->load->helper(array('form', 'url'));
    }

    public function registerToken() {
        
        $data =  array(
          'device_id' => $this->input->post('device_id'),
          'device_type' => $this->input->post('device_type'),
          'unique_device_id' => $this->input->post('unique_device_id'),
          'device_model' => $this->input->post('device_model'),
        );

        $result = $this->WebService_model->registerToken($data);

//        echo '<pre>';
//        print_r($content['content']);
//        exit;
    }

}
