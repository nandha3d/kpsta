<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class District_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $this->session->userdata('id');



        $this->db->insert('office_bearer', $data);
        return $this->db->insert_id();
    }

    public function getAllDesignation() {
        $this->db->select('id, name');
        $this->db->order_by('id');
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    public function getAllDistrict($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 20;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.district,o.website_url');
        $this->db->from('district o');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.name LIKE ", '%' . $param['search'] . '%');
        }

        $this->db->order_by('o.id');
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllDistrictCount($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(o.id) as count');
        $this->db->from('district o');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }

        if ($param['search']) {
            $this->db->where("o.district LIKE ", '%' . $param['search'] . '%');
        }


        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 60;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;


        $this->db->select('o.id, o.name,o.phone, o.email, o.is_publish, o.image, c.name as designation,d.district as districtName, d.website_url, o.year, o.is_former ');
        $this->db->from('district_office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');
        $this->db->join('district d', 'o.district = d.id', 'left');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }

        if (isset($param['is_former'])) {
            if ($param['is_former'] == 0) {
                // Active: not manually marked as former, and (no year set OR year >= active term)
                $this->db->where("o.is_former", 0);
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.year IS NULL");
                    $this->db->or_where("o.year", "");
                    $this->db->or_where("o.year >=", $param['active_term']);
                    $this->db->group_end();
                }
            } else {
                // Former: manually marked as former OR year < active term
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.is_former", 1);
                    $this->db->or_group_start();
                    $this->db->where("o.year !=", "");
                    $this->db->where("o.year IS NOT NULL");
                    $this->db->where("o.year <", $param['active_term']);
                    $this->db->group_end();
                    $this->db->group_end();
                } else {
                    $this->db->where("o.is_former", 1);
                }
            }
        }

        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.name LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['designation']) && $param['designation']) {
            $this->db->where_in("o.designation", explode(',', $param['designation']));
        }

        $this->db->order_by('d.id, o.designation');
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(o.id) as count');
        $this->db->from('office_bearer o');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }

        if ($param['search']) {
            $this->db->where("o.name LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['designation']) && $param['designation']) {
            $this->db->where_in("o.designation", explode(',', $param['designation']));
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, district, website_url');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('district');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);

        $this->db->where(array("id" => $id));
        if ($this->db->update('district', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('office_bearer', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('office_bearer', array('is_delete' => 1))) {
            return true;
        }
        return false;
    }

    public function isSingleDesignation($designation, $districtId) {
        $this->db->where(array("is_single" => 1));
        $this->db->where(array("id" => $designation));

        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows()) {
            $this->db->where(array("designation" => $designation));
            $this->db->where(array("district" => $districtId));
            $query = $this->db->get('district_office_bearer');
            if ($query->num_rows()) {
                return TRUE;
            }
        }
        return false;
    }

    function getDistrictOfficeBearer($param) {

        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.name,o.phone, o.email, o.is_publish, o.image, c.name as designation, o.designation as designationId, o.year, o.is_former');
        $this->db->from('district_office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }
        
        if (isset($param['is_former'])) {
            if ($param['is_former'] == 0) {
                // Active: not manually marked as former, and (no year set OR year >= active term)
                $this->db->where("o.is_former", 0);
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.year IS NULL");
                    $this->db->or_where("o.year", "");
                    $this->db->or_where("o.year >=", $param['active_term']);
                    $this->db->group_end();
                }
            } else {
                // Former: manually marked as former OR year < active term
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.is_former", 1);
                    $this->db->or_group_start();
                    $this->db->where("o.year !=", "");
                    $this->db->where("o.year IS NOT NULL");
                    $this->db->where("o.year <", $param['active_term']);
                    $this->db->group_end();
                    $this->db->group_end();
                } else {
                    $this->db->where("o.is_former", 1);
                }
            }
        }
        
        if (isset($param['district']) && $param['district']) {
            $this->db->where(array("o.district" => $param['district']));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.name LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['designation']) && $param['designation']) {
            $this->db->where_in("o.designation", explode(',', $param['designation']));
        }

        if ($param['limit'] == 2) {
            $this->db->where('o.designation IN ( 1, 2, 3 )');
        }

        $this->db->order_by('o.designation');
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCountDistrictOfficeBearer($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(o.id) as count');
        $this->db->from('district_office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }

        if (isset($param['is_former'])) {
            if ($param['is_former'] == 0) {
                // Active: not manually marked as former, and (no year set OR year >= active term)
                $this->db->where("o.is_former", 0);
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.year IS NULL");
                    $this->db->or_where("o.year", "");
                    $this->db->or_where("o.year >=", $param['active_term']);
                    $this->db->group_end();
                }
            } else {
                // Former: manually marked as former OR year < active term
                if (isset($param['active_term'])) {
                    $this->db->group_start();
                    $this->db->where("o.is_former", 1);
                    $this->db->or_group_start();
                    $this->db->where("o.year !=", "");
                    $this->db->where("o.year IS NOT NULL");
                    $this->db->where("o.year <", $param['active_term']);
                    $this->db->group_end();
                    $this->db->group_end();
                } else {
                    $this->db->where("o.is_former", 1);
                }
            }
        }

        if (isset($param['district']) && $param['district']) {
            $this->db->where(array("o.district" => $param['district']));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.name LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['designation']) && $param['designation']) {
            $this->db->where_in("o.designation", explode(',', $param['designation']));
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function addDistrictOfficeBearer($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $this->session->userdata('id');


        $this->db->insert('district_office_bearer', $data);
        return $this->db->insert_id();
    }

    public function getByIdDistrictOfficeBearer($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('district_office_bearer');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function updateDistrictOfficeBearer($id, $formValues) {
        unset($formValues['id']);
        $this->db->where(array("id" => $id));
        if ($this->db->update('district_office_bearer', $formValues)) {
            return true;
        }
        return false;
    }

    public function publishDistrictOfficeBearer($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('district_office_bearer', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function deleteDistrictOfficeBearer($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('district_office_bearer')) {
            return true;
        }
        return false;
    }

}
