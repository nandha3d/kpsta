<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class WebService_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function registerToken($data) {

        $this->db->insert('user_device', $data);
        return $this->db->insert_id();
    }

}
