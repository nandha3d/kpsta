<?php

namespace App\Controllers;
class Download extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->model("Download_model");
    }

    function getSegment2() {
        if (in_array($this->uri->segment(1), ['notice_poster', 'melakal', 'official_outlook'])) {
            return $this->uri->segment(1);
        } else {
            return $this->uri->segment(2);
        }
    }

    /**
     * Page types whose reference design shows the file-type mark beside each
     * row: Forms (4), Notices & Posters (5), Melakal (6), Academic Corner (8).
     * Act & Rules, Software and Fonts are drawn as plain rows.
     */
    private $iconTypes = array(4, 5, 6, 8);

    public function index() {
        $this->load->view('header');
        $this->load->view('download/actRules');
        $this->load->view('footer');
    }

    public function download() {
        $segment = $this->getMenuType($this->getSegment2());
        $param['type'] = $segment['type'];
        $content['type'] = $segment['type'];
        $content['contentTitle'] = $segment['contentTitle'];
        $content['showFileIcon'] = in_array($segment['type'], $this->iconTypes);

        $param['limit'] = 15;
        $param['offset'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $param['isPublish'] = TRUE;
        $param['category'] = $this->input->get('category');

        $content['selectedCategory'] = $param['category'];
        $catRow = !empty($param['category']) ? $this->Download_model->getCategoryById($param['category']) : null;
        $content['selectedCategoryName'] = is_array($catRow) ? ($catRow['name'] ?? '') : (is_string($catRow) ? $catRow : '');
        $content['categories'] = $this->Download_model->getFormsCategory($param);
        $content['urlString'] = base_url($this->uri->uri_string());



        $config["total_rows"] = $this->Download_model->getAllCount($param, TRUE);

        $config['suffix'] = '?category=' . $param['category'];
        $config['first_url'] = '0?category=' . $param['category'];
        $config["base_url"] = base_url() . $segment['baseUrl'];
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();


        $content['data'] = $this->Download_model->getAll($param, TRUE);




        $this->load->view('header');
        $this->load->view('download/download', $content);
        $this->load->view('footer');
    }

    public function forms() {
        $segment = $this->getMenuType($this->getSegment2());

        $content['contentTitle'] = $segment['contentTitle'];
        $content['route'] = isset($segment['route']) ? $segment['route'] : '';
        $content['showFileIcon'] = in_array($segment['type'], $this->iconTypes);

        $param['category'] = $this->input->get('category');
        $param['search'] = $this->input->get('search');

        $param['limit'] = 30;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;
        $param['type'] = $segment['type'];
        $content['categories'] = $this->Download_model->getFormsCategory($param);

        // If no category is selected, do not auto-select first category; show category hub first
        if (!empty($param['category'])) {
            $config["total_rows"] = $this->Download_model->getAllCount($param, TRUE);
            $config['suffix'] = '&category=' . $param['category'];
            $config['first_url'] = '?category=' . $param['category'];
            $config["base_url"] = base_url() . $segment['baseUrl'];
            $config["per_page"] = $param['limit'];

            if ($config["total_rows"] <= $param['offset']) {
                $param['offset'] = 0;
            }
            $configBootrap = $this->BootsrapPaginationConfig();
            $config = array_merge($config, $configBootrap);
            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $content["links"] = $this->pagination->create_links();

            $content['selectedCategory'] = $param['category'];
            $catRow = !empty($param['category']) ? $this->Download_model->getCategoryById($param['category']) : null;
            $content['selectedCategoryName'] = is_array($catRow) ? ($catRow['name'] ?? '') : (is_string($catRow) ? $catRow : '');

            $forms = $this->Download_model->getAll($param, TRUE);
            $content['forms'] = array();
            try {
                foreach ($forms as $form) {
                    $content['forms'][$form['category']][] = $form;
                }
            } catch (\Exception $exc) {
                
            }
        } else {
            $content['selectedCategory'] = null;
            $content['selectedCategoryName'] = null;
            $content['forms'] = array();
            $content['links'] = '';
            $config["base_url"] = base_url() . $segment['baseUrl'];
        }

        $content['search'] = $param['search'];
        $content['urlString'] = base_url($this->uri->uri_string());
        $content['base_url'] = $config["base_url"];



        $this->load->view('header');
        $this->load->view('download/forms', $content);
        $this->load->view('footer');
    }

    function getMenuType($url) {
        switch ($url) {
            case 'act_rules':
                $data['type'] = 1;
                $data['contentTitle'] = 'Act & Rules';
                $data['baseUrl'] = 'download/act_rules';
                $data['category'] = false;
                break;
            case 'softwares':
                $data['type'] = 2;
                $data['contentTitle'] = 'Software';
                $data['baseUrl'] = 'download/softwares';
                $data['category'] = false;
                break;
            case 'fonts':
                $data['type'] = 3;
                $data['contentTitle'] = 'Fonts';
                $data['baseUrl'] = 'download/fonts';
                $data['category'] = false;
                break;
            case 'forms':
                $data['type'] = 4;
                $data['contentTitle'] = 'Forms';
                $data['baseUrl'] = 'download/forms';
                $data['category'] = true;
                break;
            case 'notice_poster':
                $data['type'] = 5;
                $data['contentTitle'] = 'Notics & Posters';
                $data['baseUrl'] = 'notice_poster';
                $data['category'] = false;
                break;

            case 'melakal':
                $data['type'] = 6;
                $data['contentTitle'] = 'Melakal';
                $data['route'] = 'melakal';
                $data['category'] = true;
                $data['baseUrl'] = 'melakal';
                break;

            case 'official_outlook':
                $data['type'] = 7;
                $data['contentTitle'] = 'Official outlook';
                $data['route'] = 'official_outlook';
                $data['baseUrl'] = 'official_outlook';
                $data['category'] = false;
                break;
                
            case 'academic_corner':
                $data['type'] = 8;
                $data['contentTitle'] = 'Academic Corner';
                $data['route'] = 'academic_corner';
                $data['baseUrl'] = 'download/academic_corner';
                $data['category'] = true;
                break;    

            default:
                $data['type'] = 0;
                $data['contentTitle'] = 'Downloads';
                $data['route'] = '';
                $data['baseUrl'] = 'download/forms';
                $data['category'] = false;
                break;
        }
        return $data;
    }

}
