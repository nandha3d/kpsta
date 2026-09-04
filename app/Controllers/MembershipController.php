<?php

namespace App\Controllers;

/**
 * Ported from CodeIgniter 3's Membership_Controller.
 *
 * Holds the group/office hierarchy used across the membership module --
 * State, District, Education District, Sub District, Branch, School -- and the
 * cascading office lookups the forms drive over AJAX. Behaviour is unchanged.
 */
class MembershipController extends AppController
{
    protected string $ci3View = 'membership';


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

    public function ci3Init(): void {
        parent::ci3Init();

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
