<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  Extended the core Router class to allow for sub-sub-folders in the controllers directory.
 */

class MY_Model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

}

class Membership_Model extends CI_Model {

    const AAUTH_GROUP_STATE = 1;
    const AAUTH_GROUP_DISTRICT = 2;
    const AAUTH_GROUP_EDUCATION_DIST = 3;
    const AAUTH_GROUP_SUB_DIST = 4;
    const AAUTH_GROUP_BRANCH = 5;
    const AAUTH_GROUP_SCHOOL = 6;
    const AAUTH_GROUP_STATE_STAFF = 7;

    public $aauthGroupId = false;
    public $aauthOfficeId = false;
    public $db = false;
    public $year = false;
    public $startedYear = 2017;

    function __construct() {
        parent::__construct();
        if (!is_object($this->db)) {
            $this->db = $this->load->database('membership', TRUE, TRUE);
        }
        $this->aauthGroupId = $this->session->userdata('group');
        $this->aauthOfficeId = $this->session->userdata('office');

        if ($this->aauthGroupId == self::AAUTH_GROUP_STATE_STAFF) {
            $this->aauthGroupId = self::AAUTH_GROUP_STATE;
        }
    }

    public function getYear() {
        $this->load->model("membership/Config_model");
        $this->year = $this->Config_model->getLabelValue('year');
    }

}
