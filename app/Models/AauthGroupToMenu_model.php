<?php

namespace App\Models;
class AauthGroupToMenu_model extends Ci3Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $this->db->where(array("group_id" => $data['group']));
        if (!$this->db->delete('aauth_group_to_menu')) {
            return false;
        }

        $menu = array();
        foreach ($data['menu'] as $bit) {
            $array = array(
                'group_id' => $data['group'],
                'menu_id' => $bit
            );
            $menu[] = $array;
        }


        if (count($menu)) {
            $this->db->insert_batch('aauth_group_to_menu', $menu);
        }

        return true;
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
        $this->db->order_by('id desc');
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

    public function getAllGroup() {
        $this->db->select('*');
        $this->db->where("id != 1");
        $this->db->order_by('id');
        $query = $this->db->get('aauth_groups');
        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    function getMenuList() {
        $this->db->select('*');
        $this->db->where("( ( parent_id IS NULL OR parent_id = ''  ) OR  is_tree = 1  )");
        $this->db->order_by('id');
        $query = $this->db->get('aauth_menus');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function getMenuIdGroup($groupId) {
        $this->db->select('menu_id');
        $this->db->where(array("group_id" => $groupId));
        $this->db->order_by('menu_id');
        $query = $this->db->get('aauth_group_to_menu');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[$row['menu_id']] = $row['menu_id'];
            }
            return $data;
        }
        return array();
    }

}
