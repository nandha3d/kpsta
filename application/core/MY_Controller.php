<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller {

    public $newUrl = null;
    public static $menuList = false;
    protected $CI;

    /**
     * constructor function
     */
    function __construct($view = "admin") {
        parent::__construct();



        //check whether user is logged in
        if ($view == "admin") {
            $this->load->library("Aauth");
            if (!$this->aauth->is_loggedin() || null !== $this->session->userdata('office')) {
                redirect('admin/logout');
            }

            //set menu for admin panel normal users.
            if ($this->session->userdata('group') <> 1) {
                //Check whether the user permitted to use this menu
                if (!$this->aauth->isMenuActive()) {
                    redirect('admin/logout');
                }

                $this->load->vars(array('adminMenuList' => $this->aauth->getAdminMenuList()));
            }


            //delete cached home page
            if (preg_match('(order-circular|news|gallery|quicklink|slider|reaction_gallery|flash_news|office_bearer)', $this->uri->uri_string()) === 1) {
                if (preg_match('(add|update|publish)', $this->uri->uri_string()) === 1) {
                    $this->deleteCachedPage('/home');
                    $this->deleteCachedPage('/');
                }
            }
        }
    }

    function BootsrapPaginationConfig() {
        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
//        $config['first_link'] = '<li  class="paginate_button previous ">';
        $config['last_link'] = false;

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last ';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';


        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        return $config;
    }

    public function getNewUrl($data = array(), $removeKey = array()) {
        $params = array();
        $urlString = base_url($this->uri->uri_string());

        $parsed = parse_url($urlString . '?' . $_SERVER['QUERY_STRING']);

        if (isset($parsed['query'])) {
            $query = $parsed['query'];
            parse_str($query, $params);
        }

        //Reset query string value
        foreach ($data as $key => $val) {
            if (isset($params[$key])) {
                $params[$key] = $val;
            }
        }

        //remove if any query string have null value
        foreach ($params as $key => $val) {
            if (empty($val) || isset(array_flip($removeKey)[$key])) {
                unset($params[$key]);
            }
        }



        if (count($params)) {
            $newUrl = $urlString . '?' . http_build_query($params);
        } else {
            $newUrl = $urlString;
        }

        return $newUrl;
    }

    public function deleteFile($file = false) {
        if ($file && is_file($file)) {
            try {
                unlink($file);
            } catch (\Exception $exc) {
                
            }
        }
        return true;
    }

    public function deleteCachedPage($url) {
        // Deletes cache for $url
        $this->output->delete_cache($url);
    }

    /**
     * This function used to create Dropdown data
     * @param type $data
     * @param type $property
     * @param type $defaultValue
     * @return type
     */
    public function createSelectDropDown($data = array(), $property = '', $defaultValue = false, $exclude = []) {
        if (!is_array($data)) {
            return $data;
        }

        $return = [];
        if ($defaultValue) {
            $return['0'] = $defaultValue;
        }
        foreach ($data as $row) {
            if(in_array($row['id'], $exclude)){
                continue;
            }
            $return[$row['id']] = $row[$property];
        }

        return $return;
    }

    /**
     * Shared plumbing for the "Delete Selected" toolbar button.
     *
     * The posted ids are handed one at a time to the caller's own single-row
     * delete, so per-controller cleanup (uploaded files, thumbnails) stays in
     * one place instead of being duplicated for the batch path.
     *
     * @param callable $deleteOne receives an id, returns TRUE when removed
     * @return array counts plus a human readable message
     */
    protected function runBatchDelete($deleteOne) {
        $ids = $this->input->post('ids');
        if (!is_array($ids)) {
            $ids = ($ids === NULL || $ids === '') ? array() : array($ids);
        }
        // ids arrive as strings from the form post
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $result = array('code' => 'error', 'deleted' => 0, 'failed' => 0);

        if (empty($ids)) {
            $result['message'] = 'No rows were selected.';
            return $result;
        }

        foreach ($ids as $id) {
            if (call_user_func($deleteOne, $id)) {
                $result['deleted']++;
            } else {
                $result['failed']++;
            }
        }

        $result['code'] = $result['deleted'] ? 'success' : 'error';
        $result['message'] = $result['deleted'] . ' item(s) deleted'
                . ($result['failed'] ? ', ' . $result['failed'] . ' could not be deleted' : '');

        return $result;
    }

}

class Public_Controller extends MY_Controller {

    public function __construct() {
        parent::__construct("public");

        $this->load->model('Slider_model');

        // Candidate page keys for the heading background, most specific first:
        // "download/forms" style keys win over the bare "download" section, and
        // a detail page (gallery/<album>) still falls back to its section.
        $pages = array();
        $seg1 = $this->uri->segment(1);
        $seg2 = $this->uri->segment(2);
        if ($seg1) {
            if ($seg2 && !is_numeric($seg2)) {
                $pages[] = $seg1 . '/' . $seg2;
            }
            $pages[] = $seg1;
        }

        $headingBgResult = $this->Slider_model->getHeadingBgForPage($pages);
        $headingBgImage = !empty($headingBgResult) ? base_url('uploads/slider/' . $headingBgResult['image']) : '';
        $this->load->vars(['heading_bg_image' => $headingBgImage]);
    }

    public function httpify($link, $append = 'http://', $allowed = array('http://', 'https://')) {
        $link = trim($link);

        if (empty($link)) {
            return false;
        }

        $found = false;
        foreach ($allowed as $protocol) {
            if (strpos($link, $protocol) !== 0) {
                $found = true;
            }
        }

        if ($found) {
            return $link;
        }
        return $append . $link;
    }



}

class Membership_Controller extends MY_Controller {

    const AAUTH_GROUP_STATE = 1;
    const AAUTH_GROUP_DISTRICT = 2;
    const AAUTH_GROUP_EDUCATION_DIST = 3;
    const AAUTH_GROUP_SUB_DIST = 4;
    const AAUTH_GROUP_BRANCH = 5;
    const AAUTH_GROUP_SCHOOL = 6;
    const AAUTH_GROUP_STATE_STAFF = 7;

    public $aauthGroupId = false;
    public $aauthOfficeId = false;
    public $newUrl = null;
    public $year = null;
    public static $menuList = false;
    protected $CI;

    public function __construct() {
        parent::__construct('membership');

        date_default_timezone_set('Asia/Kolkata');

        $this->load->library("Aauth");

        $this->CI = & get_instance();

        $this->aauth->loadDatabase('membership');

        if (!in_array($this->uri->segment(2), ["logout", "login"])) {
            if (!$this->session->userdata('group') || !$this->aauth->is_loggedin()) {
                redirect('membership/logout');
            }
        }

        $this->output->nocache();

        $this->aauthGroupId = $this->session->userdata('group');
        $this->aauthOfficeId = $this->session->userdata('office');

        if ($this->aauthGroupId == self::AAUTH_GROUP_STATE_STAFF) {
            $this->aauthGroupId = self::AAUTH_GROUP_STATE;
        }

        $this->load->vars(array('loggedInUserInfo' => $this->aauth->loggedInUserInfo()));
        $this->load->vars(array('configVars' => $this->CI->config->item('aauth')));
        
        $this->load->model("membership/Config_model");
        $this->year = $this->Config_model->getLabelValue('year');
    }

    
    /**
     * This is common function  used to return office data with respective group ID
     * @param type $groupId
     * @param type $createSelect
     * @return string
     */
    function getOffice($groupId = false, $createSelect = TRUE, $ajax = true, $param = array()) {
        $dataSelect = [];
        $defaultValue = $label = $previousLabel = false;
        if (!$groupId) {
            $groupId = $this->input->post('groupId');
            $param['officeId'] = $this->input->post('officeId');
        }

        $selectedGroupId = $this->input->post('selectedGroupId');
        if (($selectedGroupId - 1) == $this->aauthGroupId) {
            $groupId = $selectedGroupId - 1;
        } else if ($groupId < $this->aauthGroupId) {
            $groupId = $this->aauthGroupId + 1;
        } else if ($groupId == $this->aauthGroupId) {
            $groupId = $this->aauthGroupId + 1;
        }


        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $dataSelect = $this->district_model->getAll($param);
            $defaultValue = "- - - SELECT DISTRICT - - -";
            $label = "DISTRICT";
            $previousLabel = 'State';
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $dataSelect = $this->eductaion_dist_model->getAll($param);
            $defaultValue = "- - - SELECT EDUCATION DISTRICT - - -";
            $label = "EDUCATION DISTRICT";
            $previousLabel = 'District';
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $dataSelect = $this->sub_dist_model->getAll($param);
            $defaultValue = "- - - SELECT SUB DISTRICT - - -";
            $label = "SUB DISTRICT";
            $previousLabel = 'EDUCATION DISTRICT';
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $dataSelect = $this->branch_model->getAll($param);
            $defaultValue = "- - - SELECT BRANCH - - -";
            $label = "BRANCH";
            $previousLabel = 'SUB DISTRICT';
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $dataSelect = $this->school_model->getAll($param);
            $defaultValue = "- - - SELECT SCHOOL - - -";
            $label = "School";
            $previousLabel = 'Branch';
        }


        if (isset($param['compiledSelect']) && $param['compiledSelect']) {
            return $dataSelect;
        }

        $data['groupId'] = $groupId;
        $data['previousLabel'] = $previousLabel;
        $data['officeLabel'] = $label;
        if ($createSelect) {
            $data['officeSelect'] = $this->createSelectDropDown($dataSelect, 'name', $defaultValue);
        } else {
            $data['officeSelect'] = $dataSelect;
        }

        if ($groupId == 7) {
            $data['officeSelect'] = array("0" => "- - -SELECT OFFICE- - -", 100 => "State");
            $data['officeLabel'] = "Staff office";
        }


        if ($this->input->is_ajax_request() && $ajax) {
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        } else {
            return $data;
        }
    }

    function getOfficeCount($groupId = false, $ajax = true, $param = array()) {
        $dataSelect = [];
        $defaultValue = $label = $previousLabel = false;
        if (!$groupId) {
            $groupId = $this->input->post('groupId');
            $param['officeId'] = $this->input->post('officeId');
        }


        $selectedGroupId = $this->input->post('selectedGroupId');
        if (($selectedGroupId - 1) == $this->aauthGroupId) {
            $groupId = $selectedGroupId - 1;
        } else if ($groupId < $this->aauthGroupId) {
            $groupId = $this->aauthGroupId + 1;
        }


        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $dataSelect = $this->district_model->getAllCount($param);
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $dataSelect = $this->eductaion_dist_model->getAllCount($param);
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $dataSelect = $this->sub_dist_model->getAllCount($param);
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $dataSelect = $this->branch_model->getAllCount($param);
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $dataSelect = $this->school_model->getAllCount($param);
        }

        if ($this->input->is_ajax_request() && $ajax) {
            echo json_encode($dataSelect);
            exit;
        } else {
            return $dataSelect;
        }
    }

    function getOfficeById($groupId = false, $id = false) {
        $dataSelect = [];
        $defaultValue = $label = $previousLabel = false;
        if (!$groupId) {
            $groupId = $this->input->post('val');
        }

        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $dataSelect = $this->district_model->getById($id);
            $defaultValue = "- - - SELECT DISTRICT - - -";
            $label = "DISTRICT";
            $previousLabel = 'State';
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $dataSelect = $this->eductaion_dist_model->getById($id);
            $defaultValue = "- - - SELECT EDUCATION DISTRICT - - -";
            $label = "EDUCATION DISTRICT";
            $previousLabel = 'District';
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $dataSelect = $this->sub_dist_model->getById($id);
            $defaultValue = "- - - SELECT SUB DISTRICT - - -";
            $label = "SUB DISTRICT";
            $previousLabel = 'EDUCATION DISTRICT';
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $dataSelect = $this->branch_model->getById($id);
            $defaultValue = "- - - SELECT BRANCH - - -";
            $label = "BRANCH";
            $previousLabel = 'SUB DISTRICT';
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $dataSelect = $this->school_model->getById($id);
            $defaultValue = "- - - SELECT SCHOOL - - -";
            $label = "School";
            $previousLabel = 'Branch';
        }


        $data['previousLabel'] = $previousLabel;
        $data['officeLabel'] = $label;

        $data['officeSelect'] = $dataSelect;


        return $data;
    }

    function generateUserName($groupId = false, $officeId = false, $ajax = true) {
        $districtPref = false;
        if (!$groupId || !$officeId) {
            $groupId = $this->input->post('groupId');
            $officeId = $this->input->post('officeId');
        }

        $lengthCount = 4;
        $prefixArr = array(1 => 'ST', 2 => 'DT', 3 => 'ED', 4 => 'SD', 5 => 'BR', 6 => 'SH', 7 => 'STS');
        $prefix = $prefixArr[$groupId];

        $this->load->model("membership/Main_model", "main_model");
        $officeSelect = $this->getOfficeById($groupId, $officeId);
        if (isset($officeSelect['officeSelect']['code'])) {
            $districtPref = $officeSelect['officeSelect']['code'];
        }

        if ($groupId == 7) {
            $districtPref = 100;
        }

        if (!$districtPref) {
            $data['code'] = 'error';
            $data['error'][] = 'Office Code is Empty!... Please update office code';
            if ($this->input->is_ajax_request() && $ajax) {
                echo json_encode($data);
                exit;
            } else {
                return false;
            }
        }


        $userCount = $this->aauth->getOfficeUserCount($groupId, $officeId);
        if ($userCount >= 10) {
            $data['code'] = 'error';
            $data['message'] = 'Maximum user allowed';
            echo json_encode($data);
            exit;
        }
        $count = $lengthCount - strlen((string) $userCount) - strlen((string) $officeId);
        if ($count < 0) {
            $count = 0;
        }

        $zeros = str_repeat("0", $count);

        $data['data'] = $prefix . '_' . $districtPref . '_' . $officeId . $zeros . $userCount;

        if ($this->input->is_ajax_request() && $ajax) {
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        } else {
            return $data['data'];
        }
    }

    function getGroupOfficeId($getGroupIdDefault = false, $getOfficeIdDefault = false) {
        $getGroupId = $this->input->get('group');
        $getofficeId = $this->input->get('office');
        $group = $this->aauthGroup_model->getAllGroup();
        $groupDropDown = $this->createSelectDropDown($group, 'name');
        if ($getGroupIdDefault) {
            $getGroupId = $getGroupIdDefault;
            $getofficeId = $getOfficeIdDefault;
        } else if (!$getGroupId) {
            if ($this->uri->segment(2) == "teacher") {
                $getGroupId = 0;
            } else {
                $getGroupId = key($groupDropDown);
            }
            $getofficeId = $getofficeId;
        } else {
            if (!in_array($getGroupId, array_flip($groupDropDown))) {
                die('you are dead');
            }
        }

        return array($getGroupId, $getofficeId);
    }

    function checkDuplicateCode($groupId, $shortCode, $id = false) {
        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $dataSelect = $this->district_model->checkDuplicateCode($shortCode, $id);
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $dataSelect = $this->eductaion_dist_model->checkDuplicateCode($shortCode, $id);
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $dataSelect = $this->sub_dist_model->checkDuplicateCode($shortCode, $id);
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $dataSelect = $this->branch_model->checkDuplicateCode($shortCode, $id);
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $dataSelect = $this->school_model->checkDuplicateCode($shortCode, $id);
        }

        return $dataSelect;
    }

}
