<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class TeacherProcess_model extends Membership_Model {

    function __construct() {
        parent::__construct();
        $this->getYear();
    }

    function approveByOffice($members) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($row['approvedId'] || $row['processId']) {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'approved_by' => $this->session->userdata('id'),
                    'approved_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                $insertArray[] = array(
                    'teacher_id' => $row['id'],
                    'approved_by' => $this->session->userdata('id'),
                    'approved_date' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'year' => $this->year
                );
            }
        }

        if (count($insertArray)) {
            $this->db->insert_batch('teacher_process', $insertArray);
        }

        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function confirmByOffice($members) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($row['confirmedId'] || $row['processId']) {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'confirmed_by' => $this->session->userdata('id'),
                    'confirmed_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                $insertArray[] = array(
                    'teacher_id' => $row['id'],
                    'confirmed_by' => $this->session->userdata('id'),
                    'confirmed_date' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'year' => $this->year
                );
            }
        }

        if (count($insertArray)) {
            $this->db->insert_batch('teacher_process', $insertArray);
        }

        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function confirm($members, $param) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($param['action'] == "reject") {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'is_confirmed' => 2,
                    'confirmed_by' => $this->session->userdata('id'),
                    'confirmed_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                if ($row['processId']) {
                    $updateArray[] = array(
                        'id' => $row['processId'],
                        'is_confirmed' => 1,
                        'confirmed_by' => $this->session->userdata('id'),
                        'confirmed_date' => (new \DateTime())->format('Y-m-d H:i:s')
                    );
                } else {
                    $insertArray[] = array(
                        'teacher_id' => $row['id'],
                        'is_confirmed' => 1,
                        'confirmed_by' => $this->session->userdata('id'),
                        'confirmed_date' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'year' => $this->year
                    );
                }
            }
        }

        if (count($insertArray)) {
            $this->db->insert_batch('teacher_process', $insertArray);
        }

        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function check($members, $param) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($param['action'] == "reject") {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'is_checked' => 2,
                    'checked_by' => $this->session->userdata('id'),
                    'checked_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                if ($row['processId']) {
                    $updateArray[] = array(
                        'id' => $row['processId'],
                        'is_checked' => 1,
                        'checked_by' => $this->session->userdata('id'),
                        'checked_date' => (new \DateTime())->format('Y-m-d H:i:s')
                    );
                }
            }
        }

        if (count($insertArray)) {
            $this->db->insert_batch('teacher_process', $insertArray);
        }

        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function verify($members, $param) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($param['action'] == "reject") {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'is_verified' => 2,
                    'verified_by' => $this->session->userdata('id'),
                    'verified_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                if ($row['processId']) {
                    $updateArray[] = array(
                        'id' => $row['processId'],
                        'is_verified' => 1,
                        'verified_by' => $this->session->userdata('id'),
                        'verified_date' => (new \DateTime())->format('Y-m-d H:i:s')
                    );
                }
            }
        }

        if (count($insertArray)) {
            $this->db->insert_batch('teacher_process', $insertArray);
        }

        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function approve($members, $param) {
        ini_set('max_execution_time', 0);

        if (!count($members)) {
            return false;
        }

        $insertArray = $updateArray = [];
        foreach ($members as $row) {
            if ($param['action'] == "reject") {
                $updateArray[] = array(
                    'id' => $row['processId'],
                    'is_approved' => 2,
                    'approved_by' => $this->session->userdata('id'),
                    'approved_date' => (new \DateTime())->format('Y-m-d H:i:s')
                );
            } else {
                if ($row['processId']) {
                    $updateArray[] = array(
                        'id' => $row['processId'],
                        'is_approved' => 1,
                        'approved_by' => $this->session->userdata('id'),
                        'approved_date' => (new \DateTime())->format('Y-m-d H:i:s')
                    );
                }
            }
        }


        if (count($updateArray)) {
            $this->db->update_batch('teacher_process', $updateArray, 'id');
        }
    }

    function createProcessEntry($formYear) {
        $previousYear = $formYear - 1;
        $sql = " INSERT INTO teacher_process (id, teacher_id, year, school_id, designation_id)"
                . " SELECT NULL as id , teacher_id, " . $formYear . " AS year,  school_id, designation_id"
                . " FROM  teacher_process "
                . " WHERE year = " . $previousYear . " AND is_approved = 1 AND teacher_id NOT IN (SELECT teacher_id FROM teacher_process WHERE year = " . $formYear . " )";

        $this->db->query($sql);
        return true;
    }

  

}
