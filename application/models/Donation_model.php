<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Donation_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['ip_address'] = $this->input->ip_address();
        $data['status'] = 'pending';
  
        // $this->db->set('uuid', 'UUID()', FALSE);
        // $this->uuid_key();
        // $this->db->set('uuid', $data['uuid'], FALSE);

        // unset($data['uuid']);

        $this->db->insert('donation', $data);
        return $this->db->insert_id();
    }


    public function getActiveByUuid($uuid){
        // $this->db->select('*');
        $this->db->from('donation o');
        $this->db->where(array("o.uuid" => $uuid));
        $this->db->where("o.status != 'success'");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array()[0];
        }
        return [];
    }

    public function update($id, $data) {
        $data['updated_at'] = date("Y-m-d H:i:s");

        $this->db->where(array("id" => $id));
        if ($this->db->update('donation', $data)) {
            return $id;
        }
        return false;
    }

    public function updateByOrderId($id, $data) {
        $data['updated_at'] = date("Y-m-d H:i:s");

        $this->db->where(array("order_id" => $id));
        if ($this->db->update('donation', $data)) {
            return true;
        }
        return false;
    }

    public function getByOrderId($id){
        // $this->db->select('*');
        $this->db->from('donation o');
        $this->db->where(array("o.order_id" => $id));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array()[0];
        }
        return [];
    }

    public function getDesignation(){
        $db = $this->load->database('membership', TRUE);
        $db->select('d.name, d.id');
        $db->from(' teacher_designation d');
        $db->order_by('d.position, d.id, d.name ASC');
        $query = $db->get();
        return $query->result_array();
    }

    public function getDistrict(){
        $db = $this->load->database('membership', TRUE);
        $db->select('d.name, d.id');
        $db->from(' district d');
        $db->order_by('d.name ASC');
        $query = $db->get();
        return $query->result_array();
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

    

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('flash_news', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

   
}
