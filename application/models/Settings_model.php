<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'settings';
    }

    public function get($key) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return $query->row()->setting_value;
        }
        return false;
    }

    public function update($key, $value) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $this->db->where('setting_key', $key);
            $this->db->update($this->table, array('setting_value' => $value));
        } else {
            $this->db->insert($this->table, array('setting_key' => $key, 'setting_value' => $value));
        }
        return true;
    }

    public function getActiveTerm() {
        $rollover_month = $this->get('rollover_month');
        if (!$rollover_month) $rollover_month = 2; // Default February
        
        $current_month = date('n');
        $current_year = date('Y');
        
        if ($current_month >= $rollover_month) {
            return $current_year . '-' . ($current_year + 1);
        } else {
            return ($current_year - 1) . '-' . $current_year;
        }
    }
}
