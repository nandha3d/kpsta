<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class AauthGroup_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $this->db->insert('aauth_groups', $data);
        return $this->db->insert_id();
    }

    public function getAll($param) {
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
        $this->db->order_by('id ');
        $query = $this->db->get('aauth_groups');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        $this->db->select('count(id) as count');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("( heading LIKE   '%" . $param['search'] . "%'   OR  content LIKE   '%" . $param['search'] . "%'  ) ");
        }
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("publish" => 1));
        }
        $query = $this->db->get('aauth_groups');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getNews($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('aauth_groups');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $this->db->where(array("id" => $id));
        if ($this->db->update('aauth_groups', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('aauth_groups', array('publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('aauth_groups')) {
            return true;
        }
        return false;
    }

    function getAllGroup($max = false, $min = false, $exclude = array()) {
        if (count($exclude) > 0) {
            $this->db->where_not_in("id", $exclude);
        }
        if ($max) {
            $this->db->where(array("id <" => $max));
        }
        $this->db->where(array("id >" => $this->aauthGroupId));
        $this->db->select('id, description AS name');
        $this->db->order_by('id');
        $query = $this->db->get('aauth_groups');
        return $query->result_array();
    }

}
