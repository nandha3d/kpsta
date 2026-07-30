<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class WhatsNew_model extends Membership_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function addNews($data) {
        $this->db->insert('whats_new', $data);
        return $this->db->insert_id();
    }

    public function getAllNews($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;

        $this->db->select('w.*,  CASE WHEN w.group_id IS NULL THEN "ALL" ELSE aq.name END AS `group` ', FALSE);
        if (isset($param['search']) && $param['search']) {
            $this->db->where("( w.heading LIKE   '%" . $param['search'] . "%'   OR  w.content LIKE   '%" . $param['search'] . "%'  ) ");
        }
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("publish" => 1));
        }
        $this->db->join('aauth_groups aq', 'w.group_id = aq.id', 'left');
        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            $this->db->where("( w.group_id IS NULL OR w.group_id = 0 OR w.group_id = " . $this->aauthGroupId . "  ) ");
        }
        $this->db->limit($param['limit'], $param['offset']);
        $this->db->order_by('w.position, w.id desc');
        $query = $this->db->get('whats_new w');
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
        if ($this->aauthGroupId != static::AAUTH_GROUP_STATE) {
            $this->db->where("( group_id IS NULL OR group_id = 0 OR group_id = " . $this->aauthGroupId . "  ) ");
        }
        $query = $this->db->get('whats_new');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getNews($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('whats_new');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $this->db->where(array("id" => $id));
        if ($this->db->update('whats_new', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('whats_new', array('publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('whats_new')) {
            return true;
        }
        return false;
    }

    public function deleteFile($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('whats_new', array('file_name' => NULL))) {
            return true;
        }
        return false;
    }

}
