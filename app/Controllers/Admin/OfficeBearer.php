<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
class OfficeBearer extends AppController {

    private $imageWidthThumb, $imageHeightThumb;

    public function ci3Init(): void {
        parent::ci3Init();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
        $this->load->model("OfficeBearer_model");
        $this->load->model("District_model");

        $this->imageWidthThumb = 173;
        $this->imageHeightThumb = 214;
    }

    /**
     * Load live district names from the district table.
     */
    protected function getDistrictOptions(): array {
        $rows = $this->District_model->getAllDistrict(['limit' => 50]);
        $options = [];
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $dName = trim((string)($r['district'] ?? ''));
                if ($dName !== '') {
                    $options[$dName] = $dName;
                }
            }
        }
        if (empty($options)) {
            $fallback = [
                'Thiruvananthapuram', 'Kollam', 'Pathanamthitta', 'Allapuzha',
                'Kottayam', 'Idukki', 'Ernakulam', 'Thrissur', 'Palakkad',
                'Malappuram', 'Kozhikode', 'Wayanad', 'Kannur', 'Kasargod',
            ];
            foreach ($fallback as $dName) {
                $options[$dName] = $dName;
            }
        }
        return $options;
    }

    public function stateLeaders($roleSlug = '') {
        $this->renderSectionPage('state', (string)$roleSlug);
    }

    public function formerStateLeaders($roleSlug = '') {
        $this->renderSectionPage('former', (string)$roleSlug);
    }

    public function districtLeaders($roleSlug = '') {
        $this->renderSectionPage('district', (string)$roleSlug);
    }

    protected function normalizeSectionKey(string $section): string {
        $key = strtolower(trim($section));
        return in_array($key, ['state', 'former', 'district'], true) ? $key : 'state';
    }

    protected function rowMatchesSection(array $row, string $section): bool {
        $level = $row['level'] ?? 'State';
        $isFormer = (int)($row['is_former'] ?? 0);
        if ($section === 'district') {
            return $level === 'District' && $isFormer === 0;
        }
        if ($section === 'former') {
            $hasPrev = !empty($row['previous_positions']) && $row['previous_positions'] !== '[]';
            return $level === 'State' && ($isFormer === 1 || $hasPrev);
        }
        return $level === 'State' && $isFormer === 0;
    }

    protected function renderSectionPage(string $section, string $roleSlug = '') {
        $section = $this->normalizeSectionKey($section);
        $this->OfficeBearer_model->ensureCoreDesignations();
        $meta = $this->OfficeBearer_model->getSectionMeta($section);

        $roleSlug = strtolower(trim($roleSlug));
        if ($roleSlug === '' || !isset($meta['roles'][$roleSlug])) {
            $qRole = strtolower(trim((string)$this->input->get('role')));
            $roleSlug = isset($meta['roles'][$qRole]) ? $qRole : '';
        }

        $selectedDistrict = ($section === 'district') ? trim((string)$this->input->get('district')) : '';
        $districts = $this->getDistrictOptions();
        if ($selectedDistrict !== '' && !isset($districts[$selectedDistrict])) {
            $selectedDistrict = '';
        }

        $contentHtml = $this->getSectionContent($section, $roleSlug);
        $roleCounts = $this->OfficeBearer_model->getSectionRoleCounts(
            $section,
            $selectedDistrict !== '' ? $selectedDistrict : null
        );

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'code'       => 'success',
                'content'    => $contentHtml,
                'roleCounts' => $roleCounts,
                'newUrl'     => $this->newUrl,
            ]);
            exit;
        }

        $data = [
            'section'          => $section,
            'sectionMeta'      => $meta,
            'activeRole'       => $roleSlug,
            'roleCounts'       => $roleCounts,
            'districts'        => $districts,
            'selectedDistrict' => $selectedDistrict,
            'form'             => $this->createSectionForm(
                $section,
                base_url($meta['base_route'] . '/add'),
                false,
                'Add ' . ($roleSlug !== '' ? $meta['roles'][$roleSlug]['label'] : $meta['title']),
                $roleSlug,
                $selectedDistrict
            ),
            'content'          => $contentHtml,
        ];

        $header = [
            'page_title'  => $meta['title'] . ($roleSlug !== '' ? ' — ' . $meta['roles'][$roleSlug]['label'] : ''),
            'public_url'  => $meta['public_url'],
            'special_css' => ['plugins/select2/select2.min.css', 'css/imgareaselect-default.css'],
        ];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/jquery.imgareaselect.min.js', 'js/officeBearerSection.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/officeBearer/section_page', $data);
        $this->load->view('admin/footer', $footer);
    }

    protected function getSectionContent(string $section, string $activeRole = '') {
        $this->load->library("pagination");
        $meta = $this->OfficeBearer_model->getSectionMeta($section);

        $roleFromQuery = strtolower(trim((string)$this->input->get('role')));
        if ($roleFromQuery !== '' && isset($meta['roles'][$roleFromQuery])) {
            $activeRole = $roleFromQuery;
        }

        $param = [
            'search'     => trim((string)$this->input->get('search')),
            'level'      => $meta['level'],
            'is_former'  => $meta['is_former'],
            'is_publish' => $this->input->get('is_publish'),
            'sort'       => $this->input->get('sort') ?: 'position-asc',
            'limit'      => 15,
        ];

        $selectedDistrict = '';
        if ($section === 'district') {
            $selectedDistrict = trim((string)$this->input->get('district'));
            if ($selectedDistrict !== '') {
                $param['exact_district'] = $selectedDistrict;
            }
        }

        if ($activeRole !== '' && isset($meta['roles'][$activeRole])) {
            $this->OfficeBearer_model->applySectionRoleParam($param, $section, $activeRole);
        }

        $page = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['page'] = $page > 0 ? $page : 1;
        $param['offset'] = ($param['page'] - 1) * $param['limit'];

        $config["total_rows"] = $this->OfficeBearer_model->getAllCount($param);
        $baseRoute = $meta['base_route'] . ($activeRole !== '' ? '/' . $activeRole : '');
        $config["base_url"] = base_url($baseRoute);
        $config["per_page"] = $param['limit'];

        $config = array_merge($config, $this->BootsrapPaginationConfig());
        $this->pagination->initialize($config);

        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];

        $this->newUrl = $this->getNewUrl([
            'page'       => $param['page'],
            'search'     => $param['search'],
            'district'   => $selectedDistrict,
            'is_publish' => $param['is_publish'],
            'sort'       => $param['sort'],
        ]);

        $content = [
            'links'       => $this->pagination->create_links(),
            'config'      => $config,
            'content'     => $this->OfficeBearer_model->getAll($param),
            'section'     => $section,
            'sectionMeta' => $meta,
            'activeRole'  => $activeRole,
        ];

        return $this->load->view('admin/officeBearer/section_content', $content, TRUE);
    }

    protected function createSectionForm(
        string $section,
        string $url,
        $formValues = false,
        string $title = 'Add',
        string $activeRole = '',
        string $activeDistrict = ''
    ) {
        $meta = $this->OfficeBearer_model->getSectionMeta($section);
        $designations = $this->OfficeBearer_model->getDesignationsForSection($section);

        $roleDefaultDesigId = '';
        $roleDefaultPos = 25;
        if ($activeRole !== '' && isset($meta['roles'][$activeRole])) {
            $roleDef = $meta['roles'][$activeRole];
            $roleDefaultPos = (int)$roleDef['position'];
            foreach ($designations as $dId => $dLabel) {
                if (strcasecmp($dLabel, $roleDef['label']) === 0) {
                    $roleDefaultDesigId = $dId;
                    break;
                }
            }
        }

        if ($formValues === false) {
            $formValues = [
                'designation'     => $roleDefaultDesigId,
                'section_heading' => $section === 'district' ? $activeDistrict : '',
                'position'        => $roleDefaultPos,
                'is_publish'      => 1,
            ];
        } else {
            if (!isset($formValues['is_publish'])) {
                $formValues['is_publish'] = 1;
            }
            if (isset($formValues['previous_positions']) && is_string($formValues['previous_positions'])) {
                $decoded = json_decode($formValues['previous_positions'], true);
                $formValues['previous_positions'] = is_array($decoded) ? $decoded : [];
            }
        }

        $data = [
            'section'            => $section,
            'sectionMeta'        => $meta,
            'title'              => $title,
            'url'                => $url,
            'addUrl'             => base_url($meta['base_route'] . '/add'),
            'formValues'         => $formValues,
            'designation'        => ['' => '- - - SELECT ROLE / DESIGNATION - - -'] + $designations,
            'districts'          => ['' => '- - - SELECT DISTRICT - - -'] + $this->getDistrictOptions(),
            'activeRole'         => $activeRole,
            'activeDistrict'     => $activeDistrict,
            'defaultDesignation' => $roleDefaultDesigId,
            'defaultPosition'    => $roleDefaultPos,
        ];

        return $this->load->view('admin/officeBearer/section_form', $data, TRUE);
    }

    protected function sectionFormValidation(string $section): array {
        $meta = $this->OfficeBearer_model->getSectionMeta($section);
        $allowedDesigs = $this->OfficeBearer_model->getDesignationsForSection($section);

        $this->form_validation->set_rules('name', 'Leader Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'Role / Designation', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('year', 'Year / Term', 'trim');

        if ($section === 'district') {
            $this->form_validation->set_rules('section_heading', 'District', 'trim|required');
        }

        $this->form_validation->set_error_delimiters("<p>", "</p>");

        $desigId = (int)$this->input->post('designation');
        $desigRow = $this->OfficeBearer_model->getDesignationById($desigId);

        $position = $this->input->post('position');
        if (!$position || !is_numeric($position)) {
            $position = 25;
            if ($desigRow) {
                $upperDesig = strtoupper(trim((string)$desigRow['name']));
                foreach ($meta['roles'] as $r) {
                    if (in_array($upperDesig, $r['match'], true)) {
                        $position = (int)$r['position'];
                        break;
                    }
                }
            }
        } else {
            $position = (int)$position;
        }

        $sectionHeading = trim((string)$this->input->post('section_heading'));
        if ($section === 'state' && $sectionHeading === '' && $desigId > 0) {
            $sectionHeading = $this->OfficeBearer_model->getDefaultSectionHeadingForDesignation($desigId);
        }

        $isPublishRaw = $this->input->post('is_publish');
        $isPublish = ($isPublishRaw === null || $isPublishRaw === '') ? 1 : (int)$isPublishRaw;

        $formValues = [
            'name'            => trim((string)$this->input->post('name')),
            'designation'     => $desigId,
            'level'           => $meta['level'],
            'section_heading' => $sectionHeading,
            'email'           => trim((string)$this->input->post('email')),
            'phone'           => $this->input->post('phone') ? trim((string)$this->input->post('phone')) : NULL,
            'is_publish'      => $isPublish,
            'position'        => $position,
            'year'            => trim((string)$this->input->post('year')),
            'is_former'       => $meta['is_former'],
            '_desig_allowed'  => isset($allowedDesigs[$desigId]),
        ];

        return $formValues;
    }

    public function sectionAdd(string $section = 'state') {
        $section = $this->normalizeSectionKey($section);
        $meta = $this->OfficeBearer_model->getSectionMeta($section);
        $activeRole = strtolower(trim((string)$this->input->post('active_role')));
        $activeDistrict = trim((string)$this->input->post('active_district'));
        $formValues = $this->sectionFormValidation($section);

        $customError = null;
        if ($this->form_validation->run() == FALSE) {
            $customError = null;
        } else if (!$formValues['_desig_allowed']) {
            $customError = 'Please select a valid role for ' . $meta['title'] . '.';
        } else if ($section === 'district') {
            $districts = $this->getDistrictOptions();
            if (!isset($districts[$formValues['section_heading']])) {
                $customError = 'Please select a valid district.';
            }
        }

        unset($formValues['_desig_allowed']);

        if ($this->form_validation->run() == FALSE || $customError !== null) {
            if ($customError !== null) {
                $formValues['error'] = $customError;
            }
            echo json_encode([
                'code' => 'error',
                'form' => $this->createSectionForm($section, base_url($meta['base_route'] . '/add'), $formValues, 'Add ' . $meta['title'], $activeRole, $activeDistrict),
            ]);
            exit;
        }

        if ($this->OfficeBearer_model->isSingleDesignation(
            $formValues['designation'],
            $meta['is_former'],
            $meta['level'],
            $section === 'district' ? $formValues['section_heading'] : null
        )) {
            $formValues['error'] = $section === 'district'
                ? 'This role already exists for ' . htmlspecialchars($formValues['section_heading']) . '. Please edit the existing record instead.'
                : 'This designation already has an active State Leader. Please update the existing record instead.';
            echo json_encode([
                'code' => 'error',
                'form' => $this->createSectionForm($section, base_url($meta['base_route'] . '/add'), $formValues, 'Add ' . $meta['title'], $activeRole, $activeDistrict),
            ]);
            exit;
        }

        $formValues['image'] = '';
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $formValues['error'] = $uploadData['error'];
                echo json_encode([
                    'code' => 'error',
                    'form' => $this->createSectionForm($section, base_url($meta['base_route'] . '/add'), $formValues, 'Add ' . $meta['title'], $activeRole, $activeDistrict),
                ]);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        }

        $addId = $this->OfficeBearer_model->add($formValues);
        if ($addId) {
            $districtFilter = ($section === 'district') ? trim((string)$this->input->get('district')) : null;
            echo json_encode([
                'code'       => 'success',
                'lastId'     => $addId,
                'content'    => $this->getSectionContent($section, $activeRole),
                'roleCounts' => $this->OfficeBearer_model->getSectionRoleCounts($section, $districtFilter ?: null),
            ]);
        } else {
            echo json_encode([
                'code' => 'error',
                'data' => 'Something went wrong! Please try again.',
            ]);
        }
        exit;
    }

    public function sectionEdit(string $section = 'state', $id = 0) {
        $section = $this->normalizeSectionKey($section);
        $meta = $this->OfficeBearer_model->getSectionMeta($section);
        $id = (int)$id;
        $formInfo = $this->OfficeBearer_model->getById($id);

        if (!$formInfo || !$this->rowMatchesSection($formInfo, $section)) {
            echo json_encode(['code' => 'error', 'message' => 'Record not found in this section.']);
            exit;
        }

        $activeRole = strtolower(trim((string)$this->input->get('role')));
        $url = base_url($meta['base_route'] . '/update/' . $id);
        echo json_encode([
            'code' => 'success',
            'form' => $this->createSectionForm($section, $url, $formInfo, 'Edit ' . $meta['title'], $activeRole),
        ]);
        exit;
    }

    public function sectionUpdate(string $section = 'state', $id = 0) {
        $section = $this->normalizeSectionKey($section);
        $meta = $this->OfficeBearer_model->getSectionMeta($section);
        $id = (int)$id;
        $url = base_url($meta['base_route'] . '/update/' . $id);
        $activeRole = strtolower(trim((string)$this->input->post('active_role')));
        $activeDistrict = trim((string)$this->input->post('active_district'));

        $formInfo = $this->OfficeBearer_model->getById($id);
        if (!$formInfo || !$this->rowMatchesSection($formInfo, $section)) {
            echo json_encode(['code' => 'error', 'message' => 'Record not found in this section.']);
            exit;
        }

        $formValues = $this->sectionFormValidation($section);
        $customError = null;
        if ($this->form_validation->run() == FALSE) {
            $customError = null;
        } else if (!$formValues['_desig_allowed']) {
            $customError = 'Please select a valid role for ' . $meta['title'] . '.';
        } else if ($section === 'district') {
            $districts = $this->getDistrictOptions();
            if (!isset($districts[$formValues['section_heading']])) {
                $customError = 'Please select a valid district.';
            }
        }
        unset($formValues['_desig_allowed']);

        if ($this->form_validation->run() == FALSE || $customError !== null) {
            if ($customError !== null) {
                $formValues['error'] = $customError;
            }
            $formValues['image'] = $formInfo['image'];
            echo json_encode([
                'code' => 'error',
                'form' => $this->createSectionForm($section, $url, $formValues, 'Edit ' . $meta['title'], $activeRole, $activeDistrict),
            ]);
            exit;
        }

        if ($this->OfficeBearer_model->isSingleDesignation(
            $formValues['designation'],
            $meta['is_former'],
            $meta['level'],
            $section === 'district' ? $formValues['section_heading'] : null,
            $id
        )) {
            $formValues['error'] = $section === 'district'
                ? 'This role already exists for ' . htmlspecialchars($formValues['section_heading']) . '.'
                : 'This designation already has another active State Leader.';
            $formValues['image'] = $formInfo['image'];
            echo json_encode([
                'code' => 'error',
                'form' => $this->createSectionForm($section, $url, $formValues, 'Edit ' . $meta['title'], $activeRole, $activeDistrict),
            ]);
            exit;
        }

        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $formValues['error'] = $uploadData['error'];
                $formValues['image'] = $formInfo['image'];
                echo json_encode([
                    'code' => 'error',
                    'form' => $this->createSectionForm($section, $url, $formValues, 'Edit ' . $meta['title'], $activeRole, $activeDistrict),
                ]);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        } else if ($this->input->post("w") > 0 && !empty($formInfo['image'])) {
            $this->createThumbnail($formInfo['image']);
        }

        $this->OfficeBearer_model->update($id, $formValues);
        $districtFilter = ($section === 'district') ? trim((string)$this->input->get('district')) : null;
        echo json_encode([
            'code'       => 'success',
            'lastId'     => $id,
            'content'    => $this->getSectionContent($section, $activeRole),
            'roleCounts' => $this->OfficeBearer_model->getSectionRoleCounts($section, $districtFilter ?: null),
            'message'    => 'Saved Successfully',
        ]);
        exit;
    }

    public function sectionPublish(string $section = 'state', $id = 0) {
        $section = $this->normalizeSectionKey($section);
        $id = (int)$id;
        $row = $this->OfficeBearer_model->getById($id);
        if ($id && $row && $this->rowMatchesSection($row, $section)) {
            $publish = ($row['is_publish'] == 1) ? 0 : 1;
            $this->OfficeBearer_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    public function sectionDelete(string $section = 'state', $id = 0) {
        $section = $this->normalizeSectionKey($section);
        $id = (int)$id;
        $row = $this->OfficeBearer_model->getById($id);
        $activeRole = strtolower(trim((string)$this->input->get('role')));
        $districtFilter = ($section === 'district') ? trim((string)$this->input->get('district')) : null;

        if ($row && $this->rowMatchesSection($row, $section) && $this->deleteOne($id)) {
            echo json_encode([
                'code'       => 'success',
                'lastId'     => $id,
                'content'    => $this->getSectionContent($section, $activeRole),
                'roleCounts' => $this->OfficeBearer_model->getSectionRoleCounts($section, $districtFilter ?: null),
            ]);
            exit;
        }
        echo json_encode(['code' => 'error', 'message' => 'Could not delete record.']);
        exit;
    }

    public function sectionBatchDelete(string $section = 'state') {
        $section = $this->normalizeSectionKey($section);
        $activeRole = strtolower(trim((string)$this->input->get('role')));
        $districtFilter = ($section === 'district') ? trim((string)$this->input->get('district')) : null;

        $result = $this->runBatchDelete(function ($id) use ($section) {
            $row = $this->OfficeBearer_model->getById((int)$id);
            if (!$row || !$this->rowMatchesSection($row, $section)) {
                return FALSE;
            }
            return $this->deleteOne((int)$id);
        });
        $result['content'] = $this->getSectionContent($section, $activeRole);
        $result['roleCounts'] = $this->OfficeBearer_model->getSectionRoleCounts($section, $districtFilter ?: null);
        echo json_encode($result);
        exit;
    }

    public function index() {

        $data['form'] = $this->createForm(base_url('admin/office_bearer/add'));
        $data['content'] = $this->getContent();

        $data['designations'] = $this->OfficeBearer_model->getAllDesignation();
        $data['section_headings'] = $this->OfficeBearer_model->getAllSectionHeadings();
        $data['districts'] = [
            'Thiruvananthapuram' => 'Thiruvananthapuram',
            'Kollam' => 'Kollam',
            'Pathanamthitta' => 'Pathanamthitta',
            'Alappuzha' => 'Alappuzha',
            'Kottayam' => 'Kottayam',
            'Idukki' => 'Idukki',
            'Ernakulam' => 'Ernakulam',
            'Thrissur' => 'Thrissur',
            'Palakkad' => 'Palakkad',
            'Malappuram' => 'Malappuram',
            'Kozhikode' => 'Kozhikode',
            'Wayanad' => 'Wayanad',
            'Kannur' => 'Kannur',
            'Kasaragod' => 'Kasaragod'
        ];

        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $header['special_css'] = ['plugins/select2/select2.min.css', 'css/imgareaselect-default.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/jquery.imgareaselect.min.js', 'js/officeBearer.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/officeBearer/officeBearer', $data);
        $this->load->view('admin/footer', $footer);
    }

    function getContent($param = array()) {

        $this->load->library("pagination");

        $param['search'] = trim((string)$this->input->get('search'));
        $param['designation'] = $this->input->get('designation');
        $param['level'] = $this->input->get('level');
        $param['district'] = $this->input->get('district');
        $param['is_former'] = $this->input->get('is_former');
        $param['is_publish'] = $this->input->get('is_publish');
        $param['sort'] = $this->input->get('sort') ? $this->input->get('sort') : 'position-asc';

        $param['limit'] = 15;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //PAGINATION CONFIGS
        $config["total_rows"] = $this->OfficeBearer_model->getAllCount($param);
        $config["base_url"] = base_url('admin/office_bearer');
        $config["per_page"] = $param['limit'];

        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        //view table bottom-left info
        $config["from"] = $param['offset'] + 1;
        $temp = $param['limit'] + $param['offset'];
        $config["to"] = $temp <= $config["total_rows"] ? $temp : $config["total_rows"];
        $content['config'] = $config;

        $this->newUrl = $this->getNewUrl([
            'page' => $param['page'],
            'search' => $param['search'],
            'designation' => $param['designation'],
            'level' => $param['level'],
            'district' => $param['district'],
            'is_former' => $param['is_former'],
            'is_publish' => $param['is_publish'],
            'sort' => $param['sort']
        ]);

        $content['content'] = $this->OfficeBearer_model->getAll($param);
        $content['is_former_filter'] = isset($param['is_former']) ? $param['is_former'] : '';
        return $this->load->view('admin/officeBearer/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/office_bearer/add');
        
        if ($formValues && isset($formValues['previous_positions']) && is_string($formValues['previous_positions'])) {
            $decoded = json_decode($formValues['previous_positions'], true);
            $formValues['previous_positions'] = is_array($decoded) ? $decoded : [];
        }
        $data['formValues'] = $formValues;
        
        $data['designation'] = $this->OfficeBearer_model->getAllDesignation();
        $data['designation'] = ['' => '- - - SELECT DESIGNATION - - -'] + $data['designation'];

        $data['section_headings'] = $this->OfficeBearer_model->getAllSectionHeadings();
        $data['section_headings'] = ['' => '- - - SELECT OR TYPE SECTION HEADING - - -'] + $data['section_headings'];
        
        $data['category_levels'] = [
            'State' => 'State Level',
            'District' => 'District Level',
            'Educational District' => 'Educational District Level',
            'Sub District' => 'Sub District Level'
        ];

        $data['districts'] = [
            '' => '- - - SELECT DISTRICT - - -',
            'Thiruvananthapuram' => 'Thiruvananthapuram',
            'Kollam' => 'Kollam',
            'Pathanamthitta' => 'Pathanamthitta',
            'Alappuzha' => 'Alappuzha',
            'Kottayam' => 'Kottayam',
            'Idukki' => 'Idukki',
            'Ernakulam' => 'Ernakulam',
            'Thrissur' => 'Thrissur',
            'Palakkad' => 'Palakkad',
            'Malappuram' => 'Malappuram',
            'Kozhikode' => 'Kozhikode',
            'Wayanad' => 'Wayanad',
            'Kannur' => 'Kannur',
            'Kasaragod' => 'Kasaragod'
        ];

        return $this->load->view('admin/officeBearer/form', $data, TRUE);
    }

    public function add() {
        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $desigInput = $formValues['designation'];
        $formValues['designation'] = $this->OfficeBearer_model->getOrAddDesignation($formValues['designation']);
        $desigRow = $this->OfficeBearer_model->getDesignationById($formValues['designation']);
        $resolvedDesigName = $desigRow ? $desigRow['name'] : $desigInput;

        // Check if name and phone match an existing person (no duplicate entries for same person)
        $cleanedName = trim((string)$formValues['name']);
        $cleanedPhone = trim((string)$formValues['phone']);
        $existingPerson = null;
        if (!empty($cleanedName) && !empty($cleanedPhone)) {
            $existingPerson = $this->OfficeBearer_model->findPersonByNameAndPhone($cleanedName, $cleanedPhone);
        }

        if ($existingPerson) {
            $imageName = '';
            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                $uploadData = $this->uploadImage();
                if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                    $data['code'] = 'error';
                    $formValues['error'] = $uploadData['error'];
                    $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
                    echo json_encode($data);
                    exit;
                }
                $imageName = $uploadData['file_name'];
            }

            // Positions to merge into existing person:
            $submittedPositions = array();
            $submittedPositions[] = [
                'designation' => $resolvedDesigName,
                'year' => $formValues['year'],
                'level' => $formValues['level'],
                'section_heading' => $formValues['section_heading'],
                'position' => isset($formValues['position']) ? (int)$formValues['position'] : 25
            ];

            if (!empty($formValues['previous_positions'])) {
                foreach ($formValues['previous_positions'] as $pp) {
                    $submittedPositions[] = $pp;
                }
            }

            $extra = array();
            if (!empty($imageName)) {
                $extra['image'] = $imageName;
            }
            if (!empty($formValues['email'])) {
                $extra['email'] = $formValues['email'];
            }
            if (isset($formValues['is_former'])) {
                $extra['is_former'] = $formValues['is_former'];
            }

            $this->OfficeBearer_model->mergePositions($existingPerson['id'], $submittedPositions, $extra);

            $data['code'] = 'success';
            $data['lastId'] = $existingPerson['id'];
            $data['message'] = "Leader '" . htmlspecialchars($cleanedName) . "' already exists. The position(s) were successfully added to their profile without creating a duplicate record.";
            $data['content'] = $this->getContent();
            echo json_encode($data);
            exit;
        }

        //check whether the post is single person or multiple
        if ($this->OfficeBearer_model->isSingleDesignation($formValues['designation'], $formValues['is_former'])) {
            $data['code'] = 'error';
            $formValues['error'] = 'This designation already exists. Please update the existing record';
            $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $formValues['image'] = '';
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadData['error'];
                $data['form'] = $this->createForm(base_url('admin/office_bearer/add'), $formValues);
                echo json_encode($data);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        }
        $add = $this->OfficeBearer_model->add($formValues);
        if ($add) {
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
        } else {
            $data['code'] = 'error';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function uploadImage() {
        if (!is_dir(OFFICE_BEARER)) {
            mkdir(OFFICE_BEARER, 0777, true);
        }

        $config['upload_path'] = './' . OFFICE_BEARER;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 0;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload');
            $this->upload->initialize($config);
        
        if (!$this->upload->do_upload('image')) {
            $data['code'] = 'error';
            $data['error'] = $this->upload->display_errors('', '');
            return $data;
        } else {
            $uploadData = $this->upload->data();
            $this->createThumbnail($uploadData['file_name']);
            $uploadData['code'] = 'success';
            return $uploadData;
        }
    }

    function createThumbnail($fileName) {
        $this->load->library('image_functions');
        $x1 = (float)$this->input->post("x1");
        $y1 = (float)$this->input->post("y1");
        $x2 = (float)$this->input->post("x2"); // displayed width
        $y2 = (float)$this->input->post("y2"); // displayed height
        $w = (float)$this->input->post("w");   // selection width
        $h = (float)$this->input->post("h");   // selection height

        $thumb_image_location = OFFICE_BEARER . '/' . $fileName;
        $large_image_location = OFFICE_BEARER . '/' . $fileName;

        $width = $this->image_functions->getWidth($large_image_location);
        $height = $this->image_functions->getHeight($large_image_location);

        if (!$width || !$height) {
            return;
        }

        if (!$x2 || $x2 <= 0) $x2 = $width;
        if (!$y2 || $y2 <= 0) $y2 = $height;

        if (!$w || $w <= 0 || !$h || $h <= 0) {
            $w = $x2;
            $h = $x2 * ($this->imageHeightThumb / $this->imageWidthThumb);
            if ($h > $y2) {
                $h = $y2;
                $w = $y2 * ($this->imageWidthThumb / $this->imageHeightThumb);
            }
            $x1 = max(0, ($x2 - $w) / 2);
            $y1 = max(0, ($y2 - $h) / 2);
        }

        // Map selection coordinates proportionally from display scale to original image dimensions
        $scaleX = $width / $x2;
        $scaleY = $height / $y2;

        $origCropX = max(0, min($width - 1, round($x1 * $scaleX)));
        $origCropY = max(0, min($height - 1, round($y1 * $scaleY)));
        $origCropW = max(1, min($width - $origCropX, round($w * $scaleX)));
        $origCropH = max(1, min($height - $origCropY, round($h * $scaleY)));

        $thumb_width = $this->imageWidthThumb;
        $scale = $thumb_width / $origCropW;
        $this->image_functions->resizeThumbnailImage($thumb_image_location, $large_image_location, $origCropW, $origCropH, $origCropX, $origCropY, $scale);
    }

    function formValidation() {


        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'Designation', 'trim|required');
        $this->form_validation->set_rules('section_heading', 'Section Heading', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'name' => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'level' => $this->input->post('level') ? $this->input->post('level') : 'State',
            'section_heading' => $this->input->post('section_heading'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone') ? $this->input->post('phone') : NULL,
            'is_publish' => $this->input->post('is_publish'),
            'position' => $this->input->post('position') ? $this->input->post('position') : 100,
            'year' => $this->input->post('year'),
            'is_former' => $this->input->post('is_former') ? $this->input->post('is_former') : 0
        ];

        $prevPositions = $this->input->post('previous_positions');
        $cleanedPositions = array();
        if (!empty($prevPositions) && is_array($prevPositions)) {
            foreach ($prevPositions as $pos) {
                $pDesig = isset($pos['designation']) ? trim((string)$pos['designation']) : '';
                $pYear = isset($pos['year']) ? trim((string)$pos['year']) : '';
                $pLevel = isset($pos['level']) ? trim((string)$pos['level']) : 'State';
                $pSec = isset($pos['section_heading']) ? trim((string)$pos['section_heading']) : '';

                if (!empty($pDesig) || !empty($pYear)) {
                    $cleanedPositions[] = [
                        'designation' => $pDesig,
                        'year' => $pYear,
                        'level' => $pLevel,
                        'section_heading' => $pSec,
                        'position' => isset($pos['position']) && is_numeric($pos['position']) ? (int)$pos['position'] : 25,
                        'is_enabled' => isset($pos['is_enabled']) ? (int)$pos['is_enabled'] : 1
                    ];
                }
            }
        }
        $formValues['previous_positions'] = $cleanedPositions;

        return $formValues;
    }

    public function edit() {
        $data['code'] = 'error';

        $id = $this->uri->segment(4);
        $formInfo = $this->OfficeBearer_model->getById($id);
        if ($formInfo) {
            if (!empty($formInfo['previous_positions']) && is_string($formInfo['previous_positions'])) {
                $decoded = json_decode($formInfo['previous_positions'], true);
                $formInfo['previous_positions'] = is_array($decoded) ? $decoded : [];
            }
            $url = base_url('admin/office_bearer/update/' . $id);
            $data['form'] = $this->createForm($url, $formInfo, 'Edit Form');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    public function check_person() {
        $name = trim((string)$this->input->get('name'));
        $phone = trim((string)$this->input->get('phone'));
        $excludeId = $this->input->get('exclude_id');

        if (empty($name) || empty($phone)) {
            echo json_encode(['exists' => false]);
            exit;
        }

        $person = $this->OfficeBearer_model->findPersonByNameAndPhone($name, $phone, $excludeId);
        if ($person) {
            $prev = array();
            if (!empty($person['previous_positions'])) {
                $decoded = json_decode($person['previous_positions'], true);
                if (is_array($decoded)) {
                    $prev = $decoded;
                }
            }
            echo json_encode([
                'exists' => true,
                'person' => [
                    'id' => $person['id'],
                    'name' => $person['name'],
                    'phone' => $person['phone'],
                    'email' => $person['email'],
                    'designation' => $person['designation_name'],
                    'year' => $person['year'],
                    'level' => $person['level'],
                    'section_heading' => $person['section_heading'],
                    'is_former' => $person['is_former'],
                    'previous_positions' => $prev
                ]
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
        exit;
    }

    public function update() {

        $formValues = $this->formValidation();

        $id = $this->uri->segment(4);
        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('admin/office_bearer/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit');
            echo json_encode($data);
            exit;
        }

        $formValues['designation'] = $this->OfficeBearer_model->getOrAddDesignation($formValues['designation']);

        $formInfo = $this->OfficeBearer_model->getById($id);
        if (!$formInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }

        //check whether  image is change while editing, then upload new image
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $uploadData = $this->uploadImage();
            if (isset($uploadData['code']) && $uploadData['code'] === 'error') {
                $data['code'] = 'error';
                $formValues['error'] = $uploadData['error'];
                $url = base_url('admin/office_bearer/update/' . $id);
                $data['form'] = $this->createForm($url, $formValues, 'Edit');
                echo json_encode($data);
                exit;
            }
            $formValues['image'] = $uploadData['file_name'];
        } else if ($this->input->post("w") > 0) {
            $this->createThumbnail($formInfo['image']);
        }

        //update 
        $this->OfficeBearer_model->update($id, $formValues);
        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    public function publish() {
        $id = $this->uri->segment(4);
        $orderInfo = $this->OfficeBearer_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->OfficeBearer_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        if ($this->deleteOne($id)) {
            $data['content'] = $this->getContent();
            $data['code'] = 'success';
            $data['lastId'] = $id;
        }
        echo json_encode($data);
        exit;
    }

    /* ------------------------------------------------------------------
     * Designations
     *
     * Designations are created on the fly when an office bearer is saved,
     * so without this screen a typo could never be corrected - and the
     * designation is what groups people into sections on the public page.
     * Deleting is deliberately not offered: office_bearer rows reference
     * these by id and would be left pointing at nothing.
     * ------------------------------------------------------------------ */

    public function designation() {
        $data['form'] = $this->createDesignationForm(base_url('admin/office_bearer/designation/add'));
        $data['content'] = $this->getDesignationContent();

        if ($this->input->is_ajax_request()) {
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        $header['page_title'] = 'Designations';

        $this->load->view('admin/header', $header);
        $this->load->view('admin/officeBearer/designation/category', $data);
        $this->load->view('admin/footer');
    }

    public function getDesignationContent() {
        $content['categories'] = $this->OfficeBearer_model->getAllDesignation();
        return $this->load->view('admin/officeBearer/designation/content', $content, TRUE);
    }

    public function createDesignationForm($url, $formValues = false, $title = "Add Designation") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('admin/office_bearer/designation/add');
        $data['formValues'] = $formValues;

        return $this->load->view('admin/officeBearer/designation/form', $data, TRUE);
    }

    public function designationAdd() {
        $this->form_validation->set_rules('name', 'Designation Name', 'trim|required');
        $formValues = array('name' => trim((string) $this->input->post('name')));

        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $data['form'] = $this->createDesignationForm(base_url('admin/office_bearer/designation/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        if ($this->OfficeBearer_model->designationExists($formValues['name'])) {
            $formValues['error'] = 'This designation already exists.';
            $data['code'] = 'error';
            $data['form'] = $this->createDesignationForm(base_url('admin/office_bearer/designation/add'), $formValues);
            echo json_encode($data);
            exit;
        }

        $id = $this->OfficeBearer_model->addDesignation($formValues['name']);
        $data['code'] = $id ? 'success' : 'error';
        $data['lastId'] = $id;
        $data['content'] = $this->getDesignationContent();

        echo json_encode($data);
        exit;
    }

    public function designationEdit($id = false) {
        $data['code'] = 'error';

        $formInfo = $this->OfficeBearer_model->getDesignationById($id);
        if ($formInfo) {
            $data['form'] = $this->createDesignationForm(
                    base_url('admin/office_bearer/designation/update/' . $id), $formInfo, 'Edit Designation');
            $data['code'] = 'success';
        }

        echo json_encode($data);
        exit;
    }

    public function designationUpdate($id = false) {
        $url = base_url('admin/office_bearer/designation/update/' . $id);

        $this->form_validation->set_rules('name', 'Designation Name', 'trim|required');
        $formValues = array('name' => trim((string) $this->input->post('name')));

        $formInfo = $this->OfficeBearer_model->getDesignationById($id);
        if (!$formInfo) {
            $formValues['error'] = 'Record not found.';
        } else if ($this->OfficeBearer_model->designationExists($formValues['name'], $id)) {
            $formValues['error'] = 'This designation already exists.';
        }

        if ($this->form_validation->run() == FALSE || isset($formValues['error'])) {
            $data['code'] = 'error';
            $data['form'] = $this->createDesignationForm($url, $formValues, 'Edit Designation');
            echo json_encode($data);
            exit;
        }

        $this->OfficeBearer_model->updateDesignation($id, $formValues['name']);

        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getDesignationContent();

        echo json_encode($data);
        exit;
    }


    /**
     * Remove one row plus its photo. Shared by the row Delete button and the
     * "Delete Selected" toolbar action.
     */
    protected function deleteOne($id) {
        $info = $this->OfficeBearer_model->getById($id);
        if (!$info || !$this->OfficeBearer_model->delete($id)) {
            return FALSE;
        }
        $this->deleteFile(OFFICE_BEARER . '/' . $info['image']);
        return TRUE;
    }

    public function batchDelete() {
        $result = $this->runBatchDelete(function ($id) {
            return $this->deleteOne($id);
        });
        $result['content'] = $this->getContent();
        echo json_encode($result);
        exit;
    }

}
