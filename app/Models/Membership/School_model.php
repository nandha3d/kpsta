<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class School_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($param) {
        $data['branch_id'] = $param['office_id'];
        $data['school_type'] = $param['school_type'];
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];
        $data['updated_by'] = $this->session->userdata('id');


        $this->db->insert('school', $data);
        return $this->db->insert_id();
    }

    public function getAll($param = array()) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;


        $this->db->select('s.*, b.name AS office, sd.name as sdName ');
        $this->db->from('school s');

        $this->loadCondition($param);

        $this->db->group_by('s.id');
        $this->db->order_by('b.name ASC, s.name ASC');
        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getAllCount($param) {

        $this->db->select('count(s.id) as count');
        $this->db->from('school s');

        $this->loadCondition($param);

        $query = $this->db->get();
        $result = $query->row(0, 'array');
        return $result['count'];
    }

    function loadCondition($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->join('branch b', 's.branch_id = b.id');
        $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
        if ($this->aauthGroupId == static::AAUTH_GROUP_BRANCH) {
            $this->db->where(array("s.branch_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->where(array("b.sub_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
           // $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
            $this->db->where(array("sd.education_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
           // $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
            $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
            $this->db->where(array("ed.district_id" => $this->aauthOfficeId));
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("s.name LIKE ", '%' . $param['search'] . '%');
        }

        if (isset($param['groupId']) && isset($param['officeId'])) {
            if ($param['groupId'] == static::AAUTH_GROUP_BRANCH) {
                $this->db->where(array("s.branch_id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_SUB_DIST) {
                $this->db->where(array("b.sub_dist_id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_EDUCATION_DIST) {
                $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
                $this->db->where(array("sd.education_dist_id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_DISTRICT) {
                $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
                $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
                $this->db->where(array("ed.district_id" => $param['officeId']));
            }
        }
        
        
        if (isset($param['officeId']) && $param['officeId']) {
            $this->db->where(array("s.branch_id" => $param['officeId']));
        }
        
//        if (isset($param['officeId']) && $param['officeId'] && $param['officeId'] != 'null') {
//            $this->db->where(array("s.branch_id" => $param['officeId']));
//        }
    }

    function getSchoolTypeCount($param) {

        $this->db->select('COUNT(s.school_type) AS count, s.school_type');
        $this->db->from('school s');

        $this->loadCondition($param);

        $this->db->group_by('s.school_type');
//        $this->db->order_by('s.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getById($id) {
        $this->db->select('id, name, code, branch_id AS office_id, school_type');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('school');
        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $param) {
        $data['branch_id'] = $param['office_id'];
        $data['school_type'] = $param['school_type'];
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];
        $data['updated_by'] = $this->session->userdata('id');


        $this->db->where(array("id" => $id));
        if ($this->db->update('school', $data)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('branch', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("school_id" => $id));
        $query = $this->db->get('teacher_details');
        if ($query->num_rows() > 0) {
            return false;
        }
        $this->db->where(array("id" => $id));
        if ($this->db->delete('school')) {
            return true;
        }
        return false;
    }

}
