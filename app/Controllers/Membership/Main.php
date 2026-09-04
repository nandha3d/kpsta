<?php

namespace App\Controllers\Membership;

use App\Controllers\MembershipController;
class Main extends MembershipController {

    public function ci3Init(): void {
        parent::ci3Init();

        $this->load->library("Aauth");

        $this->load->model("membership/AauthGroup_model", "aauthGroup_model");
        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');
    }

    public function index() {
        $data = false;

        $data['userForm'] = $this->createForm(base_url('membership/main/add'));
        $data['content'] = $this->getContent();

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/membership.js'];

        $this->load->view('membership/header', $header);
        $this->load->view('membership/main/main', $data);
        $this->load->view('membership/footer', $footer);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('membership/main/add');
        $data['formValues'] = $formValues;
        $getOffice = array();
        $data['officeSelect'] = [ '' => '- - - SELECT GROUP - - -'];
        $data['officeSearch'] = array();

        $group = $this->aauthGroup_model->getAllGroup(7);
        $reject = ($this->aauthGroupId == 4) ? [2, 3, 4] : [2, 3, 4, 5];
        $data['groupSelect'] = $this->createSelectDropDown($group, 'name', '- - - SELECT GROUP - - -', $reject);
        if (isset($formValues['group_id']) && $formValues['group_id']) {
            $formOfficeId = $formValues['group_id'] - 1;
            $getOffice = $this->getOffice($formOfficeId, TRUE, false);
            $data['officeSelect'] = $getOffice['officeSelect'];
            $data['officeLabel'] = $getOffice['officeLabel'];
        }


        $searchGroupId = $this->input->get('group') ? ($this->input->get('group') - 1) : false;
        if ($searchGroupId && (($searchGroupId ) > $this->aauthGroupId)) {
            $office = $this->getOffice($searchGroupId, TRUE, false);
            $data['officeSearch'] = $office['officeSelect'];
        }
        $data['groupSearch'] = $this->createSelectDropDown($group, 'name');

        return $this->load->view('membership/main/form', $data, TRUE);
    }

    function getContent($getGroupIdDefault = false, $getOfficeIdDefault = false) {

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId($getGroupIdDefault, $getOfficeIdDefault);

        $content['groupId'] = $getGroupId;
        $content['officeId'] = $param['officeId'] = $getofficeId;

        $param['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));
        $param['limit'] = 20;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        $this->newUrl = $this->getNewUrl([
            'group' => $getGroupId,
            'office' => $getofficeId
                ]
        );

        //PAGINATION CONFIGS
        $config["total_rows"] = $content['total_rows'] = $this->getOfficeCount($getGroupId, false, $param);
        $config["base_url"] = $this->getNewUrl([
            'group' => $getGroupId,
            'office' => $getofficeId
                ], ['page']
        );

        $config["per_page"] = $param['limit'];

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();
        $content['config'] = $config;

        $this->load->model("membership/School_model", "school_model");
        $content['consolidated'] = $this->school_model->getSchoolTypeCount($param);

        $content['data'] = $this->getOffice($getGroupId, false, false, $param);
        return $this->load->view('membership/main/content', $content, TRUE);
    }

    public function add() {

        $formValues = $this->formValidation();

        if ($this->form_validation->run() == FALSE) {
            $data['form'] = $this->createForm(base_url('membership/main/add'), $formValues, 'Add Details');
            $data['code'] = 'error';
            $data['data'] = array(validation_errors());
            echo json_encode($data);
            exit;
        }


        //Insert values
        if ($formValues['group_id'] == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $add = $this->district_model->add($formValues);
        } else if ($formValues['group_id'] == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $add = $this->eductaion_dist_model->add($formValues);
        } else if ($formValues['group_id'] == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $add = $this->sub_dist_model->add($formValues);
        } else if ($formValues['group_id'] == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $add = $this->branch_model->add($formValues);
        } else if ($formValues['group_id'] == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $add = $this->school_model->add($formValues);
        }

        if ($add) {
            $data['newUrl'] = $this->newUrl;
            $data['groupId'] = $formValues['group_id'];
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent($formValues['group_id']);
        } else {
            $data['code'] = 'success';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function shortCodeValidate($code) {
        if (!$code) {
            return TRUE;
        }
        $id = $id = $this->uri->segment(4);
        $checkDuplicateCode = $this->checkDuplicateCode($this->input->post('group_id'), $this->input->post('code'), $id);
        if ($checkDuplicateCode) {
            $this->form_validation->set_message('shortCodeValidate', 'Duplicate short code');
            return FALSE;
        }
        return TRUE;
    }

    function formValidation() {
        if ($this->input->post('group_id') == ($this->aauthGroupId + 1 )) {
            $_POST['office_id'] = $this->aauthOfficeId;
        }

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        if ($this->input->post('group_id') != 2) {
            $this->form_validation->set_rules('office_id', 'Office', 'trim|required');
        }
        $this->form_validation->set_rules('group_id', 'Group', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        if (in_array($this->input->post('group_id'), [2, 3, 4])) {
            $this->form_validation->set_rules('code', 'Short Code', 'trim|required|callback_shortCodeValidate');
        }
        if (in_array($this->input->post('group_id'), [6])) {
            $this->form_validation->set_rules('school_type', 'School type', 'trim|required');
        }
        $this->form_validation->set_error_delimiters("<p>", "</p>");


        $formValues = [
            'name' => $this->input->post('name'),
            'group_id' => $this->input->post('group_id'),
            'office_id' => $this->input->post('office_id'),
            'code' => $this->input->post('code'),
            'school_type' => $this->input->post('school_type'),
        ];

        return $formValues;
    }

    /*
     * It create html form For editing User info 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        if (!$id) {
            echo json_encode($data);
            exit;
        }
        $groupId = $this->input->get('group_id');
        $info = $this->getOfficeById($groupId, $id);
        if ($info) {
            $info = $info['officeSelect'];
            $info['group_id'] = $groupId;
            $info['disabled'] = true;
            $url = base_url('membership/main/update/' . $id . '?group_id=' . $groupId);
            $data['form'] = $this->createForm($url, $info, 'Edit Details');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update Admin user login info
     * @return json_endcode  data
     */
    public function update() {
        $_POST['group_id'] = $this->input->get('group_id');
        $formValues = $this->formValidation();
        $id = $this->uri->segment(4);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $groupId = $this->input->get('group_id');
            $info = $this->getOfficeById($groupId, $id);
            $data['code'] = 'error';
            //$formValues = $info['officeSelect'];
            $formValues['group_id'] = $groupId;
            $formValues['disabled'] = true;
            $url = base_url('membership/main/update/' . $id . '?group_id=' . $groupId);
            $data['form'] = $this->createForm($url, $formValues, 'Edit Details');
            echo json_encode($data);
            exit;
        }

        $groupId = $this->input->get('group_id');
//        $groupId = $formValues['group_id'];

        $info = $this->getOfficeById($groupId, $id);
        if (!$info) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        //update news
        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $add = $this->district_model->update($id, $formValues);
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $add = $this->eductaion_dist_model->update($id, $formValues);
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $add = $this->sub_dist_model->update($id, $formValues);
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $add = $this->branch_model->update($id, $formValues);
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $add = $this->school_model->update($id, $formValues);
        }

        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent($groupId);
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        $groupId = $this->input->get('group_id');

        if (!$id || !$groupId) {
            $data['message'] = 'INVALID EDIT';
            echo json_encode($data);
            exit;
        }

        if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->load->model("membership/District_model", "district_model");
            $delete = $this->district_model->delete($id);
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->load->model("membership/Eductaion_dist_model", "eductaion_dist_model");
            $delete = $this->eductaion_dist_model->delete($id);
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->load->model("membership/Sub_dist_model", "sub_dist_model");
            $delete = $this->sub_dist_model->delete($id);
        } else if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->load->model("membership/Branch_model", "branch_model");
            $delete = $this->branch_model->delete($id);
        } else if ($groupId == static::AAUTH_GROUP_SCHOOL) {
            $this->load->model("membership/School_model", "school_model");
            $delete = $this->school_model->delete($id);
        }

        $data['lastId'] = $id;
        if ($delete) {
            $data['code'] = 'success';
            $data['content'] = $this->getContent($groupId);
        } else {
            $data['code'] = 'error';
            $data['message'] = 'Please delete child entry first';
        }

        echo json_encode($data);
        exit;
    }

}
