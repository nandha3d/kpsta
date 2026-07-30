<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Forms_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        unset($data['pdfName']);
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['date'] = implode('-', array_reverse(explode('-', $data['date'])));
        $data['created_by'] = $this->session->userdata('id');

        $this->db->insert('forms', $data);
        return $this->db->insert_id();
    }

    public function getAllCategory() {
        $this->db->select('id, name');
        $query = $this->db->get('forms_category');
        if ($query->num_rows() > 0) {
//            $data[''] = '- - - SELECT CATEGORY - - -';
            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    public function getFormsCategory() {
        $query = $this->db->query("SELECT  name, id FROM forms_category WHERE id IN ( SELECT DISTINCT category FROM forms WHERE is_publish = 1 AND is_delete <> 1  ) ");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.upload_type, DATE_FORMAT(o.date,"%d-%m-%Y") as date, o.date as date_unformat, c.name as category, o.path, o.is_publish');
        $this->db->from('forms o');
        $this->db->join('forms_category c', 'o.category = c.id', 'left');

        if (isset($param['isPublish'] )  && $param['isPublish'] ) {
            $this->db->where(array("o.is_publish" => 1));
        }
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['category']) && $param['category']) {
            $this->db->where_in("o.category", explode(',', $param['category']));
        }

        $this->db->order_by('c.name , o.id desc');
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
         $this->db->from('forms o');
        $this->db->join('forms_category c', 'o.category = c.id', 'left');

        if (isset($param['isPublish'] )  && $param['isPublish'] ) {
            $this->db->where(array("o.is_publish" => 1));
        }
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['category']) && $param['category']) {
            $this->db->where_in("o.category", explode(',', $param['category']));
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, description, upload_type, DATE_FORMAT(date,"%d-%m-%Y") as date, category, path, is_publish');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('forms');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        unset($formValues['pdfName']);
        $formValues['date'] = implode('-', array_reverse(explode('-', $formValues['date'])));

        $this->db->where(array("id" => $id));
        if ($this->db->update('forms', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('forms', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('forms', array('is_delete' => 1))) {
            return true;
        }
        return false;
    }

    public function categoryAdd($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $this->session->userdata('id');

        $this->db->insert('forms_category', $data);
        return $this->db->insert_id();
    }

    public function getCategoryById($id) {
        $this->db->select('id, name');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('forms_category');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function categoryUpdate($id, $formValues) {

        $this->db->where(array("id" => $id));
        if ($this->db->update('forms_category', $formValues)) {
            return true;
        }
        return false;
    }

}
