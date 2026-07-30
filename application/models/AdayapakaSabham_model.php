<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AdayapakaSabham_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    ############################################################################
    /**
     * 
     * @param type $data
     * @return type
     */

    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $this->db->insert('adayapaka_sabham', $data);
        return $this->db->insert_id();
    }

    public function getAllCount($param) {
        $this->db->select('count(id) as count');
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("is_publish" => 1));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("description LIKE ", '%' . $param['search'] . '%');
        }
        $query = $this->db->get('adayapaka_sabham');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->from('adayapaka_sabham');
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("is_publish" => 1));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("description LIKE ", '%' . $param['search'] . '%');
        }
        $this->db->order_by('id desc, created_at desc');
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getById($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('adayapaka_sabham');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        unset($formValues['file']);

        $this->db->where(array("id" => $id));
        if ($this->db->update('adayapaka_sabham', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('adayapaka_sabham', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('adayapaka_sabham')) {
            return true;
        }
        return false;
    }

}
