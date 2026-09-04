<?php

namespace App\Controllers;
class WebService extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
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
