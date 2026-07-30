<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class District extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("OfficeBearer_model");
        $this->load->model("Settings_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index() {

        $param['isPublish'] = TRUE;
        $param['is_former'] = 0; // Only get active district office bearers for public page
        $param['active_term'] = $this->Settings_model->getActiveTerm();
        $param['level'] = 'District';
        $param['limit'] = 1500;
        $result = $this->OfficeBearer_model->getAll($param);

        $content['content'] = array();
        foreach ($result as $row) {
            try {
                $key = isset($row['section_heading']) ? $row['section_heading'] : 'Other';
                $content['content'][$key]['officeBearer'][] = $row;
            } catch (\Exception $exc) {
                
            }
        }

        $this->load->view('header');
        $this->load->view('district/districtOfficeBearer', $content);
        $this->load->view('footer');
    }

}
