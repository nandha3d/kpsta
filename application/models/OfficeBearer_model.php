<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class OfficeBearer_model extends CI_Model {

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


        if ($data['position'] <> 100) {
            $this->updatePosition($data);
        }


        $this->db->insert('office_bearer', $data);
        return $this->db->insert_id();
    }

    public function updatePosition($data) {
        $this->db->where(array("position" => $data['position']));
        $query = $this->db->get('office_bearer');
        if ($query->num_rows() > 0) {

            $this->db->query('UPDATE office_bearer SET position = position + 1 where position >= ' . $data['position'] . ' AND designation = ' . $data['designation']);
        }
    }

    public function getAllDesignation($param = array()) {
        $this->db->select('id, name');

        if (isset($param['limit']) && $param['limit'] == 3) {
            $this->db->where('id IN ( 1, 2, 3 )');
        }
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

    public function getAllSectionHeadings() {
        $this->db->select('DISTINCT(section_heading) as heading');
        $this->db->where('section_heading IS NOT NULL');
        $this->db->where('section_heading !=', '');
        $this->db->order_by('section_heading', 'ASC');
        $query = $this->db->get('office_bearer');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[$row->heading] = $row->heading;
            }
        }
        return $data;
    }

    public function getOrAddDesignation($designation) {
        if (is_numeric($designation)) {
            $this->db->where('id', $designation);
            $query = $this->db->get('office_bearer_designation');
            if ($query->num_rows() > 0) {
                return $designation;
            }
        }
        
        $this->db->where('name', $designation);
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->id;
        }

        $this->db->insert('office_bearer_designation', ['name' => $designation]);
        return $this->db->insert_id();
    }

    public function getDesignationById($id) {
        $this->db->select('id, name');
        $this->db->where('id', $id);
        $query = $this->db->get('office_bearer_designation');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    /**
     * Designations are shared across every office bearer, so the name has to
     * stay unique - two rows with the same name would split one section into
     * two on the public listing.
     */
    public function designationExists($name, $excludeId = false) {
        $this->db->where('name', $name);
        if ($excludeId) {
            $this->db->where('id !=', $excludeId);
        }
        $query = $this->db->get('office_bearer_designation');
        return $query->num_rows() > 0;
    }

    public function addDesignation($name) {
        $this->db->insert('office_bearer_designation', array('name' => $name));
        return $this->db->insert_id();
    }

    public function updateDesignation($id, $name) {
        $this->db->where('id', $id);
        return (bool) $this->db->update('office_bearer_designation', array('name' => $name));
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 15;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;

        $this->db->select('o.id, o.name, o.phone, o.email, o.is_publish, o.image, c.name as designation, o.designation as designationId, o.section_heading, o.year, o.is_former, o.level, o.position');
        $this->db->from('office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['level']) && $param['level'] !== '') {
            $this->db->where("o.level", $param['level']);
        }

        if (isset($param['district']) && $param['district'] !== '') {
            $this->db->group_start();
            $this->db->where("o.section_heading", $param['district']);
            $this->db->or_like("o.section_heading", $param['district']);
            $this->db->group_end();
        }

        if (isset($param['is_publish']) && $param['is_publish'] !== '') {
            $this->db->where("o.is_publish", $param['is_publish']);
        }

        // Public pages ask for published rows only, via isPublish.
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where("o.is_publish", 1);
        }

        $this->applyActiveTerm($param);

        if (isset($param['is_former']) && $param['is_former'] !== '') {
            $this->db->where("o.is_former", $param['is_former']);
        }

        if (isset($param['search']) && !empty($param['search'])) {
            $this->db->group_start();
            $this->db->like("o.name", $param['search']);
            $this->db->or_like("o.email", $param['search']);
            $this->db->or_like("o.phone", $param['search']);
            $this->db->or_like("o.section_heading", $param['search']);
            $this->db->or_like("c.name", $param['search']);
            $this->db->group_end();
        }

        if (isset($param['designation']) && !empty($param['designation'])) {
            $desigArr = is_array($param['designation']) ? $param['designation'] : explode(',', $param['designation']);
            $desigArr = array_filter($desigArr);
            if (!empty($desigArr)) {
                $this->db->where_in("o.designation", $desigArr);
            }
        }

        // Sorting options
        $sort = isset($param['sort']) ? $param['sort'] : 'position-asc';
        switch($sort) {
            case 'name-asc':
                $this->db->order_by('o.name', 'ASC');
                break;
            case 'name-desc':
                $this->db->order_by('o.name', 'DESC');
                break;
            case 'designation-asc':
                $this->db->order_by('c.name', 'ASC');
                break;
            case 'designation-desc':
                $this->db->order_by('c.name', 'DESC');
                break;
            case 'level-asc':
                $this->db->order_by('o.level', 'ASC');
                break;
            case 'level-desc':
                $this->db->order_by('o.level', 'DESC');
                break;
            case 'section-asc':
                $this->db->order_by('o.section_heading', 'ASC');
                break;
            case 'section-desc':
                $this->db->order_by('o.section_heading', 'DESC');
                break;
            case 'position-desc':
                $this->db->order_by('o.position', 'DESC');
                $this->db->order_by('o.id', 'DESC');
                break;
            case 'position-asc':
            default:
                $this->db->order_by('o.position', 'ASC');
                $this->db->order_by('o.designation', 'ASC');
                break;
        }

        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        $this->db->select('count(o.id) as count');
        $this->db->from('office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['level']) && $param['level'] !== '') {
            $this->db->where("o.level", $param['level']);
        }

        if (isset($param['district']) && $param['district'] !== '') {
            $this->db->group_start();
            $this->db->where("o.section_heading", $param['district']);
            $this->db->or_like("o.section_heading", $param['district']);
            $this->db->group_end();
        }

        if (isset($param['is_publish']) && $param['is_publish'] !== '') {
            $this->db->where("o.is_publish", $param['is_publish']);
        }

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where("o.is_publish", 1);
        }

        $this->applyActiveTerm($param);

        if (isset($param['is_former']) && $param['is_former'] !== '') {
            $this->db->where("o.is_former", $param['is_former']);
        }

        if (isset($param['search']) && !empty($param['search'])) {
            $this->db->group_start();
            $this->db->like("o.name", $param['search']);
            $this->db->or_like("o.email", $param['search']);
            $this->db->or_like("o.phone", $param['search']);
            $this->db->or_like("o.section_heading", $param['search']);
            $this->db->or_like("c.name", $param['search']);
            $this->db->group_end();
        }

        if (isset($param['designation']) && !empty($param['designation'])) {
            $desigArr = is_array($param['designation']) ? $param['designation'] : explode(',', $param['designation']);
            $desigArr = array_filter($desigArr);
            if (!empty($desigArr)) {
                $this->db->where_in("o.designation", $desigArr);
            }
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return isset($result['count']) ? $result['count'] : 0;
    }

    /**
     * Restrict a query to the term the Settings screen currently considers
     * active. Rows captured before the year field existed carry no year, so
     * they stay visible instead of blanking the public listings.
     */
    private function applyActiveTerm($param) {
        if (empty($param['active_term'])) {
            return;
        }

        $this->db->group_start();
        $this->db->where("o.year", $param['active_term']);
        $this->db->or_where("o.year IS NULL", NULL, FALSE);
        $this->db->or_where("o.year", '');
        $this->db->group_end();
    }

    public function getById($id) {
        $this->db->select('id, name, phone, email, is_publish, image, designation, position, section_heading, year, is_former, level');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('office_bearer');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);


        if ($formValues['position'] <> 100) {
            $this->updatePosition($formValues);
        }


        $this->db->where(array("id" => $id));
        if ($this->db->update('office_bearer', $formValues)) {
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
        if ($this->db->delete('office_bearer')) {
            return true;
        }
        return false;
    }

    public function isSingleDesignation($designation) {
        $this->db->where(array("is_single" => 1));
        $this->db->where(array("id" => $designation));
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows()) {
            $this->db->where(array("designation" => $designation));
            $query = $this->db->get('office_bearer');
            if ($query->num_rows()) {
                return TRUE;
            }
        }
        return false;
    }

}
