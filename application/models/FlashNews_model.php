<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FlashNews_model extends CI_Model {

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
        $data['position'] = $data['position'] ? $data['position'] : 1000;

        if ($data['position'] > 0 && $data['position'] < 25) {
            $this->updatePosition($data);
        }


        $this->db->insert('flash_news', $data);
        return $this->db->insert_id();
    }

    function updatePosition($data) {
        $this->db->query('UPDATE flash_news SET position = position + 1 where news_type = ' . $data['news_type'] . ' AND position >= ' . $data['position'] . ' LIMIT 25');
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.path, o.is_publish, o.position, o.news_type');
        $this->db->from('flash_news o');
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['news_type'])) {
            $this->db->where(array("o.news_type" => $param['news_type']));
        }

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }


        $this->db->order_by('o.position, o.id desc');
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
        $this->db->from('flash_news o');
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['news_type'])) {
            $this->db->where(array("o.news_type" => $param['news_type']));
        }

        if ($isPublish) {
            $this->db->where(array("o.is_publish" => 1));
        }

        if ($param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getById($id) {
        $this->db->select('id, description, path, is_publish, position');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('flash_news');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $formValues['position'] = $formValues['position'] ? $formValues['position'] : 1000;

        if ($formValues['position'] > 0 && $formValues['position'] < 25) {
            $this->updatePosition($formValues);
        }

        $this->db->where(array("id" => $id));
        if ($this->db->update('flash_news', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('flash_news', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('flash_news')) {
            return true;
        }
        return false;
    }

    function getSiteVisitorsCount() {
        if ($this->db->query('UPDATE site_visitors SET count = count + 1 limit 1')) {
            $query = $this->db->query('SELECT count FROM  site_visitors  limit 1');

            if ($query->num_rows() > 0) {
                $count = $query->row_array();
                return $count['count'];
            }
        }

        return false;
    }

}
