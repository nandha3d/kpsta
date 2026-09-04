<?php

namespace App\Models;
class ResultLink_model extends Ci3Model {

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
            $this->db->query('UPDATE result_link SET position = position + 1 where position >= ' . $data['position'] . ' LIMIT 25');
        }

        $this->db->insert('result_link', $data);
        return $this->db->insert_id();
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.path, o.is_publish, o.position');
        $this->db->from('result_link o');
        $this->db->where("o.is_delete  != ", '1');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where(array("o.is_publish" => 1));
        }
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['category']) && $param['category']) {
            $this->db->where_in("o.category", explode(',', $param['category']));
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
        $this->db->from('result_link o');
        $this->db->where("o.is_delete  != ", '1');

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
        $query = $this->db->get('result_link');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $formValues['position'] = $formValues['position'] ? $formValues['position'] : 1000;

        if ($formValues['position'] > 0 && $formValues['position'] < 25) {
            $this->db->query('UPDATE result_link SET position = position + 1 where position >= ' . $formValues['position'] . ' LIMIT 25');
        }

        $this->db->where(array("id" => $id));
        if ($this->db->update('result_link', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('result_link', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('result_link')) {
            return true;
        }
        return false;
    }

}
