<?php

namespace App\Models;
class WebService_model extends Ci3Model {

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
