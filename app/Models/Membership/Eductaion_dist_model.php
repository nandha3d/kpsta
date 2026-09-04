<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class Eductaion_dist_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($param) {
        $data['district_id'] = $param['office_id'];
        $data['updated_by'] = $this->session->userdata('id');
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];

        $this->db->insert('education_dist', $data);
        return $this->db->insert_id();
    }

    public function getAll($param = array()) {

        if (!in_array($this->aauthGroupId, [static::AAUTH_GROUP_STATE, static::AAUTH_GROUP_DISTRICT])) {
            return false;
        }

        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('b.name, b.id, d.name AS office, 3 AS group_id ');
        $this->db->from('education_dist b');
        $this->db->join('district d', 'b.district_id = d.id', 'left');

        if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->where(array("b.district_id" => $this->aauthOfficeId));
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("b.name LIKE ", '%' . $param['search'] . '%');
        }


        if (isset($param['compiledSelect']) && $param['compiledSelect']) {
            return $this->db->get_compiled_select();
        } else if (isset($param['officeId']) && $param['officeId']) {
            $this->db->where(array("b.district_id" => $param['officeId']));
        }

        $this->db->order_by('b.name ASC');
        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        if (!in_array($this->aauthGroupId, [static::AAUTH_GROUP_STATE, static::AAUTH_GROUP_DISTRICT])) {
            return false;
        }

        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(b.id) as count');
        $this->db->from('education_dist b');
        $this->db->join('district d', 'b.district_id = d.id', 'left');

        if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->where(array("d.id" => $this->aauthOfficeId));
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("b.name LIKE ", '%' . $param['search'] . '%');
        }

        if (isset($param['officeId']) && $param['officeId']) {
            $this->db->where(array("b.district_id" => $param['officeId']));
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, name, district_id AS office_id, code');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('education_dist');
        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $param) {
        $data['district_id'] = $param['office_id'];
        $data['updated_by'] = $this->session->userdata('id');
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];

        $this->db->where(array("id" => $id));
        if ($this->db->update('education_dist', $data)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('education_dist', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("education_dist_id" => $id));
        $query = $this->db->get('sub_dist');
        if ($query->num_rows() > 0) {
            return false;
        }
        $this->db->where(array("id" => $id));
        if ($this->db->delete('education_dist')) {
            return true;
        }
        return false;
    }

    function checkDuplicateCode($shortCode, $id) {
        if ($id) {
            $this->db->where(array("id !=" => $id));
        }
        $this->db->where(array("UPPER(code)" => strtoupper($shortCode)));
        $query = $this->db->get('education_dist');
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

}
