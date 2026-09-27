<?php

namespace App\Models;
class OrderCircular_model extends Ci3Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        unset($data['pdfName']);
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['date'] = implode('-', array_reverse(explode('-', $data['date'])));
        $data['created_by'] = $this->session->userdata('id');

        $this->db->insert('order_circular', $data);
        return $this->db->insert_id();
    }

    public function getAllCategory() {
        $this->db->select('id, name');
        $this->db->order_by('name');
        $query = $this->db->get('order_circular_category');
        if ($query->num_rows() > 0) {
//            $data[''] = '- - - SELECT CATEGORY - - -';
            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    public function getOrderCircularCategory($param) {

        $query = $this->db->query("SELECT  name, id FROM order_circular_category WHERE id IN ( SELECT DISTINCT category FROM order_circular WHERE type = " . $param['type'] . " AND is_publish = 1 AND is_delete <> 1  ) ORDER BY name ");

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllByType($param, $isPublish = FALSE) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('o.id, o.description, o.upload_type, DATE_FORMAT(o.date,"%d-%m-%Y") as date, o.date as date_unformat, o.category as raw_category, c.name as category, o.path, o.is_publish');
        $this->db->from('order_circular o');
        $this->db->join('order_circular_category c', 'o.category = c.id', 'left');
        if (isset($param['type']) && $param['type']) {
            $this->db->where(array("o.type" => $param['type']));
        }
        if ($isPublish) {
            $this->db->where(array("o.is_publish" => 1));
        }
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['category']) && !empty($param['category'])) {
            $cat = $this->db->escape_str($param['category']);
            $this->db->where("(o.category = '{$cat}' OR FIND_IN_SET('{$cat}', o.category) > 0)");
        }
        if (isset($param['year']) && !empty($param['year'])) {
            $this->db->where("YEAR(o.date)", (int)$param['year']);
        }
        if (isset($param['month']) && !empty($param['month'])) {
            $this->db->where("MONTH(o.date)", (int)$param['month']);
        }

        $this->db->order_by('o.date desc, o.id desc');
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $orders = $query->result_array();
            $catMap = $this->getAllCategory(); // [id => name]
            $nameToId = array();
            foreach ($catMap as $cid => $cname) {
                $nameToId[strtolower(trim($cname))] = $cid;
            }

            foreach ($orders as &$order) {
                $order['categories_list'] = array();
                
                // 1. Try by raw_category (e.g. "103" or comma separated "103,105")
                if (!empty($order['raw_category'])) {
                    $catIds = explode(',', (string)$order['raw_category']);
                    foreach ($catIds as $cid) {
                        $cid = trim($cid);
                        if (!empty($cid) && isset($catMap[$cid])) {
                            $order['categories_list'][] = array(
                                'id' => $cid,
                                'name' => $catMap[$cid]
                            );
                        }
                    }
                }
                
                // 2. Fallback by name lookup if categories_list is still empty
                if (empty($order['categories_list']) && !empty($order['category'])) {
                    $cleanName = strtolower(trim($order['category']));
                    if (isset($nameToId[$cleanName])) {
                        $order['categories_list'][] = array(
                            'id' => $nameToId[$cleanName],
                            'name' => $order['category']
                        );
                    }
                }
            }
            return $orders;
        }
        return array();
    }

    public function getAllByTypeCount($param, $isPublish = FALSE) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(o.id) as count');
        $this->db->from('order_circular o');
        $this->db->join('order_circular_category c', 'o.category = c.id', 'left');
        if (isset($param['type']) && $param['type']) {
            $this->db->where(array("o.type" => $param['type']));
        }
        if ($isPublish) {
            $this->db->where(array("o.is_publish" => 1));
        }
        $this->db->where("o.is_delete  != ", '1');
        if (isset($param['search']) && $param['search']) {
            $this->db->where("o.description LIKE ", '%' . $param['search'] . '%');
        }
        if (isset($param['category']) && !empty($param['category'])) {
            $cat = $this->db->escape_str($param['category']);
            $this->db->where("(o.category = '{$cat}' OR FIND_IN_SET('{$cat}', o.category) > 0)");
        }
        if (isset($param['year']) && !empty($param['year'])) {
            $this->db->where("YEAR(o.date)", (int)$param['year']);
        }
        if (isset($param['month']) && !empty($param['month'])) {
            $this->db->where("MONTH(o.date)", (int)$param['month']);
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getAvailableYears($type = null) {
        $this->db->select('DISTINCT(YEAR(date)) as year');
        $this->db->where('is_delete !=', 1);
        $this->db->where('date IS NOT NULL');
        if ($type) {
            $this->db->where('type', $type);
        }
        $this->db->order_by('year', 'DESC');
        $query = $this->db->get('order_circular');
        $years = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($row->year > 1970) {
                    $years[] = (int)$row->year;
                }
            }
        }
        return $years;
    }

    public function getOrAddCategory($name) {
        $clean = trim((string)$name);
        if (is_numeric($clean)) {
            return (int)$clean;
        }
        $this->db->where('LOWER(name)', strtolower($clean));
        $query = $this->db->get('order_circular_category');
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return $this->categoryAdd(['name' => $clean]);
    }

    public function getById($id) {
        $this->db->select('id, description, upload_type, DATE_FORMAT(date,"%d-%m-%Y") as date, category, path, is_publish');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('order_circular');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        unset($formValues['pdfName']);
        $formValues['date'] = implode('-', array_reverse(explode('-', $formValues['date'])));

        $this->db->where(array("id" => $id));
        if ($this->db->update('order_circular', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('order_circular', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('order_circular', array('is_delete' => 1))) {
            return true;
        }
        return false;
    }

    public function categoryAdd($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = (int)($this->session->userdata('id') ?: 0);

        $this->db->insert('order_circular_category', $data);
        return $this->db->insert_id();
    }

    public function getCategoryById($id) {
        $this->db->select('id, name');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('order_circular_category');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function categoryUpdate($id, $formValues) {

        $this->db->where(array("id" => $id));
        if ($this->db->update('order_circular_category', $formValues)) {
            return true;
        }
        return false;
    }

}
