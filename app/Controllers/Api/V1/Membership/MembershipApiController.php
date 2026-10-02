<?php

namespace App\Controllers\Api\V1\Membership;

use App\Controllers\Api\V1\BaseApiController;
use App\Models\Membership\Teacher_model;
use App\Models\Membership\WhatsNew_model;
use CodeIgniter\HTTP\ResponseInterface;

class MembershipApiController extends BaseApiController
{
    /**
     * GET /api/v1/membership/dashboard
     */
    public function dashboard(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $totalTeachers = $db->table('teacher_details')->countAllResults();
        $verifiedCount = $db->table('teacher_process')->where('is_verified', 1)->countAllResults();
        $approvedCount = $db->table('teacher_process')->where('is_approved', 1)->countAllResults();
        $confirmedCount = $db->table('teacher_process')->where('is_confirmed', 1)->countAllResults();

        // Recent whats new
        $whatsNewModel = model(WhatsNew_model::class);
        $rawWhatsNew = $whatsNewModel->getAllNews(['isPublish' => 1, 'limit' => 5]) ?: [];
        $whatsNew = array_map(function ($wn) {
            return [
                'id'          => (int)($wn['id'] ?? 0),
                'title'       => $wn['title'] ?? $wn['heading'] ?? '',
                'description' => $wn['description'] ?? '',
                'file_url'    => !empty($wn['file']) ? $this->formatFileUrl('uploads/whats_new/' . $wn['file']) : null,
                'created_at'  => $this->formatIsoDate($wn['created_at'] ?? null),
            ];
        }, $rawWhatsNew);

        return $this->respondSuccess([
            'metrics' => [
                'total_teachers'  => $totalTeachers,
                'verified'        => $verifiedCount,
                'approved'        => $approvedCount,
                'confirmed'       => $confirmedCount,
                'verified_count'  => $verifiedCount,
                'approved_count'  => $approvedCount,
                'confirmed_count' => $confirmedCount,
            ],
            'total_teachers'  => $totalTeachers,
            'verified'        => $verifiedCount,
            'approved'        => $approvedCount,
            'confirmed'       => $confirmedCount,
            'verified_count'  => $verifiedCount,
            'approved_count'  => $approvedCount,
            'confirmed_count' => $confirmedCount,
            'whats_new'       => $whatsNew,
        ], 'Membership dashboard retrieved successfully.');
    }

    /**
     * GET /api/v1/membership/teachers
     * Query: page, per_page, search, status, district_id, school_id
     */
    public function teachers(): ResponseInterface
    {
        $db = \Config\Database::connect();
        $builder = $db->table('teacher_details')
            ->select('teacher_details.*, teacher_process.school_id, teacher_process.designation_id, teacher_process.year, teacher_process.is_verified, teacher_process.is_approved, teacher_process.is_confirmed')
            ->join('teacher_process', 'teacher_details.id = teacher_process.teacher_id', 'left');

        $page = max(1, (int)$this->request->getGet('page'));
        $perPage = min(100, max(1, (int)($this->request->getGet('per_page') ?: 20)));
        $offset = ($page - 1) * $perPage;

        $search = trim((string)$this->request->getGet('search'));
        if (!empty($search)) {
            $builder->groupStart()
                ->like('teacher_details.name', $search)
                ->orLike('teacher_details.mobile', $search)
                ->groupEnd();
        }

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $rows = $builder->orderBy('teacher_details.id', 'DESC')->limit($perPage, $offset)->get()->getResultArray() ?: [];

        $items = array_map(function ($t) {
            $status = 'PENDING';
            if (!empty($t['is_approved'])) {
                $status = 'APPROVED';
            } elseif (!empty($t['is_verified'])) {
                $status = 'VERIFIED';
            } elseif (!empty($t['is_confirmed'])) {
                $status = 'CONFIRMED';
            }

            return [
                'id'             => (int)($t['id'] ?? 0),
                'name'           => $t['name'] ?? '',
                'mobile'         => $t['mobile'] ?? '',
                'teacher_type'   => (int)($t['teacher_type'] ?? 1),
                'designation_id' => (int)($t['designation_id'] ?? 0),
                'school_id'      => (int)($t['school_id'] ?? 0),
                'status'         => $status,
                'created_at'     => $this->formatIsoDate($t['created_at'] ?? null),
            ];
        }, $rows);

        return $this->respondSuccess($items, 'Teachers list retrieved.', $this->buildPaginationMeta($page, $perPage, $total));
    }

    /**
     * GET /api/v1/membership/teachers/{id}
     */
    public function teacherDetail(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $t = $db->table('teacher_details')
            ->select('teacher_details.*, teacher_process.school_id, teacher_process.designation_id, teacher_process.year, teacher_process.is_verified, teacher_process.is_approved, teacher_process.is_confirmed')
            ->join('teacher_process', 'teacher_details.id = teacher_process.teacher_id', 'left')
            ->where('teacher_details.id', $id)
            ->get(1)
            ->getRowArray();

        if (!$t) {
            return $this->respondNotFound('Teacher record not found.');
        }

        return $this->respondSuccess($t, 'Teacher details retrieved.');
    }

    /**
     * POST /api/v1/membership/teachers
     */
    public function createTeacher(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $name = trim((string)($json['name'] ?? ''));
        $mobile = trim((string)($json['mobile'] ?? ''));

        $errors = [];
        if (empty($name)) {
            $errors['name'] = ['Teacher name is required.'];
        }
        if (empty($mobile)) {
            $errors['mobile'] = ['Mobile number is required.'];
        }
        if (!empty($errors)) {
            return $this->respondValidationFailed($errors);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $data = [
            'name'                          => $name,
            'mobile'                        => $mobile,
            'teacher_type'                  => (int)($json['teacher_type'] ?? 1),
            'adhyapaka_sabdham_subscriber' => !empty($json['adhyapaka_sabdham_subscriber']) ? 1 : 0,
            'created_at'                    => date('Y-m-d H:i:s'),
        ];
        $db->table('teacher_details')->insert($data);
        $teacherId = $db->insertID();

        // Also record in teacher_process
        $db->table('teacher_process')->insert([
            'teacher_id'     => $teacherId,
            'school_id'      => (int)($json['school_id'] ?? 0),
            'designation_id' => (int)($json['designation_id'] ?? 1),
            'year'           => (int)($json['year'] ?? date('Y')),
            'is_confirmed'   => 0,
            'is_verified'    => 0,
            'is_approved'    => 0,
        ]);

        $db->transCommit();

        return $this->respondCreated(['id' => (int)$teacherId], 'Teacher record created successfully.');
    }

    /**
     * PUT /api/v1/membership/teachers/{id}
     */
    public function updateTeacher(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $db = \Config\Database::connect();

        $existing = $db->table('teacher_details')->where('id', $id)->get(1)->getRowArray();
        if (!$existing) {
            return $this->respondNotFound('Teacher record not found.');
        }

        $update = [];
        if (isset($json['name'])) {
            $update['name'] = trim((string)$json['name']);
        }
        if (isset($json['mobile'])) {
            $update['mobile'] = trim((string)$json['mobile']);
        }
        if (isset($json['teacher_type'])) {
            $update['teacher_type'] = (int)$json['teacher_type'];
        }

        if (!empty($update)) {
            $db->table('teacher_details')->where('id', $id)->update($update);
        }

        $processUpdate = [];
        if (isset($json['school_id'])) {
            $processUpdate['school_id'] = (int)$json['school_id'];
        }
        if (isset($json['designation_id'])) {
            $processUpdate['designation_id'] = (int)$json['designation_id'];
        }
        if (!empty($processUpdate)) {
            $db->table('teacher_process')->where('teacher_id', $id)->update($processUpdate);
        }

        return $this->respondSuccess(['id' => $id], 'Teacher record updated successfully.');
    }

    /**
     * DELETE /api/v1/membership/teachers/{id}
     */
    public function deleteTeacher(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();
        $db->table('teacher_process')->where('teacher_id', $id)->delete();
        $db->table('teacher_details')->where('id', $id)->delete();
        return $this->respondSuccess(null, 'Teacher record deleted successfully.');
    }

    /**
     * POST /api/v1/membership/teachers/{id}/process
     * Transition status (VERIFIED / APPROVED / CONFIRMED)
     */
    public function processTeacher(int $id): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $action = strtoupper(trim((string)($json['action'] ?? '')));

        $validActions = ['VERIFIED', 'APPROVED', 'CONFIRMED', 'RESET'];
        if (!in_array($action, $validActions, true)) {
            return $this->respondValidationFailed([
                'action' => ['Invalid action. Must be VERIFIED, APPROVED, CONFIRMED, or RESET.'],
            ]);
        }

        $db = \Config\Database::connect();
        $update = [];
        $now = date('Y-m-d H:i:s');
        if ($action === 'CONFIRMED') {
            $update['is_confirmed'] = 1;
            $update['confirmed_date'] = $now;
        } elseif ($action === 'VERIFIED') {
            $update['is_verified'] = 1;
        } elseif ($action === 'APPROVED') {
            $update['is_approved'] = 1;
            $update['approved_date'] = $now;
        } elseif ($action === 'RESET') {
            $update['is_confirmed'] = 0;
            $update['is_verified'] = 0;
            $update['is_approved'] = 0;
        }

        $db->table('teacher_process')->where('teacher_id', $id)->update($update);

        return $this->respondSuccess([
            'id'     => $id,
            'status' => $action,
        ], "Teacher status updated to {$action}.");
    }

    /**
     * GET /api/v1/membership/whats-new
     */
    public function whatsNew(): ResponseInterface
    {
        $whatsNewModel = model(WhatsNew_model::class);
        $raw = $whatsNewModel->getAllNews(['isPublish' => 1]) ?: [];

        $items = array_map(function ($wn) {
            return [
                'id'          => (int)($wn['id'] ?? 0),
                'title'       => $wn['title'] ?? $wn['heading'] ?? '',
                'description' => $wn['description'] ?? '',
                'file_url'    => !empty($wn['file']) ? $this->formatFileUrl('uploads/whats_new/' . $wn['file']) : null,
                'created_at'  => $this->formatIsoDate($wn['created_at'] ?? null),
            ];
        }, $raw);

        return $this->respondSuccess($items, "What's New items retrieved successfully.");
    }

    /**
     * GET /api/v1/membership/counts
     * Returns live membership tallies aggregated directly from SQL database.
     */
    public function counts(): ResponseInterface
    {
        $db = \Config\Database::connect();
        $group = (int)($this->request->getGet('group') ?: 1); // 1=State, 2=District, 4=SubDist, 6=School
        $office = (int)($this->request->getGet('office') ?: 0);
        $year = $this->request->getGet('year') ?: date('Y');

        $builder = $db->table('teacher_details t')
            ->select('
                d.id AS district_id,
                d.name AS district_name,
                COUNT(t.id) AS total_count,
                SUM(CASE WHEN tr.is_confirmed = 1 THEN 1 ELSE 0 END) AS confirmed_count,
                SUM(CASE WHEN tr.is_verified = 1 THEN 1 ELSE 0 END) AS verified_count,
                SUM(CASE WHEN tr.is_approved = 1 THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govt_members,
                SUM(CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aided_members
            ')
            ->join('teacher_process tr', 't.id = tr.teacher_id', 'left')
            ->join('school s', 'tr.school_id = s.id', 'left')
            ->join('branch b', 's.branch_id = b.id', 'left')
            ->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left')
            ->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left')
            ->join('district d', 'ed.district_id = d.id', 'left')
            ->groupBy('d.id')
            ->orderBy('d.name');

        if ($office > 0) {
            $builder->where('d.id', $office);
        }

        $rows = $builder->get()->getResultArray();

        $tlTotal = 0;
        $tlConfirmed = 0;
        $tlVerified = 0;
        $tlApproved = 0;
        $tlGovt = 0;
        $tlAided = 0;

        $items = [];
        foreach ($rows as $r) {
            $total = (int)($r['total_count'] ?? 0);
            $confirmed = (int)($r['confirmed_count'] ?? 0);
            $verified = (int)($r['verified_count'] ?? 0);
            $approved = (int)($r['approved_count'] ?? 0);
            $govt = (int)($r['govt_members'] ?? 0);
            $aided = (int)($r['aided_members'] ?? 0);

            $tlTotal += $total;
            $tlConfirmed += $confirmed;
            $tlVerified += $verified;
            $tlApproved += $approved;
            $tlGovt += $govt;
            $tlAided += $aided;

            $items[] = [
                'id'              => (int)($r['district_id'] ?? 0),
                'name'            => $r['district_name'] ?? 'General',
                'total'           => $total,
                'confirmed'       => $confirmed,
                'verified'        => $verified,
                'approved'        => $approved,
                'govt_members'    => $govt,
                'aided_members'   => $aided,
            ];
        }

        return $this->respondSuccess([
            'year'     => $year,
            'group'    => $group,
            'items'    => $items,
            'totals'   => [
                'total'           => $tlTotal,
                'confirmed'       => $tlConfirmed,
                'verified'        => $tlVerified,
                'approved'        => $tlApproved,
                'govt_members'    => $tlGovt,
                'aided_members'   => $tlAided,
            ],
        ], 'Membership counts retrieved successfully.');
    }

    /**
     * GET /api/v1/membership/reports
     * Returns consolidated membership reports calculated directly by database.
     */
    public function reports(): ResponseInterface
    {
        $db = \Config\Database::connect();
        $type = $this->request->getGet('type') ?: 'district';
        $districtId = (int)$this->request->getGet('district_id');

        if ($type === 'designation') {
            $builder = $db->table('teacher_details t')
                ->select('
                    td.id,
                    td.name AS designation,
                    SUM(CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govt_members,
                    SUM(CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aided_members,
                    COUNT(t.id) AS total_count
                ')
                ->join('teacher_process tr', 't.id = tr.teacher_id', 'left')
                ->join('teacher_designation td', 'tr.designation_id = td.id', 'left')
                ->groupBy('td.id')
                ->orderBy('td.name');
        } elseif ($type === 'sub_district') {
            $builder = $db->table('teacher_details t')
                ->select('
                    sd.id,
                    sd.name AS name,
                    ed.name AS edu_district,
                    SUM(CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govt_members,
                    SUM(CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aided_members,
                    COUNT(t.id) AS total_count
                ')
                ->join('teacher_process tr', 't.id = tr.teacher_id', 'left')
                ->join('school s', 'tr.school_id = s.id', 'left')
                ->join('branch b', 's.branch_id = b.id', 'left')
                ->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left')
                ->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left')
                ->groupBy('sd.id')
                ->orderBy('ed.name, sd.name');
        } elseif ($type === 'school') {
            $builder = $db->table('teacher_details t')
                ->select('
                    s.id,
                    s.name AS name,
                    b.name AS branch,
                    SUM(CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govt_members,
                    SUM(CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aided_members,
                    COUNT(t.id) AS total_count
                ')
                ->join('teacher_process tr', 't.id = tr.teacher_id', 'left')
                ->join('school s', 'tr.school_id = s.id', 'left')
                ->join('branch b', 's.branch_id = b.id', 'left')
                ->groupBy('s.id')
                ->orderBy('b.name, s.name')
                ->limit(200);
        } else {
            // Default: district-wise
            $builder = $db->table('teacher_details t')
                ->select('
                    d.id,
                    d.name AS name,
                    SUM(CASE WHEN t.teacher_type = 1 THEN 1 ELSE 0 END) AS govt_members,
                    SUM(CASE WHEN t.teacher_type = 2 THEN 1 ELSE 0 END) AS aided_members,
                    COUNT(t.id) AS total_count
                ')
                ->join('teacher_process tr', 't.id = tr.teacher_id', 'left')
                ->join('school s', 'tr.school_id = s.id', 'left')
                ->join('branch b', 's.branch_id = b.id', 'left')
                ->join('sub_dist sd', 'b.sub_dist_id = sd.id', 'left')
                ->join('education_dist ed', 'sd.education_dist_id = ed.id', 'left')
                ->join('district d', 'ed.district_id = d.id', 'left')
                ->groupBy('d.id')
                ->orderBy('d.name');
        }

        if ($districtId > 0 && in_array($type, ['sub_district', 'school'])) {
            $builder->where('d.id', $districtId);
        }

        $rows = $builder->get()->getResultArray();

        $tlGovt = 0;
        $tlAided = 0;
        $tlTotal = 0;

        $items = [];
        foreach ($rows as $r) {
            $govt = (int)($r['govt_members'] ?? 0);
            $aided = (int)($r['aided_members'] ?? 0);
            $total = (int)($r['total_count'] ?? 0);

            $tlGovt += $govt;
            $tlAided += $aided;
            $tlTotal += $total;

            $items[] = [
                'id'            => (int)($r['id'] ?? 0),
                'name'          => $r['name'] ?? $r['designation'] ?? 'Unknown',
                'extra_label'   => $r['edu_district'] ?? $r['branch'] ?? null,
                'govt_members'  => $govt,
                'aided_members' => $aided,
                'total_count'   => $total,
            ];
        }

        return $this->respondSuccess([
            'report_type' => $type,
            'items'       => $items,
            'totals'      => [
                'govt_members'  => $tlGovt,
                'aided_members' => $tlAided,
                'total_count'   => $tlTotal,
            ],
        ], 'Membership report generated.');
    }

    /**
     * GET /api/v1/membership/metadata
     * Returns actual database dropdown records for teachers, schools, districts, and designations.
     */
    public function metadata(): ResponseInterface
    {
        $db = \Config\Database::connect();
        $designations = $db->table('teacher_designation')->select('id, name')->orderBy('name')->get()->getResultArray();
        if (empty($designations)) {
            $designations = $db->table('office_bearer_designation')->select('id, name')->orderBy('name')->get()->getResultArray();
        }
        $districts = $db->table('district')->select('id, name')->orderBy('name')->get()->getResultArray();
        $schools = $db->table('school')->select('id, name, code')->limit(300)->orderBy('name')->get()->getResultArray();

        return $this->respondSuccess([
            'designations' => array_map(fn($d) => ['id' => (int)$d['id'], 'name' => $d['name']], $designations),
            'districts'    => array_map(fn($d) => ['id' => (int)$d['id'], 'name' => $d['name']], $districts),
            'schools'      => array_map(fn($s) => ['id' => (int)$s['id'], 'name' => $s['name'], 'code' => $s['code'] ?? ''], $schools),
        ], 'Membership form metadata retrieved.');
    }
}
