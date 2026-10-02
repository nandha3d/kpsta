<?php

namespace App\Controllers\Api\V1;

use App\Models\District_model;
use App\Models\OfficeBearer_model;
use App\Models\Settings_model;
use CodeIgniter\HTTP\ResponseInterface;

class OrganizationController extends BaseApiController
{
    /**
     * GET /api/v1/office-bearers
     * Query: level (State|District), active_term, designation
     */
    public function officeBearers(): ResponseInterface
    {
        $obModel = model(OfficeBearer_model::class);
        $settingsModel = model(Settings_model::class);

        $level = $this->request->getGet('level') ?: 'State';
        $activeTerm = $this->request->getGet('active_term') ?: $settingsModel->getActiveTerm();

        $params = [
            'isPublish'   => true,
            'is_former'   => 0,
            'limit'       => 200,
            'active_term' => $activeTerm,
            'level'       => $level,
        ];

        $rawList = $obModel->getAll($params) ?: [];

        // Group by section heading
        $grouped = [];
        foreach ($rawList as $row) {
            $section = !empty($row['section_heading']) ? $row['section_heading'] : ($row['designation_name'] ?? 'Other');
            $grouped[$section][] = [
                'id'              => (int)($row['id'] ?? 0),
                'name'            => $row['name'] ?? '',
                'designation_id'  => (int)($row['designation'] ?? 0),
                'designation'     => $row['designation_name'] ?? $row['designation'] ?? '',
                'phone'           => !empty($row['phone']) ? (string)$row['phone'] : null,
                'email'           => $row['email'] ?? null,
                'address'         => $row['address'] ?? null,
                'photo_url'       => !empty($row['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $row['photo']) : null,
                'year'            => $row['year'] ?? null,
                'level'           => $row['level'] ?? 'State',
                'section_heading' => $row['section_heading'] ?? null,
                'position'        => (int)($row['position'] ?? 25),
            ];
        }

        // Standard ordering for State office bearers
        $sectionOrder = [
            'President / General Secretary / Treasurer',
            'Senior Vice President & Associate General Secretary',
            'Vice President',
            'Secretary',
            'Secretariate Members',
        ];
        $ordered = [];
        foreach ($sectionOrder as $sec) {
            if (isset($grouped[$sec])) {
                $ordered[$sec] = $grouped[$sec];
                unset($grouped[$sec]);
            }
        }
        foreach ($grouped as $sec => $bearers) {
            $ordered[$sec] = $bearers;
        }

        return $this->respondSuccess([
            'level'          => $level,
            'active_term'    => $activeTerm,
            'sections'       => $ordered,
        ], 'Office bearers retrieved successfully.');
    }

    /**
     * GET /api/v1/former-leaders
     */
    public function formerLeaders(): ResponseInterface
    {
        $obModel = model(OfficeBearer_model::class);
        $raw = $obModel->getAll(['isPublish' => true, 'is_former' => 1, 'limit' => 500]) ?: [];

        $leaders = [];
        $personIndex = [];

        foreach ($raw as $ob) {
            $normName = mb_strtolower(trim((string)$ob['name']));
            $phone = !empty($ob['phone']) ? preg_replace('/[^0-9]/', '', (string)$ob['phone']) : '';
            $lookupKey = !empty($phone) ? ($normName . '|' . $phone) : $normName;

            $primaryPos = [
                'designation'     => $ob['designation_name'] ?? $ob['designation'] ?? '',
                'year'            => !empty($ob['year']) ? $ob['year'] : '',
                'level'           => !empty($ob['level']) ? $ob['level'] : 'State',
                'section_heading' => !empty($ob['section_heading']) ? $ob['section_heading'] : '',
                'position'        => isset($ob['position']) && is_numeric($ob['position']) ? (int)$ob['position'] : 25,
            ];

            $additionalPositions = [];
            if (!empty($ob['previous_positions'])) {
                $decoded = is_array($ob['previous_positions']) ? $ob['previous_positions'] : json_decode($ob['previous_positions'], true);
                if (is_array($decoded)) {
                    $additionalPositions = $decoded;
                }
            }

            if (isset($personIndex[$lookupKey])) {
                $idx = $personIndex[$lookupKey];
                $leaders[$idx]['all_positions'][] = $primaryPos;
                foreach ($additionalPositions as $pos) {
                    $leaders[$idx]['all_positions'][] = $pos;
                }
                if (empty($leaders[$idx]['photo']) && !empty($ob['photo'])) {
                    $leaders[$idx]['photo'] = $ob['photo'];
                }
            } else {
                $entry = $ob;
                $entry['all_positions'] = array_merge([$primaryPos], $additionalPositions);
                $personIndex[$lookupKey] = count($leaders);
                $leaders[] = $entry;
            }
        }

        // Format clean leader objects
        $formatted = array_map(function ($ldr) {
            return [
                'id'          => (int)($ldr['id'] ?? 0),
                'name'        => $ldr['name'] ?? '',
                'photo_url'   => !empty($ldr['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $ldr['photo']) : null,
                'phone'       => !empty($ldr['phone']) ? (string)$ldr['phone'] : null,
                'email'       => $ldr['email'] ?? null,
                'positions'   => $ldr['all_positions'] ?? [],
            ];
        }, $leaders);

        return $this->respondSuccess($formatted, 'Former leaders retrieved successfully.');
    }

    /**
     * GET /api/v1/districts
     */
    public function districts(): ResponseInterface
    {
        $districtModel = model(District_model::class);
        $obModel = model(OfficeBearer_model::class);
        $settingsModel = model(Settings_model::class);

        $districts = $districtModel->getAllDistrict(['limit' => 50]) ?: [];
        $activeTerm = $settingsModel->getActiveTerm();

        // Retrieve all active district office bearers
        $allDistrictBearers = $obModel->getAll([
            'isPublish'   => true,
            'is_former'   => 0,
            'active_term' => $activeTerm,
            'level'       => 'District',
            'limit'       => 1500,
        ]) ?: [];

        // Group bearers by district name (stored in section_heading)
        $districtBearers = [];
        foreach ($allDistrictBearers as $bearer) {
            $distName = $bearer['section_heading'] ?? 'Other';
            $districtBearers[$distName][] = [
                'id'             => (int)($bearer['id'] ?? 0),
                'name'           => $bearer['name'] ?? '',
                'designation'    => $bearer['designation_name'] ?? $bearer['designation'] ?? '',
                'phone'          => !empty($bearer['phone']) ? (string)$bearer['phone'] : null,
                'email'          => $bearer['email'] ?? null,
                'photo_url'      => !empty($bearer['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $bearer['photo']) : null,
            ];
        }

        $items = array_map(function ($d) use ($districtBearers) {
            $name = $d['district'] ?? '';
            return [
                'id'             => (int)($d['id'] ?? 0),
                'name'           => $name,
                'office_bearers' => $districtBearers[$name] ?? [],
            ];
        }, $districts);

        return $this->respondSuccess($items, 'Districts retrieved successfully.');
    }

    /**
     * GET /api/v1/districts/{id}
     */
    public function districtDetail(int $id): ResponseInterface
    {
        $districtModel = model(District_model::class);
        $obModel = model(OfficeBearer_model::class);
        $settingsModel = model(Settings_model::class);

        $d = $districtModel->get($id);
        if (!$d) {
            return $this->respondNotFound('District not found.');
        }

        $distName = $d['district'] ?? '';
        $activeTerm = $settingsModel->getActiveTerm();

        $bearers = $obModel->getAll([
            'isPublish'       => true,
            'is_former'       => 0,
            'active_term'     => $activeTerm,
            'level'           => 'District',
            'section_heading' => $distName,
            'limit'           => 100,
        ]) ?: [];

        $formattedBearers = array_map(function ($b) {
            return [
                'id'          => (int)($b['id'] ?? 0),
                'name'        => $b['name'] ?? '',
                'designation' => $b['designation_name'] ?? $b['designation'] ?? '',
                'phone'       => !empty($b['phone']) ? (string)$b['phone'] : null,
                'email'       => $b['email'] ?? null,
                'photo_url'   => !empty($b['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $b['photo']) : null,
            ];
        }, $bearers);

        return $this->respondSuccess([
            'id'             => (int)$d['id'],
            'name'           => $distName,
            'office_bearers' => $formattedBearers,
        ], 'District details retrieved successfully.');
    }

    /**
     * GET /api/v1/editorial-board
     */
    public function editorialBoard(): ResponseInterface
    {
        $db = \Config\Database::connect();
        $builder = $db->table('editorial_board');
        $rows = $builder->where('status', 1)->orderBy('position', 'ASC')->get()->getResultArray() ?: [];

        $items = array_map(function ($eb) {
            return [
                'id'          => (int)($eb['id'] ?? 0),
                'name'        => $eb['name'] ?? '',
                'designation' => $eb['designation'] ?? '',
                'phone'       => $eb['phone'] ?? null,
                'email'       => $eb['email'] ?? null,
                'photo_url'   => !empty($eb['photo']) ? $this->formatFileUrl('uploads/editorial_board/' . $eb['photo']) : null,
            ];
        }, $rows);

        return $this->respondSuccess($items, 'Editorial board members retrieved.');
    }
}
