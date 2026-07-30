<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Main_model extends Membership_Model {

    function __construct() {
        parent::__construct();
    }

    public function getDistrictByOffice($groupId, $officeId) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 10;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;
        $param['search'] = isset($param['search']) ? $param['search'] : FALSE;

        $this->db->select('d.id, d.name AS district, d.code');
        $this->db->from('district d');

        if ($groupId == static::AAUTH_GROUP_BRANCH) {
            $this->db->join('education_dist ed', 'ed.district_id = d.id');
            $this->db->join('sub_dist sd', 'sd.education_dist_id = ed.id');
            $this->db->join('branch b', 'b.sub_dist_id = sd.id');
            $this->db->where(array("b.id" => $officeId));
        } else if ($groupId == static::AAUTH_GROUP_SUB_DIST) {
            $this->db->join('education_dist ed', 'ed.district_id = d.id');
            $this->db->join('sub_dist sd', 'sd.education_dist_id = ed.id');
            $this->db->where(array("sd.id" => $officeId));
        } else if ($groupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $this->db->join('education_dist ed', 'ed.district_id = d.id');
            $this->db->where(array("ed.id" => $officeId));
        } else if ($groupId == static::AAUTH_GROUP_DISTRICT) {
            $this->db->where(array("d.id" => $officeId));
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_STATE) {
            $this->db->join('branch b', 's.branch_id = b.id', 'left');
        }

        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return array();
    }

    function getListUser($param) {
        $param['limit'] = isset($param['limit']) ? $param['limit'] : false;
        $param['offset'] = isset($param['offset']) ? $param['offset'] : 0;

        $this->db->select("au.*,  g.name as groupName, 
                                CASE  au.group_id 
                                WHEN 1 THEN 'state' 
                                WHEN 2 THEN d.name 
                                WHEN 3 THEN ed.name  
                                WHEN 4 THEN sd.name 
                                 
                            END AS  officeName"
        );
        $this->db->from("aauth_users au")
                ->where(array("au.group_id  > " => $this->aauthGroupId))
                ->join("aauth_groups g", "au.group_id = " . "g" . ".id");


        $this->loadCondition($param);

        if ($param['limit']) {
            $this->db->limit($param['limit'], $param['offset']);
        }

        $this->db->group_by("au.id ");
        $query = $this->db->get();
//        echo $this->db->last_query();exit;
        return $query->result();
    }

    function loadCondition($param = array()) {

        if (in_array($this->aauthGroupId, [static::AAUTH_GROUP_SUB_DIST, static::AAUTH_GROUP_EDUCATION_DIST, static::AAUTH_GROUP_DISTRICT])) {
            $this->db->join('(' . $param['compiledSelectQuery'] . ') AS cs', "1=1", 'left', false);

            $this->db->where("(CASE WHEN cs.group_id = 3 THEN  au.group_id = cs.group_id  AND cs.id =  au.office_id "
                    . "  WHEN cs.group_id = 4 THEN au.group_id = cs.group_id AND cs.id =  au.office_id END   )", '', FALSE);
        }

        $this->db->join("district d", "au.office_id = d.id AND au.group_id = 2 ", 'left')
                ->join("education_dist ed", "au.office_id = ed.id AND au.group_id = 3 ", 'left')
                ->join("sub_dist sd", "au.office_id = sd.id AND au.group_id = 4 ", 'left')
                ->join("branch b", "au.office_id = b.id AND au.group_id = 5 ", 'left');



        if (isset($param['search']) && $param['search']) {
            $this->db->where("au.name LIKE ", '%' . $param['search'] . '%');
        }


        if (isset($param['groupId']) && $param['groupId']) {
            $this->db->where(array("au.group_id" => $param['groupId']));
        }

        if (isset($param['officeId']) && $param['officeId'] && isset($param['groupId'])) {
            if ($param['groupId'] == static::AAUTH_GROUP_DISTRICT) {
                $this->db->where(array("d.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_EDUCATION_DIST) {
                $this->db->where(array("ed.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_SUB_DIST) {
                $this->db->where(array("sd.id" => $param['officeId']));
            } else if ($param['groupId'] == static::AAUTH_GROUP_BRANCH) {
                $this->db->where(array("b.id" => $param['officeId']));
            }
        }
    }

    function getListUserCount($param) {

        $this->db->select('count(DISTINCT au.id) as count')
                ->from("aauth_users au")
                ->where(array("au.group_id  > " => $this->aauthGroupId));

        $this->loadCondition($param);

        $query = $this->db->get();
        $result = $query->row(0, 'array');

        return $result['count'];
    }

}
