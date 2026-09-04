<?php

namespace App\Models\Membership;

use App\Models\MembershipModel;
class Config_model extends MembershipModel {

    function __construct() {
        parent::__construct();
    }

    public function getAll() {
        $this->db->select('*');
        $query = $this->db->get('config');
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                $data[$row['label']] = $row['value'];
            }
            return $data;
        }
        return false;
    }

    public function update($formValues) {
        $sql = " UPDATE config SET `value` = CASE ";
        foreach ($formValues as $key => $val) {
            $sql .= " WHEN label = '" . $key . "' THEN " . $val;
        }
        $sql .= " END";

         if ($this->db->query($sql)) {
            return true;
        }
        return false;
    }

    public function getLabelValue($label) {
        $this->db->select('value');
        $this->db->where(array("label" => $label));
        $query = $this->db->get('config');

        if ($query->num_rows() > 0) {
            $result = $query->row(0, 'array');
            return $result['value'];
        }
        return false;
    }

}
