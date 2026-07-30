<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ReactionGallery_model extends CI_Model {

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

        if ($data['position'] < 5) {
            $this->updatePosition($data);
        }

        $this->db->insert('reaction_gallery', $data);
        return $this->db->insert_id();
    }

    public function updatePosition($data) {
        $this->db->where(array("position" => $data['position']));
        $query = $this->db->get('reaction_gallery');
        if ($query->num_rows() > 0) {

            $this->db->query('UPDATE reaction_gallery SET position = position + 1 where position >= ' . $data['position'] . ' LIMIT 25');
        }
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.image, o.is_publish, o.position');
        $this->db->from('reaction_gallery o');

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
        $this->db->from('reaction_gallery o');

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
        $this->db->select('id, description, image, is_publish, position');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('reaction_gallery');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $formValues['position'] = $formValues['position'] ? $formValues['position'] : 1000;

        if ($formValues['position'] < 5) {
            $this->updatePosition($formValues);
        }

        $this->db->where(array("id" => $id));
        if ($this->db->update('reaction_gallery', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('reaction_gallery', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('reaction_gallery')) {
            return true;
        }
        return false;
    }

}
