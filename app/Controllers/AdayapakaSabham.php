<?php

namespace App\Controllers;
class AdayapakaSabham extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
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
