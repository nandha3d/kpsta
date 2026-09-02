<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Download_model extends CI_Model {

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

        $this->db->insert('download', $data);
        return $this->db->insert_id();
    }

    public function getAllCategory() {
        $this->db->select('id, name');
        $query = $this->db->get('download_category');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    public function getOrAddCategory($category) {
        if (empty($category)) {
            return NULL;
        }
        if (is_numeric($category)) {
            $this->db->where('id', $category);
            $query = $this->db->get('download_category');
            if ($query->num_rows() > 0) {
                return $category;
            }
        }
        
        $this->db->where('name', $category);
        $query = $this->db->get('download_category');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->id;
        }

        $this->db->insert('download_category', [
            'name' => $category,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => $this->session->userdata('id')
        ]);
        return $this->db->insert_id();
    }

    public function getFormsCategory($param) {
        $pageType = isset($param['type']) ? $param['type'] : 0;
        // Without ORDER BY, MySQL returned the tabs in whatever order the join
        // happened to produce, so the strip could reshuffle between requests.
        $query = $this->db->query("SELECT  name, id FROM download_category WHERE id IN ( SELECT DISTINCT category FROM download WHERE is_publish = 1 AND type= '" . $pageType . "'  ) ORDER BY name ASC ");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAll($param, $isPublish = FALSE) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.upload_type, DATE_FORMAT(o.date,"%d-%m-%Y") as date, o.date as date_unformat, c.name as category, c.id as category_id, o.path, o.is_publish');
        $this->db->from('download o');
        $this->db->join('download_category c', 'o.category = c.id', 'left');
        if (isset($param['type']) && $param['type']) {
            $this->db->where(array("o.type" => $param['type']));
        }
        if ($isPublish) {
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

    public function getAllCount($param, $isPublish = FALSE) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(o.id) as count');
        $this->db->from('download o');
        $this->db->join('download_category c', 'o.category = c.id', 'left');
        if (isset($param['type']) && $param['type']) {
            $this->db->where(array("o.type" => $param['type']));
        }
        if (isset($param['type']) && $param['type']) {
            $this->db->where(array("o.type" => $param['type']));
        }
        if ($isPublish) {
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
        $this->db->select('id, description, upload_type, category, path, is_publish');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('download');

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
        if ($this->db->update('download', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('download', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('download')) {
            return true;
        }
        return false;
    }

    public function categoryAdd($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $this->session->userdata('id');

        $this->db->insert('download_category', $data);
        return $this->db->insert_id();
    }

    public function getCategoryById($id) {
        $this->db->select('id, name');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('download_category');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function categoryUpdate($id, $formValues) {

        $this->db->where(array("id" => $id));
        if ($this->db->update('download_category', $formValues)) {
            return true;
        }
        return false;
    }

}
