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
            $rawKey = !empty($row['section_heading']) ? trim($row['section_heading']) : (!empty($row['designation']) ? trim($row['designation']) : 'Other');
            $upperKey = strtoupper($rawKey);
            $canonicalKey = $rawKey;

            if ((strpos($upperKey, 'PRESIDENT') !== false && strpos($upperKey, 'VICE') === false) || strpos($upperKey, 'TREASURER') !== false || in_array($upperKey, ['PRESIDENT', 'GENERAL SECRETARY', 'TREASURER'])) {
                $canonicalKey = 'President / General Secretary / Treasurer';
            } else if (strpos($upperKey, 'SENIOR VICE') !== false || strpos($upperKey, 'ASSOCIATE GENERAL') !== false) {
                $canonicalKey = 'Senior Vice President / Associate General Secretary';
            } else if (strpos($upperKey, 'VICE PRESIDENT') !== false) {
                $canonicalKey = 'Vice President';
            } else if (strpos($upperKey, 'SECRETARIAT') !== false || strpos($upperKey, 'SECRETARIATE') !== false) {
                $canonicalKey = 'Secretariate Members';
            } else if (strpos($upperKey, 'SECRETARY') !== false && strpos($upperKey, 'GENERAL') === false && strpos($upperKey, 'ASSOCIATE') === false) {
                $canonicalKey = 'Secretary';
            }

            $content['content'][$canonicalKey][] = $row;
        }

        // Enforce strict category designation display order
        $sectionOrder = [
            'President / General Secretary / Treasurer',
            'Senior Vice President / Associate General Secretary',
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
