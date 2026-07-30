<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Branch_model extends Membership_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($param) {
        $data['updated_by'] = $this->session->userdata('id');
        $data['sub_dist_id'] = $param['office_id'];
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];

        $this->db->insert('branch', $data);
        return $this->db->insert_id();
    }

    public function getAll($param = array()) {
        if ($this->aauthGroupId == static::AAUTH_GROUP_BRANCH) {
            return false;
        }

        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('b.name, b.id, sd.name AS office');
        $this->db->from('branch b');
        $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id');

        if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->where(array("b.sub_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
            $this->db->where(array("ed.id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
            $this->db->join('district d', 'ed.district_id = d.id', 'left');
            $this->db->where(array("d.id" => $this->aauthOfficeId));
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("b.name LIKE ", '%' . $param['search'] . '%');
        }

        if (isset($param['officeId']) && $param['officeId']) {
            $this->db->where(array("b.sub_dist_id" => $param['officeId']));
        }

        $this->db->order_by('b.name ASC');
        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getAllCount($param, $isPublish = FALSE) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(b.id) as count');
        $this->db->from('branch b');
        $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id');

        if ($this->aauthGroupId == static::AAUTH_GROUP_BRANCH) {
            return 0;
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->where(array("b.sub_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
            $this->db->where(array("ed.id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
            $this->db->join('district d', 'ed.district_id = d.id', 'left');
            $this->db->where(array("d.id" => $this->aauthOfficeId));
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("b.name LIKE ", '%' . $param['search'] . '%');
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, name, sub_dist_id As office_id, code');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('branch');
        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $param) {
        $data['updated_by'] = $this->session->userdata('id');
        $data['sub_dist_id'] = $param['office_id'];
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];
        $this->db->where(array("id" => $id));
        if ($this->db->update('branch', $data)) {
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
        $this->db->where(array("branch_id" => $id));
        $query = $this->db->get('school');
        if ($query->num_rows() > 0) {
            return false;
        }
        $this->db->where(array("id" => $id));
        if ($this->db->delete('branch')) {
            return true;
        }
        return false;
    }

}
