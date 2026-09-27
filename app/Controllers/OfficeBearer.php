<?php

namespace App\Controllers;
class OfficeBearer extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
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

        // Enforce desired section display order
        $sectionOrder = [
            'President / General Secretary / Treasurer',
            'Senior Vice President & Associate General Secretary',
            'Vice President',
            'Secretary',
            'Secretariate Members',
        ];
        $ordered = array();
        foreach ($sectionOrder as $section) {
            if (isset($content['content'][$section])) {
                $ordered[$section] = $content['content'][$section];
                unset($content['content'][$section]);
            }
        }
        // Append any remaining sections not in the predefined order
        foreach ($content['content'] as $key => $bearers) {
            $ordered[$key] = $bearers;
        }
        $content['content'] = $ordered;
        
//        echo '<pre>';
//        print_r($content['content']);
//        exit;




        $this->load->view('header');
        $this->load->view('officeBearer/officeBearer', $content);
        $this->load->view('footer');
    }

}
