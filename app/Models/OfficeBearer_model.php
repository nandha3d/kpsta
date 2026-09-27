<?php

namespace App\Models;
class OfficeBearer_model extends Ci3Model {

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

        if (isset($data['previous_positions']) && is_array($data['previous_positions'])) {
            $data['previous_positions'] = json_encode(array_values($data['previous_positions']));
        }

        if ($data['position'] <> 100) {
            $this->updatePosition($data);
        }

        $this->db->insert('office_bearer', $data);
        return $this->db->insert_id();
    }

    public function updatePosition($data) {
        $this->db->where(array("position" => $data['position']));
        $query = $this->db->get('office_bearer');
        if ($query->num_rows() > 0) {

            $this->db->query('UPDATE office_bearer SET position = position + 1 where position >= ' . $data['position'] . ' AND designation = ' . $data['designation']);
        }
    }

    public function getAllDesignation($param = array()) {
        $this->db->select('id, name');

        if (isset($param['limit']) && $param['limit'] == 3) {
            $this->db->where('id IN ( 1, 2, 3 )');
        }
        $this->db->order_by('id');
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return array();
    }

    public function getAllSectionHeadings() {
        // Predefined headings that should always be available
        $defaults = array(
            'President / General Secretary / Treasurer' => 'President / General Secretary / Treasurer',
            'Senior Vice President & Associate General Secretary' => 'Senior Vice President & Associate General Secretary',
            'Vice President' => 'Vice President',
            'Secretary' => 'Secretary',
            'Secretariate Members' => 'Secretariate Members',
        );

        $this->db->select('DISTINCT(section_heading) as heading');
        $this->db->where('section_heading IS NOT NULL');
        $this->db->where('section_heading !=', '');
        $this->db->order_by('section_heading', 'ASC');
        $query = $this->db->get('office_bearer');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[$row->heading] = $row->heading;
            }
        }
        // Merge: defaults first, then any DB-only headings appended
        return array_merge($defaults, $data);
    }

    public function getOrAddDesignation($designation) {
        if (is_numeric($designation)) {
            $this->db->where('id', $designation);
            $query = $this->db->get('office_bearer_designation');
            if ($query->num_rows() > 0) {
                return $designation;
            }
        }
        
        $this->db->where('name', $designation);
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->id;
        }

        $this->db->insert('office_bearer_designation', ['name' => $designation]);
        return $this->db->insert_id();
    }

    public function getDesignationById($id) {
        $this->db->select('id, name');
        $this->db->where('id', $id);
        $query = $this->db->get('office_bearer_designation');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    /**
     * Designations are shared across every office bearer, so the name has to
     * stay unique - two rows with the same name would split one section into
     * two on the public listing.
     */
    public function designationExists($name, $excludeId = false) {
        $this->db->where('name', $name);
        if ($excludeId) {
            $this->db->where('id !=', $excludeId);
        }
        $query = $this->db->get('office_bearer_designation');
        return $query->num_rows() > 0;
    }

    public function addDesignation($name) {
        $this->db->insert('office_bearer_designation', array('name' => $name));
        return $this->db->insert_id();
    }

    public function updateDesignation($id, $name) {
        $this->db->where('id', $id);
        return (bool) $this->db->update('office_bearer_designation', array('name' => $name));
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 15;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;

        $this->db->select('o.id, o.name, o.phone, o.email, o.is_publish, o.image, c.name as designation, o.designation as designationId, o.section_heading, o.year, o.is_former, o.level, o.position, o.previous_positions');
        $this->db->from('office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['level']) && $param['level'] !== '') {
            $this->db->where("o.level", $param['level']);
        }

        if (isset($param['district']) && $param['district'] !== '') {
            $this->db->group_start();
            $this->db->where("o.section_heading", $param['district']);
            $this->db->or_like("o.section_heading", $param['district']);
            $this->db->group_end();
        }

        if (isset($param['is_publish']) && $param['is_publish'] !== '') {
            $this->db->where("o.is_publish", $param['is_publish']);
        }

        // Public pages ask for published rows only, via isPublish.
        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where("o.is_publish", 1);
        }

        $this->applyActiveTerm($param);

        if (isset($param['is_former']) && $param['is_former'] !== '') {
            $this->db->where("o.is_former", $param['is_former']);
        }

        if (isset($param['search']) && !empty($param['search'])) {
            $this->db->group_start();
            $this->db->like("o.name", $param['search']);
            $this->db->or_like("o.email", $param['search']);
            $this->db->or_like("o.phone", $param['search']);
            $this->db->or_like("o.section_heading", $param['search']);
            $this->db->or_like("c.name", $param['search']);
            $this->db->group_end();
        }

        if (isset($param['designation']) && !empty($param['designation'])) {
            $desigArr = is_array($param['designation']) ? $param['designation'] : explode(',', $param['designation']);
            $desigArr = array_filter($desigArr);
            if (!empty($desigArr)) {
                $this->db->where_in("o.designation", $desigArr);
            }
        }

        // Sorting options
        $sort = isset($param['sort']) ? $param['sort'] : 'position-asc';
        switch($sort) {
            case 'name-asc':
                $this->db->order_by('o.name', 'ASC');
                break;
            case 'name-desc':
                $this->db->order_by('o.name', 'DESC');
                break;
            case 'designation-asc':
                $this->db->order_by('c.name', 'ASC');
                break;
            case 'designation-desc':
                $this->db->order_by('c.name', 'DESC');
                break;
            case 'level-asc':
                $this->db->order_by('o.level', 'ASC');
                break;
            case 'level-desc':
                $this->db->order_by('o.level', 'DESC');
                break;
            case 'section-asc':
                $this->db->order_by('o.section_heading', 'ASC');
                break;
            case 'section-desc':
                $this->db->order_by('o.section_heading', 'DESC');
                break;
            case 'position-desc':
                $this->db->order_by('o.position', 'DESC');
                $this->db->order_by('o.id', 'DESC');
                break;
            // Primary office bearers read in designation order (President,
            // General Secretary, Treasurer) rather than by position.
            case 'primary':
                $this->db->order_by('o.designation', 'ASC');
                $this->db->order_by('o.position', 'ASC');
                break;
            case 'position-asc':
            default:
                $this->db->order_by('o.position', 'ASC');
                $this->db->order_by('o.designation', 'ASC');
                break;
        }

        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        $this->db->select('count(o.id) as count');
        $this->db->from('office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');

        if (isset($param['level']) && $param['level'] !== '') {
            $this->db->where("o.level", $param['level']);
        }

        if (isset($param['district']) && $param['district'] !== '') {
            $this->db->group_start();
            $this->db->where("o.section_heading", $param['district']);
            $this->db->or_like("o.section_heading", $param['district']);
            $this->db->group_end();
        }

        if (isset($param['is_publish']) && $param['is_publish'] !== '') {
            $this->db->where("o.is_publish", $param['is_publish']);
        }

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where("o.is_publish", 1);
        }

        $this->applyActiveTerm($param);

        if (isset($param['is_former']) && $param['is_former'] !== '') {
            $this->db->where("o.is_former", $param['is_former']);
        }

        if (isset($param['search']) && !empty($param['search'])) {
            $this->db->group_start();
            $this->db->like("o.name", $param['search']);
            $this->db->or_like("o.email", $param['search']);
            $this->db->or_like("o.phone", $param['search']);
            $this->db->or_like("o.section_heading", $param['search']);
            $this->db->or_like("c.name", $param['search']);
            $this->db->group_end();
        }

        if (isset($param['designation']) && !empty($param['designation'])) {
            $desigArr = is_array($param['designation']) ? $param['designation'] : explode(',', $param['designation']);
            $desigArr = array_filter($desigArr);
            if (!empty($desigArr)) {
                $this->db->where_in("o.designation", $desigArr);
            }
        }

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return isset($result['count']) ? $result['count'] : 0;
    }

    /**
     * Restrict a query to the term the Settings screen currently considers
     * active. Rows captured before the year field existed carry no year, so
     * they stay visible instead of blanking the public listings.
     */
    private function applyActiveTerm($param) {
        if (empty($param['active_term'])) {
            return;
        }

        $this->db->group_start();
        $this->db->where("o.year", $param['active_term']);
        $this->db->or_where("o.year IS NULL", NULL, FALSE);
        $this->db->or_where("o.year", '');
        $this->db->group_end();
    }

    public function getById($id) {
        $this->db->select('id, name, phone, email, is_publish, image, designation, position, section_heading, year, is_former, level, previous_positions');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('office_bearer');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);

        if (isset($formValues['previous_positions']) && is_array($formValues['previous_positions'])) {
            $formValues['previous_positions'] = json_encode(array_values($formValues['previous_positions']));
        }

        if ($formValues['position'] <> 100) {
            $this->updatePosition($formValues);
        }

        $this->db->where(array("id" => $id));
        if ($this->db->update('office_bearer', $formValues)) {
            return true;
        }
        return false;
    }

    public function findPersonByNameAndPhone($name, $phone, $excludeId = false) {
        $name = trim((string)$name);
        $phone = trim((string)$phone);
        if ($name === '' || $phone === '') {
            return false;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $this->db->select('o.id, o.name, o.phone, o.email, o.image, o.designation, c.name as designation_name, o.year, o.level, o.section_heading, o.is_former, o.previous_positions');
        $this->db->from('office_bearer o');
        $this->db->join('office_bearer_designation c', 'o.designation = c.id', 'left');
        $this->db->where('LOWER(TRIM(o.name))', mb_strtolower($name));
        $this->db->group_start();
        $this->db->where('o.phone', $phone);
        if (!empty($cleanPhone)) {
            $this->db->or_where("REPLACE(REPLACE(o.phone, ' ', ''), '-', '') =", $cleanPhone);
        }
        $this->db->group_end();
        if ($excludeId) {
            $this->db->where('o.id !=', $excludeId);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function mergePositions($personId, $newPositions = array(), $extra = array()) {
        $person = $this->getById($personId);
        if (!$person) {
            return false;
        }

        $existingList = array();
        if (!empty($person['previous_positions'])) {
            $decoded = json_decode($person['previous_positions'], true);
            if (is_array($decoded)) {
                $existingList = $decoded;
            }
        }

        $makeFingerprint = function($item) {
            $desig = mb_strtolower(trim(isset($item['designation']) ? (string)$item['designation'] : ''));
            $year = mb_strtolower(trim(isset($item['year']) ? (string)$item['year'] : ''));
            $level = mb_strtolower(trim(isset($item['level']) ? (string)$item['level'] : 'State'));
            $sec = mb_strtolower(trim(isset($item['section_heading']) ? (string)$item['section_heading'] : ''));
            return "{$desig}|{$year}|{$level}|{$sec}";
        };

        $seen = array();
        $primaryDesig = '';
        $desigRow = $this->getDesignationById($person['designation']);
        if ($desigRow) {
            $primaryDesig = $desigRow['name'];
        }
        $seen[$makeFingerprint(array(
            'designation' => $primaryDesig,
            'year' => $person['year'],
            'level' => $person['level'],
            'section_heading' => $person['section_heading']
        ))] = true;

        foreach ($existingList as $k => $item) {
            $fp = $makeFingerprint($item);
            $seen[$fp] = $k;
        }

        foreach ($newPositions as $pos) {
            if (empty($pos['designation']) && empty($pos['year'])) {
                continue;
            }
            $fp = $makeFingerprint($pos);
            if (isset($seen[$fp])) {
                $k = $seen[$fp];
                if (isset($pos['is_enabled'])) {
                    $existingList[$k]['is_enabled'] = (int)$pos['is_enabled'];
                }
                if (isset($pos['position']) && is_numeric($pos['position'])) {
                    $existingList[$k]['position'] = (int)$pos['position'];
                }
            } else {
                $seen[$fp] = count($existingList);
                $existingList[] = array(
                    'designation' => trim((string)$pos['designation']),
                    'year' => trim((string)(isset($pos['year']) ? $pos['year'] : '')),
                    'level' => trim((string)(isset($pos['level']) ? $pos['level'] : 'State')),
                    'section_heading' => trim((string)(isset($pos['section_heading']) ? $pos['section_heading'] : '')),
                    'position' => isset($pos['position']) && is_numeric($pos['position']) ? (int)$pos['position'] : 25,
                    'is_enabled' => isset($pos['is_enabled']) ? (int)$pos['is_enabled'] : 1
                );
            }
        }

        $updateData = array(
            'previous_positions' => json_encode(array_values($existingList))
        );

        if (!empty($extra['image']) && empty($person['image'])) {
            $updateData['image'] = $extra['image'];
        }
        if (!empty($extra['email']) && empty($person['email'])) {
            $updateData['email'] = $extra['email'];
        }
        if (isset($extra['is_former']) && $extra['is_former'] == 1) {
            $updateData['is_former'] = 1;
        }

        $this->db->where('id', $personId);
        return $this->db->update('office_bearer', $updateData);
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('office_bearer', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->where(array("id" => $id));
        if ($this->db->delete('office_bearer')) {
            return true;
        }
        return false;
    }

    public function isSingleDesignation($designation, $isFormer = 0) {
        if (!empty($isFormer)) {
            return false;
        }
        $this->db->where(array("is_single" => 1));
        $this->db->where(array("id" => $designation));
        $query = $this->db->get('office_bearer_designation');
        if ($query->num_rows()) {
            $this->db->where(array("designation" => $designation, "is_former" => 0));
            $query = $this->db->get('office_bearer');
            if ($query->num_rows()) {
                return TRUE;
            }
        }
        return false;
    }

}
