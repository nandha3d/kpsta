<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OfficeBearer extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("OfficeBearer_model");
        $this->load->model("Settings_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index() {

        $param['isPublish'] = TRUE;
        $param['is_former'] = 0; // Only get active state office bearers for public page
        $param['limit'] = 150;
        $param['active_term'] = $this->Settings_model->getActiveTerm();
        $param['level'] = 'State';
        $result = $this->OfficeBearer_model->getAll($param);


        $content['content'] = array();
        foreach ($result as $row) {
            try {
                $key = !empty($row['section_heading']) ? $row['section_heading'] : $row['designation'];
                $content['content'][$key][] = $row;
            } catch (\Exception $exc) {
                
            }
        }
        
//        echo '<pre>';
//        print_r($content['content']);
//        exit;




        $this->load->view('header');
        $this->load->view('officeBearer/officeBearer', $content);
        $this->load->view('footer');
    }

}
