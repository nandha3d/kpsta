<?php

namespace App\Models;

class EditorialBoard_model extends Ci3Model {

    function __construct() {
        parent::__construct();
    }

    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $this->session->userdata('id') ?: 0;
        $data['position'] = (isset($data['position']) && $data['position'] !== '') ? (int)$data['position'] : 1000;

        if ($data['position'] > 0 && $data['position'] < 25) {
            $this->updatePosition($data['position']);
        }

        $this->db->insert('editorial_board', $data);
        return $this->db->insert_id();
    }

    public function updatePosition($pos, $excludeId = 0) {
        $exSql = $excludeId ? " AND id != " . (int)$excludeId : "";
        $this->db->query("UPDATE editorial_board SET position = position + 1 WHERE position >= " . (int)$pos . " AND position <> 1000 {$exSql} LIMIT 25");
    }

    public function getAll($param = []) {
        $limit = isset($param['limit']) ? (int)$param['limit'] : 10;
        $offset = isset($param['offset']) ? (int)$param['offset'] : 0;

        $this->db->select('id, name, designation, phone, email, image, position, is_publish, created_at');
        $this->db->from('editorial_board');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where('is_publish', 1);
        }
        if (!empty($param['search'])) {
            $s = $param['search'];
            $this->db->where("(name LIKE '%" . $this->db->escape_like_str($s) . "%' OR designation LIKE '%" . $this->db->escape_like_str($s) . "%')");
        }
        if (!empty($param['designation'])) {
            $this->db->where('designation', $param['designation']);
        }

        // Sorting
        $sort = isset($param['sort']) ? $param['sort'] : 'position-asc';
        switch ($sort) {
            case 'name-asc':
                $this->db->order_by('name ASC');
                break;
            case 'name-desc':
                $this->db->order_by('name DESC');
                break;
            case 'designation-asc':
                $this->db->order_by('designation ASC, position ASC');
                break;
            case 'position-desc':
                $this->db->order_by('position DESC, id DESC');
                break;
            case 'position-asc':
            default:
                $this->db->order_by('position ASC, id ASC');
                break;
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return [];
    }

    public function getAllCount($param = []) {
        $this->db->select('count(id) as count');
        $this->db->from('editorial_board');

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where('is_publish', 1);
        }
        if (!empty($param['search'])) {
            $s = $param['search'];
            $this->db->where("(name LIKE '%" . $this->db->escape_like_str($s) . "%' OR designation LIKE '%" . $this->db->escape_like_str($s) . "%')");
        }
        if (!empty($param['designation'])) {
            $this->db->where('designation', $param['designation']);
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');
        return isset($result['count']) ? (int)$result['count'] : 0;
    }

    public function getById($id) {
        $this->db->select('*');
        $this->db->where('id', (int)$id);
        $query = $this->db->get('editorial_board');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        if (isset($formValues['position']) && (int)$formValues['position'] > 0 && (int)$formValues['position'] < 25) {
            $this->updatePosition((int)$formValues['position'], (int)$id);
        }
        $this->db->where('id', (int)$id);
        return $this->db->update('editorial_board', $formValues);
    }

    public function publish($id, $publish) {
        $this->db->where('id', (int)$id);
        return $this->db->update('editorial_board', ['is_publish' => (int)$publish]);
    }

    public function delete($id) {
        $this->db->where('id', (int)$id);
        return $this->db->delete('editorial_board');
    }

    public function batchDelete($ids) {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }
        $this->db->where_in('id', array_map('intval', $ids));
        return $this->db->delete('editorial_board');
    }

    public function getDistinctDesignations() {
        $this->db->select('designation, MIN(position) as min_pos');
        $this->db->from('editorial_board');
        $this->db->group_by('designation');
        $this->db->order_by('min_pos ASC, designation ASC');
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return array_column($query->result_array(), 'designation');
        }
        return [];
    }
}
