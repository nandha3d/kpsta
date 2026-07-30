<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceCorner_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getAll($params = array()) {
        $this->db->select('*');
        $this->db->from('service_corner');
        
        if (array_key_exists("id", $params)) {
            $this->db->where('id', $params['id']);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (array_key_exists("status", $params)) {
                $this->db->where('status', $params['status']);
            }
            if (array_key_exists("returnType", $params) && $params['returnType'] == 'count') {
                $result = $this->db->count_all_results();
            } else {
                if (array_key_exists("id", $params)) {
                    $this->db->order_by('id', 'asc');
                } else {
                    $this->db->order_by('id', 'asc');
                }
                
                if (array_key_exists("start", $params) && array_key_exists("limit", $params)) {
                    $this->db->limit($params['limit'], $params['start']);
                } elseif (!array_key_exists("start", $params) && array_key_exists("limit", $params)) {
                    $this->db->limit($params['limit']);
                }
                $query = $this->db->get();
                $result = ($query->num_rows() > 0) ? $query->result_array() : FALSE;
            }
        }
        return $result;
    }

    public function insert($data = array()) {
        if (!array_key_exists("created_at", $data)) {
            $data['created_at'] = date("Y-m-d H:i:s");
        }
        $insert = $this->db->insert('service_corner', $data);
        if ($insert) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function update($data, $id) {
        if (!empty($data) && !empty($id)) {
            $update = $this->db->update('service_corner', $data, array('id' => $id));
            return $update ? true : false;
        }
        return false;
    }

    public function delete($id) {
        $delete = $this->db->delete('service_corner', array('id' => $id));
        $this->db->delete('service_rules', array('service_id' => $id));
        return $delete ? true : false;
    }

    // Rules Management
    public function getRulesByServiceId($service_id) {
        $this->db->select('*');
        $this->db->from('service_rules');
        $this->db->where('service_id', $service_id);
        $this->db->order_by('position', 'asc');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    public function getRuleById($rule_id) {
        $this->db->select('*');
        $this->db->from('service_rules');
        $this->db->where('id', $rule_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insertRule($data = array()) {
        if (!array_key_exists("created_at", $data)) {
            $data['created_at'] = date("Y-m-d H:i:s");
        }
        $insert = $this->db->insert('service_rules', $data);
        return $insert ? $this->db->insert_id() : false;
    }

    public function updateRule($data, $rule_id) {
        if (!empty($data) && !empty($rule_id)) {
            $update = $this->db->update('service_rules', $data, array('id' => $rule_id));
            return $update ? true : false;
        }
        return false;
    }

    public function deleteRule($rule_id) {
        $delete = $this->db->delete('service_rules', array('id' => $rule_id));
        return $delete ? true : false;
    }

}
