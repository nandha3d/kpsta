<?php

namespace App\Controllers;
class Results extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->model("ResultLink_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index() {

        $param['limit'] = 250;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        $config["total_rows"] = $this->ResultLink_model->getAllCount($param, TRUE);
        $config["base_url"] = base_url() . "quicklink";
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();



        $param['isPublish'] = TRUE;
        $content['data'] = $this->ResultLink_model->getAll($param);




        $this->load->view('header');
        $this->load->view('resultLink/result', $content);
        $this->load->view('footer');
    }

}
