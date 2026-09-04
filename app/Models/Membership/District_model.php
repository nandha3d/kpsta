<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class District_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($param) {
        $data['updated_by'] = $this->session->userdata('id');
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];
        $data['state_id'] = 1;

        $this->db->insert('district', $data);
        return $this->db->insert_id();
    }

    public function getAll($param = array()) {
        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            return false;
        }
        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('d.name, d.id,"Kerala" AS office ');
        $this->db->from('district d');

        if (isset($param['search']) && $param['search']) {
            $this->db->where("d.name LIKE ", '%' . $param['search'] . '%');
        }

        $this->db->order_by('d.name ASC');
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

        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            return false;
        }

        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(d.id) as count');
        $this->db->from('district d');

        if (isset($param['search']) && $param['search']) {
            $this->db->where("d.name LIKE ", '%' . $param['search'] . '%');
        }


        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, name, state_id AS office_id, code');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('district');
        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $param) {
        $data['updated_by'] = $this->session->userdata('id');
        $data['code'] = strtoupper($param['code']);
        $data['name'] = $param['name'];
        $data['state_id'] = 1;
        $this->db->where(array("id" => $id));
        if ($this->db->update('district', $data)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('district', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("district_id" => $id));
        $query = $this->db->get('education_dist');
        if ($query->num_rows() > 0) {
            return false;
        }
        $this->db->where(array("id" => $id));
        if ($this->db->delete('district')) {
            return true;
        }
        return false;
    }

    function checkDuplicateCode($shortCode, $id) {
        if ($id) {
            $this->db->where(array("id !=" => $id));
        }
        $this->db->where(array("UPPER(code)" => strtoupper($shortCode)));
        $query = $this->db->get('district');
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

}
