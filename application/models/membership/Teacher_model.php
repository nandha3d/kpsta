<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Teacher_model extends Membership_Model {

    function __construct() {
        parent::__construct();
        $this->getYear();
    }

    /**
     * Get common circulars for admin user
     * @return boolean
     */
    public function add($data) {
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = $data['updated_by'] = $this->session->userdata('id');
        unset($data['school']);
        unset($data['designation']);

        $this->db->insert('teacher_details', $data);
        return $this->db->insert_id();
    }

    public function insertBatch($data) {
        $newData = [];
        foreach($data as $row){
            unset($row['school_id']);
            unset($row['designation_id']);
            $newData[] = $row;
        }
        $this->db->insert_batch('teacher_details', $newData);
        return $this->db->insert_id();
    }

    public function getAll($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('t.id, tr.school_id, t.name, tr.designation_id, t.mobile, s.name AS school, td.name AS designation, b.name AS branch, t.adhyapaka_sabdham_subscriber, t.teacher_type AS teacherType, tr.id AS processId, '
                . ' tr.approved_by AS approvedId,  aua.name AS approvedBy, DATE_FORMAT(tr.approved_date, "%d-%m-%Y") AS  approvedDate ,'
                . ' tr.is_confirmed, tr.confirmed_by AS confirmedId,  auc.name AS confirmedBy, DATE_FORMAT(tr.confirmed_date, "%d-%m-%Y") AS  confirmedDate ,'
                . ' sd.id AS subDistId,  sd.name AS subDist, ed.id AS eduDistId, ed.name AS eduDist, '
                . ' d.id AS distId, d.name AS dist, tr.year AS year ');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');
        $this->db->join('teacher_designation td', 'tr.designation_id = td.id', 'left');
//        $this->db->join('teacher_process tr', 't.id = tr.teacher_id AND (tr.year IS NULL OR tr.year = ' . $this->year . ')', 'left');
        $this->db->join('aauth_users auc', 'tr.approved_by = auc.id', 'left');
        $this->db->join('aauth_users aua', 'tr.confirmed_by = aua.id', 'left');

        $this->loadCondition($param);

        $this->db->order_by('sd.name, sd.id, b.name, b.id, s.name, s.id, t.id desc, t.id desc, t.name');
        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        $query = $this->db->get();
//       $this->db->close();
// echo $this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }
    
    /**
     * Sub dist --- confim 
     * //Edu Dst --- checked
     * Rev dist --- verify
     * State --- approved
     * 
     * @param type $param
     */

    function loadCondition($param = array()) {
        $this->db->join('teacher_deleted tdel', 't.id = tdel.teacher_id', 'left');
        
        $this->db->join('school s', 'tr.school_id = s.id', 'left');
        $this->db->join('branch b', 's.branch_id = b.id', 'left');
        $this->db->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left');
        $this->db->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left');
        $this->db->join('district d', 'ed.district_id = d.id', 'left');

        if ($this->aauthGroupId == static::AAUTH_GROUP_BRANCH) {
            $this->db->where(array("s.branch_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->where(array("b.sub_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->db->where(array("sd.education_dist_id" => $this->aauthOfficeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->where(array("ed.district_id" => $this->aauthOfficeId));
        }


        if (isset($param['search']) && $param['search']) {
            $this->db->where("t.name LIKE ", '%' . $param['search'] . '%');
        }

//        if (isset($param['groupId']) && $param['groupId']) {
//            $this->db->where(array("au.group_id" => $param['groupId']));
//        }
        if (isset($param['officeId']) && $param['officeId'] && isset($param['groupId'])) {
            if ($param['groupId'] == static::AAUTH_GROUP_DISTRICT) {
                $this->db->where(array("d.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_EDUCATION_DIST) {
                $this->db->where(array("ed.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_SUB_DIST) {
                $this->db->where(array("sd.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_BRANCH) {
                $this->db->where(array("b.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_SCHOOL) {
                $this->db->where(array("s.id" => $param['officeId']));
            }
        }

        if (isset($param['id']) && $param['id']) {
            if (is_array($param['id'])) {
                //print_r($param['id']);
                $this->db->where_in("t.id", $param['id']);
            } else {
                $this->db->where(array("t.id" => $param['id']));
            }
        }

        //get list member to update for process
        if (isset($param['process']) && $param['process']) {
            if ($param['process'] == "confirm") {
                if ($param['action'] != "reject") {
                    $this->db->where('(tr.is_confirmed IS NULL OR tr.is_confirmed != 1)');
                } else {
                    $this->db->where('tr.is_confirmed = 1');
                }
            } else if ($param['process'] == "check") {
                if ($param['action'] != "reject") {
                    $this->db->where('(tr.is_confirmed = 1 AND ( tr.is_checked != 1 OR tr.is_checked IS NULL ))');
                } else {
                    $this->db->where('tr.is_checked = 1');
                }
            } else if ($param['process'] == "verify") {
                if ($param['action'] != "reject") {
                    $this->db->where('(tr.is_confirmed = 1 AND ( tr.is_verified != 1 OR tr.is_verified IS NULL ))');
                } else {
                    $this->db->where('tr.is_verified = 1');
                }
            } else if ($param['process'] == "approve") {
                if ($param['action'] != "reject") {
                    $this->db->where('(tr.is_verified = 1 AND ( tr.is_approved != 1 OR tr.is_approved IS NULL) )');
                } else {
                    $this->db->where('tr.is_approved = 1');
                }
            }
        }


        //get List memeber to display 
        if (isset($param['view']) && $param['view']) {
            if ($param['view'] == 1) {
                $this->db->where('(tr.is_confirmed IS NULL OR tr.is_confirmed != 1)');
            } else if ($param['view'] == 2) {
//                $his->db->where('tr.is_confirmed = 1 AND (tr.is_checked != 1 OR tr.is_checked IS NULL ) ');
                $this->db->where('tr.is_confirmed = 1 AND (tr.is_verified != 1 OR tr.is_verified IS NULL ) ');
            } else if ($param['view'] == 6) {
                $this->db->where('tr.is_checked = 1 AND (tr.is_verified != 1 OR tr.is_verified IS NULL ) ');
            } else if ($param['view'] == 3) {
                $this->db->where('tr.is_verified = 1 AND (tr.is_approved != 1 OR tr.is_approved IS NULL )');
            } else if ($param['view'] == 4) {
                $this->db->where('tr.is_approved = 1');
            }
        }

        if (isset($param['year']) && $param['year']) {
            $this->db->where(array("tr.year" => $param['year']));
        }
        
        if(isset($param['view']) && $param['view'] == 10) {
            $this->db->where("tdel.deleted_year =". $param['year']);
        }else{
            $this->db->where("( tdel.deleted_year IS NULL OR tdel.deleted_year !=". $param['year'] .")");
        }
        
    }

    public function getConsolidatedCount($param = array()) {
        $this->db->select('SUM( CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govtMembers , '
                . ' SUM( CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aidedMembers, '
                . ' COUNT(t.id) AS totalCount');

        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');

        $this->loadCondition($param);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return array();
    }

    function getDesignationCount($param = array()) {
        $this->db->select('SUM( CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govtMembers ,SUM( CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aidedMembers, tr.designation_id, COUNT(tr.designation_id) AS designationCount, td.name AS designation');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');
        $this->db->join("teacher_designation td", "td.id  = tr.designation_id ", 'left');

        $this->loadCondition($param);
        $this->db->order_by('td.position, t.id');
        $this->db->group_by('tr.designation_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function getConsolidationDistrict($param = array()) {
        $this->db->select('SUM( CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govtMembers , '
                . ' SUM( CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aidedMembers, '
                . ' COUNT(t.id) AS totalCount, d.name AS name');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');


        $this->loadCondition($param);
        $this->db->order_by('d.name');
        $this->db->group_by('d.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function getConsolidationSubDistrict($param = array()) {
        $this->db->select('SUM( CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govtMembers , '
                . ' SUM( CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aidedMembers, '
                . ' COUNT(t.id) AS totalCount, sd.name AS name, ed.name AS eduDistrict ');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');


        $this->loadCondition($param);
        $this->db->order_by('ed.name, sd.name');
        $this->db->group_by('sd.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function getConsolidationSchool($param = array()) {
        $this->db->select('SUM( CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govtMembers , '
                . ' SUM( CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aidedMembers, '
                . ' COUNT(t.id) AS totalCount, s.name AS name, b.name AS branch ');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');


        $this->loadCondition($param);
        $this->db->order_by('b.name, s.name');
        $this->db->group_by('s.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllCount($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('count(t.id) as count');
        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');

        $this->loadCondition($param);

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getDashboard($param) {
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        if ($param['groupId'] != static::AAUTH_GROUP_SCHOOL) {
//            SUM( CASE WHEN (tr.is_confirmed IS NULL OR tr.is_confirmed != 1) THEN 1 ELSE 0 END) AS entered,
//            SUM( CASE WHEN (tr.is_confirmed = 1 AND (tr.is_checked IS NULL OR tr.is_checked != 1)) THEN 1 ELSE 0 END) AS confirmed,
//            SUM( CASE WHEN (tr.is_checked  = 1 AND (tr.is_verified IS NULL OR tr.is_verified != 1)) THEN 1 ELSE 0 END) AS checked,
//            SUM( CASE WHEN (tr.is_verified = 1 AND (tr.is_approved IS NULL OR tr.is_approved != 1)) THEN 1 ELSE 0 END) AS verified,
            $this->db->select(' 
                    SUM( CASE WHEN (tr.is_confirmed IS NULL OR tr.is_confirmed != 1) THEN 1 ELSE 0 END) AS entered,
                    SUM( CASE WHEN (tr.is_confirmed = 1 AND (tr.is_verified IS NULL OR tr.is_verified != 1)) THEN 1 ELSE 0 END) AS confirmed,
                    SUM( CASE WHEN (tr.is_verified = 1 AND (tr.is_approved IS NULL OR tr.is_approved != 1)) THEN 1 ELSE 0 END) AS verified,
                    SUM( CASE WHEN (tr.is_approved = 1) THEN 1 ELSE 0 END) AS approved
                    ');
        }

        $this->db->from('teacher_details t');
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');

        $this->loadCondition($param);

        if ($param['groupId'] == static::AAUTH_GROUP_SCHOOL) {
            $this->db->select('
                    CASE WHEN (tr.is_confirmed IS NULL OR tr.is_confirmed != 1) THEN 1 ELSE 0 END AS entered,
                    CASE WHEN (tr.is_confirmed = 1 AND (tr.is_checked IS NULL OR tr.is_checked != 1)) THEN 1 ELSE 0 END AS confirmed,
                    CASE WHEN (tr.is_checked  = 1 AND (tr.is_verified IS NULL OR tr.is_verified != 1)) THEN 1 ELSE 0 END AS checked,
                    CASE WHEN (tr.is_verified = 1 AND (tr.is_approved IS NULL OR tr.is_approved != 1)) THEN 1 ELSE 0 END AS verified,
                    CASE WHEN (tr.is_approved = 1) THEN 1 ELSE 0 END AS approved,
                    t.name AS name, t.id AS id, s.name AS officeName');
            //$this->db->group_by('s.id');
            $tableGroupName = 'Members';
            $groupName = 'School';
        } else if ($param['groupId'] == static::AAUTH_GROUP_BRANCH) {
            $this->db->select('s.name AS name, s.id AS id, b.name AS officeName');
            $this->db->group_by('s.id');
            $tableGroupName = 'Schools';
            $groupName = 'Branch';
        } else if ($param['groupId'] == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->select('b.name AS name, b.id AS id, sd.name AS officeName');
            $this->db->group_by('b.id');
            $tableGroupName = 'Branch';
            $groupName = 'Sub District';
        } else if ($param['groupId'] == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->db->select('sd.name AS name, sd.id AS id, ed.name AS officeName');
            $this->db->group_by('sd.id');
            $tableGroupName = 'Sub District';
            $groupName = 'Edu. District';
        } else if ($param['groupId'] == static::AAUTH_GROUP_DISTRICT) {
            $this->db->select('ed.name AS name, ed.id AS id, d.name AS officeName');
            $this->db->group_by('ed.id');
            $tableGroupName = 'Edu. Districts';
            $groupName = 'Rev. Dist';
        } else {
            $this->db->select('d.name AS name, d.id AS id, "Kerala" AS officeName');
            $this->db->group_by('d.id');
            $tableGroupName = 'Districts';
            $groupName = 'State';
        }

        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        $total['tlEntered'] = $total['tlConfirmed'] = $total['tlChecked'] = $total['tlVerified'] = $total['tlApproved'] = 0;

        $officeName = '';
        foreach ($query->result_array() as $row) {
            $officeName = $row['officeName'];
            $total['tlEntered'] += $row['entered'];
            $total['tlConfirmed'] += $row['confirmed'];
//            $total['tlChecked'] += $row['checked'];
            $total['tlVerified'] += $row['verified'];
            $total['tlApproved'] += $row['approved'];
        }
        return [
            'tableGroupName' => $tableGroupName,
            'tableFootUrl' => ($this->aauthGroupId == $param['groupId']) ? false : '&group=' . $param['groupId'] . '&office=' . $param['officeId'],
            'officeName' => $officeName,
            'groupName' => $groupName,
            'group' => $param['groupId'] + 1,
            'data' => $query->result_array(),
            'total' => $total
        ];
    }

    public function getById($param) {
        $this->db->select('t.id, t.name, t.teacher_type, tr.school_id, tr.designation_id, td.name AS designationName, tr.id AS processId, tr.is_confirmed, sd.id AS subDistId,  sd.name AS subDist, ed.id AS eduDistId, ed.name AS eduDist, '
                . ' d.id AS distId, d.name AS dist, s.name AS schoolName, b.id AS branch, b.name AS branchName ');
        $this->db->where(array("t.id" => $param['id']));
        $this->db->join('teacher_process tr', 't.id = tr.teacher_id', 'left');
        $this->db->join('teacher_designation td', 'tr.designation_id = td.id', 'left');
        $this->loadCondition($param);
        $query = $this->db->get('teacher_details t');


        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        $formValues['updated_by'] = $this->session->userdata('id');
        unset($formValues['school']);
        unset($formValues['designation']);

        $this->db->where(array("id" => $id));
        if ($this->db->update('teacher_details', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('teacher_details', array('is_publish' => $publish))) {
            return true;
        }
        return false;
    }

    /**
     * This function used to delete a record
     * here is_approved = 5 means deleted
     * @param type $id
     * @return boolean
     */
    public function delete($members, $year) {
        $batch = [];
        foreach($members as $key => $val){
            $batch[] = [
                "teacher_id" => $val["id"],
                'deleted_year' => $year,
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $this->session->userdata('id')   
            ];
        }
        
        if($this->db->insert_batch('teacher_deleted', $batch)){
            return true;
        }
        return false;
        
//        $data['teacher_id'] = $id;
//        $data['deleted_year'] = $year;
//        $data['deleted_at'] = date("Y-m-d H:i:s");
//        $data['deleted_by'] = $this->session->userdata('id');
//        if ($this->db->insert('teacher_deleted', $data)) {
//            return true;
//        }
//        return false;
    }

    function newProcessEntry($batch) {
        $this->db->insert_batch('teacher_process', $batch);
        return true;
    }

    function updateProcessEntry($data) {
        $this->db->where("teacher_id = " . $data["teacher_id"] . " AND year = " . $data["year"]);
        if ($this->db->update('teacher_process', $data)) {
            return true;
        }
    }

}
