<?php

namespace App\Controllers;
class News extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->model("news_model");
    }

    public function index() {


        $config["per_page"] = $param['limit'] = 8;
        $param['isPublish'] = TRUE;
        
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        $config["total_rows"] = $this->news_model->getAllNewsCount($param, TRUE);

        $config["base_url"] = base_url() . "news";
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();
        
        $content['listNews'] = $this->news_model->getAllNews($param, TRUE);
        $this->load->view('header');
        $this->load->view('news/news', $content);
        $this->load->view('footer');
    }

}
