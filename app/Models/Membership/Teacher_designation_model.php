<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class Teacher_designation_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['date'] = implode('-', array_reverse(explode('-', $data['date'])));
        $data['created_by'] = $this->session->userdata('id');

        $this->db->insert(' teacher_designation', $data);
        return $this->db->insert_id();
    }

    public function getAll($param = array()) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('d.name, d.id');
        $this->db->from(' teacher_designation d');

        if (isset($param['search']) && $param['search']) {
            $this->db->where("d.name LIKE ", '%' . $param['search'] . '%');
        }

        $this->db->order_by('d.position, d.id, d.name ASC');
        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        $query = $this->db->get();
         
        return $query->result_array();
    }

    public function getAllCount($param, $isPublish = FALSE) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(b.id) as count');
        $this->db->from(' teacher_designation b');

        if (isset($param['search']) && $param['search']) {
            $this->db->where("b.name LIKE ", '%' . $param['search'] . '%');
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, description, name');
        $this->db->where(array("id" => $id));
        $query = $this->db->get(' teacher_designation');
        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {

        $this->db->where(array("id" => $id));
        if ($this->db->update(' teacher_designation', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update(' teacher_designation', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete(' teacher_designation')) {
            return true;
        }
        return false;
    }

}
