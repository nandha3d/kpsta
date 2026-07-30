<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class News_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function addNews($data) {
        $this->db->insert('news', $data);
        return $this->db->insert_id();
    }

    public function getAllNews($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;

        $this->db->select('*');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("( heading LIKE   '%" . $param['search'] . "%'   OR  content LIKE   '%" . $param['search'] . "%'  ) ");
        }
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("publish" => 1));
        }
        $this->db->limit($param['limit'], $param['offset']);
        $this->db->order_by('id desc');
        $query = $this->db->get('news');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllNewsCount($param) {
        $this->db->select('count(id) as count');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("( heading LIKE   '%" . $param['search'] . "%'   OR  content LIKE   '%" . $param['search'] . "%'  ) ");
        }
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("publish" => 1));
        }
        $query = $this->db->get('news');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getNews($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('news');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $this->db->where(array("id" => $id));
        if ($this->db->update('news', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('news', array('publish' => $publish))) {
            return true;
        }
        return false;
    }

    /**
     * Get special circulars for admin user
     * @return boolean
     */
    public function getSpecialCirculars() {
        $this->db->select("*");
        $this->db->from("`sc_admin` `s`");
        $this->db->join("`gs_admin` `g`", "g.MenuId = s.MenuId", "left");
        $this->db->where(array("g.Active" => "N", "s.SchoolCode" => $this->session->userdata("school_code")));
        $resource = $this->db->get();
        if ($resource->num_rows > 0) {
            return $resource->result();
        } else {
            return FALSE;
        }
    }

    /**
     * Get common links for admin user
     * @return boolean
     */
    public function getLinks() {
        $this->db->where(array($this->session->userdata("school_type") => "Y", "`Active`" => "Y"));
        $this->db->order_by('OrderNo', 'ASC');
        $resource = $this->db->get("`g_admin`");
        if ($resource->num_rows > 0) {
            return $resource->result();
        } else {
            return FALSE;
        }
    }

    /**
     * Get special links for admin user
     * @return boolean
     */
    public function getSpecialLinks() {
        $this->db->select("*");
        $this->db->from("`s_admin` `s`");
        $this->db->join("`g_admin` `g`", "g.MenuId = s.MenuId", "left");
        $this->db->where(array("g.Active" => "N", "s.SchoolCode" => $this->session->userdata("school_code")));
        $resource = $this->db->get();
        if ($resource->num_rows > 0) {
            return $resource->result();
        } else {
            return FALSE;
        }
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('news')) {
            return true;
        }
        return false;
    }

}
