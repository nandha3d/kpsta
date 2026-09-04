<?php

namespace App\Models;
class Home_model extends Ci3Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function updateNews($data) {
        $data['updated_by'] = $this->session->userdata('id');
        $this->db->where(array("news_id" => $data['news_id']));
        $query = $this->db->get('flash_news');
        if ($query->num_rows()) {
            $this->db->where(array("news_id" => $data['news_id']));
            unset($data['news_id']);
            $this->db->update('flash_news', $data);
        } else {
            $this->db->insert('flash_news', $data);
        }

        return true;
    }

    public function getAll() {
        $this->db->select('news_id,news ');
        $this->db->from('flash_news ');
        $this->db->where_in("news_id", [1, 2]);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            foreach ($result as $value) {
                $data[$value['news_id']] = $value['news'];
            }
            return $data;
        }
        return array();
    }

}
