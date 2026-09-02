<?php

/**
 */
Class Teacher extends Membership_Controller {

    const DOWNLOAD_CSV = 'csv';
    const DOWNLOAD_PDF = 'pdf';

    public function __construct() {
        parent::__construct();
// Load form helper library
        $this->load->helper(array('form', 'url', 'file'));
// Load form validation library
        $this->load->library('form_validation');
        // Pdf extends TCPDF; only the PDF-export methods instantiate it
        // (new Pdf()). Include the class here without instantiating so
        // ordinary teacher pages don't spin up TCPDF at all.
        require_once APPPATH . 'libraries/Pdf.php';
        /* loding model */



        $this->load->model("membership/Teacher_designation_model", "Teacher_designation_model");
        $this->load->model("membership/Branch_model", "Branch_model");
        $this->load->model("membership/School_model", "School_model");
        $this->load->model("membership/Teacher_model", "Teacher_model");
        $this->load->model("membership/AauthGroup_model", "aauthGroup_model");
    }

    public function index() {
        $data = array();


        $data['content'] = $this->getContent();

//check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }

        //$data['search'] = trim((string)$this->input->get('search'));
        $data['configYear'] = $this->year;
        $data['year'] = $this->input->get('year') ? $this->input->get('year') : $this->year;
//year array

        $data['reg'] = array();
        $office = $this->getOffice($this->aauthGroupId + 1, TRUE, false);
        if (isset($office['officeSelect']) && is_array($office['officeSelect'])) {
            unset($office['officeSelect'][0]);
            $data['reg']['officeSelect'] = $office['officeSelect'];
            $data['reg']['officeLabel'] = $office['officeLabel'];
        }

        $this->load->model("membership/Config_model", "config_model");
        $data['enable_entry'] = $this->config_model->getLabelValue('enable_entry');

        $data['form'] = $this->createForm(base_url('membership/teacher/add'));

        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js', 'js/teacher_details.js', 'js/membership.js'];

        $this->load->view('membership/header', $header);
        $this->load->view('membership/teacher/teacher', $data);
        $this->load->view('membership/footer', $footer);
    }

    function getContent($getGroupIdDefault = false, $getOfficeIdDefault = false) {
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId($getGroupIdDefault, $getOfficeIdDefault);

        $content['groupId'] = $param['groupId'] = $getGroupId;
        $content['officeId'] = $param['officeId'] = $getofficeId;

        $param['year'] = $this->input->get('year') ? $this->input->get('year') : $this->year;

        $content['yearConfig'] = $this->year;
        $content['year'] = $param['year'];

        $this->load->library("pagination");

        $param['search'] = $content['search'] = trim((string)$this->input->get('search'));
        $param['category'] = trim((string)$this->input->get('category'));
        $param['view'] = $content["view"] = $this->input->get('view') ? trim((string)$this->input->get('view')) : 1;
        if (($content['yearConfig'] != $content['year']) && $param['view'] == 1) {
            $param['view'] = 2;
        }
        $content['view'] = $param['view'];

        $param['limit'] = $this->input->get('limit') ? $this->input->get('limit') : 20;
        $content['limit'] = $param['limit'];

        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;

        //This logic used to handle redirection from home page membership table
        if ($getGroupId > self::AAUTH_GROUP_SCHOOL && $getofficeId) {
            $param['id'] = $getofficeId;
        }

//PAGINATION CONFIGS
        $config["total_rows"] = $this->Teacher_model->getAllCount($param);
//        $config["base_url"] = base_url() . "membership/teacher";
        $config["base_url"] = $this->getNewUrl(
                [
            'group' => $getGroupId,
            'office' => $getofficeId,
            'view' => $param['view']
                ], ['page']
        );
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
            'view' => $param['view']
        ]);

        $content['orders'] = $this->Teacher_model->getAll($param);
        $content['count'] = $this->Teacher_model->getConsolidatedCount($param);
        return $this->load->view('membership/teacher/content', $content, TRUE);
    }

    public function createForm($url, $formValues = false, $title = "Add") {
        $data['title'] = $title;
        $data['url'] = $url;
        $data['addUrl'] = base_url('membership/teacher/add');
        $data['formValues'] = $formValues;
        $data['officeSelect'] = ['' => '- - - SELECT GROUP - - -'];
        $data['officeSearch'] = array();


        $data['branchSelect'] = $this->Branch_model->getAll();
        if (is_array($data['branchSelect'])) {
            $data['branchSelect'] = $this->createSelectDropDown($data['branchSelect'], 'name', '* * SELECT BRANCH * *');
        }
        $param["officeId"] = isset($formValues["branch"]) ? $formValues["branch"] : "";
        $data['schoolSelect'] = $this->createSelectDropDown($this->School_model->getAll($param), 'name', '* * SELECT SCHOOL * *');
        $data['designationSelect'] = $this->createSelectDropDown($this->Teacher_designation_model->getAll(), 'name', '* * SELECT DESIGNATION * *');

        $group = $this->aauthGroup_model->getAllGroup(false, false, [7]);

        $searchGroupId = $this->input->get('group') ? $this->input->get('group') : false;
        if ($searchGroupId && (($searchGroupId ) > $this->aauthGroupId)) {
            $office = $this->getOffice($searchGroupId, TRUE, false);
            $data['officeSearch'] = $office['officeSelect'];
        }

        $data['groupSearch'] = $this->createSelectDropDown($group, 'name', 'ALL');

        return $this->load->view('membership/teacher/form', $data, TRUE);
    }

    /*
     * This function for Insert latest news
     * @return json_encode response
     */

    public function add() {

        $this->load->model("membership/Config_model", "config_model");
        if (!$this->config_model->getLabelValue('enable_entry')) {
            return false;
        }

        $formValues = $this->formValidation();

//validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $url = base_url('membership/teacher/add/');
            $data['form'] = $this->createForm($url, $formValues);
            echo json_encode($data);
            exit;
        }

//Insert values
        $add = $this->Teacher_model->add($formValues);
        if ($add) {
            $entry[] = [
                "teacher_id" => $add,
                "year" => $this->year,
                "school_id" => $formValues["school"],
                "designation_id" => $formValues["designation"]
            ];
            $this->Teacher_model->newProcessEntry($entry);
            $data['code'] = 'success';
            $data['lastId'] = $add;
            $data['content'] = $this->getContent();
        } else {
            $data['code'] = 'success';
            $data['data'] = 'Something went wrong! Pls try again';
        }
        echo json_encode($data);
        exit;
    }

    function formValidation() {
//        if (static::AAUTH_GROUP_BRANCH != $this->aauthGroupId) {
//            $branchId = $this->input->post('branch');
//        }
        $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
        $this->form_validation->set_rules('school', 'School', 'trim|required|is_natural_no_zero');

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'Designation', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('mobile', 'Phone no', 'trim');
        $this->form_validation->set_rules('teacher_type', 'Phone no', 'trim|required');

        $this->form_validation->set_message('is_natural_no_zero', 'The %s is required');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
//set all form values into array
        $formValues = [
            'name' => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'mobile' => $this->input->post('mobile'),
            'school' => $this->input->post('school'),
            'teacher_type' => $this->input->post('teacher_type'),
            'adhyapaka_sabdham_subscriber' => $this->input->post('adhyapaka_sabdham'),
        ];



        return $formValues;
    }

    public function addBulk() {

        $this->load->model("membership/Config_model", "config_model");
        if (!$this->config_model->getLabelValue('enable_entry')) {
            return false;
        }


        $data = array();
        $branchId = $this->aauthOfficeId;

//check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }



        $data['branchSelect'] = $this->Branch_model->getAll();
        if (is_array($data['branchSelect'])) {
            $data['branchSelect'] = $this->createSelectDropDown($data['branchSelect'], 'name', '* * SELECT BRANCH * *');
        }
        $data['schoolSelect'] = $this->createSelectDropDown($this->School_model->getAll(), 'name', '* * SELECT SCHOOL * *');
        $data['designationSelect'] = $this->createSelectDropDown($this->Teacher_designation_model->getAll(), 'name', '* * SELECT DESIGNATION * *');

        if (static::AAUTH_GROUP_BRANCH != $this->aauthGroupId) {
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $branchId = $this->input->post('branch');
        }
        $this->form_validation->set_rules('school', 'School', 'trim|required|is_natural_no_zero');



        $data['branch'] = $this->input->post('branch');
        $data['school'] = $this->input->post('school');
        $data['name'] = $this->input->post('name[]');
        $data['designation'] = $this->input->post('designation[]');
        $data['mobile'] = $this->input->post('mobile[]');
        $data['teacher_type'] = $this->input->post('teacher_type[]');
        $data['adhyapaka_sabdham'] = $this->input->post('adhyapaka_sabdham[]');

        if (!is_array($data['name'])) {
            $data['name'] = $data['designation'] = $data['mobile'] = array('');
        }

        $insertData = array();
        foreach ($data['name'] as $key => $row) {
            $this->form_validation->set_rules('name[' . $key . ']', 'Name', 'trim|required');
            $this->form_validation->set_rules('designation[' . $key . ']', 'Designation', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('mobile[' . $key . ']', 'Phone no', 'trim');
            $this->form_validation->set_rules('teacher_type[' . $key . ']', 'Teacher type', 'trim|required|is_natural_no_zero');


            $insertData[] = array(
                'school_id' => $this->input->post('school'),
                'name' => $data['name'][$key],
                'designation_id' => $data['designation'][$key],
                'mobile' => $data['mobile'][$key],
                'teacher_type' => $data['teacher_type'][$key],
                'created_at' => date("Y-m-d H:i:s"),
                'adhyapaka_sabdham_subscriber' => isset($data['adhyapaka_sabdham'][$key]) ? 1 : 0,
                'created_by' => $this->session->userdata('id'),
                'updated_by' => $this->session->userdata('id')
            );
        }

        $this->form_validation->set_message('is_natural_no_zero', 'The %s is required');




        if ($this->form_validation->run() == TRUE) {
            $insert = $this->Teacher_model->insertBatch($insertData);
            if ($insert) {
                if (count($insertData) > 1) {
                    $lastId = $insert + (count($insertData) - 1);
                } else {
                    $lastId = $insert;
                }
                $range = range($insert, $lastId);

                $batchProcess = [];
                foreach ($range as $key => $val) {
                    $tempData = $insertData[$key];
                    $batchProcess[] = [
                        "teacher_id" => $val,
                        "year" => $this->year,
                        "school_id" => $tempData["school_id"],
                        "designation_id" => $tempData["designation_id"]
                    ];
                }

                $this->Teacher_model->newProcessEntry($batchProcess);
                redirect('membership/teacher');
            }
        }



        $header['special_css'] = ['plugins/select2/select2.min.css'];
        $footer['special_js'] = ['plugins/select2/select2.full.min.js'];

        $this->load->view('membership/header', $header);
        $this->load->view('membership/teacher/addBulk', $data);
        $this->load->view('membership/footer', $footer);
    }

    /*
     * It create html form For editing 
     * @return json_endcode  data
     */

    public function edit() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['id'] = $id;
        $param['year'] = $this->input->get("year");

        $formInfo = $this->Teacher_model->getById($param);

        if ($formInfo['is_confirmed'] == 1) {
            echo json_encode($data);
            exit;
        }

        if ($formInfo) {
            $formInfo['designation'] = $formInfo['designation_id'];
            $formInfo['school'] = $formInfo['school_id'];

            $url = base_url('membership/teacher/update/' . $id);

            $data['form'] = $this->createForm($url, $formInfo, 'Edit Details');
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Update News info
     * @return json_endcode  data
     */
    public function update() {

        $formValues = $this->formValidation();

        $id = $this->uri->segment(4);
//validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $url = base_url('membership/teacher/update/' . $id);
            $data['code'] = 'error';
            $data['form'] = $this->createForm($url, $formValues, 'Edit Details');
            echo json_encode($data);
            exit;
        }
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['id'] = $id;
        $param["year"] = $this->input->get("year");
        $orderInfo = $this->Teacher_model->getById($param);
        if ($orderInfo['is_confirmed'] == 1) {
            $data['code'] = 'error';
            $data['code'] = 'Confirmed Application cant edit';
            echo json_encode($data);
            exit;
        }
        if (!$orderInfo) {
            $data['code'] = 'error';
            $data['code'] = 'Wrong Edit';
            echo json_encode($data);
            exit;
        }


//update news
        $this->Teacher_model->update($id, $formValues);

        $entry = [
            "teacher_id" => $id,
            "year" => $this->year,
            "school_id" => $formValues["school"],
            "designation_id" => $formValues["designation"]
        ];
        $this->Teacher_model->updateProcessEntry($entry);


        $data['code'] = 'success';
        $data['lastId'] = $id;
        $data['content'] = $this->getContent();
        $data['message'] = 'Saved Successfully';

        echo json_encode($data);
        exit;
    }

    /**
     * Update Publish status
     * @return json_endcode  data
     */
    public function publish() {
        $id = $this->getSegment5();
        $orderInfo = $this->Teacher_model->getById($id);
        if ($id && $orderInfo) {
            $publish = ($orderInfo['is_publish'] == 1 ) ? 0 : 1;
            $this->Teacher_model->publish($id, $publish);
            echo true;
            exit;
        }
        echo false;
        exit;
    }

    /**
     * Update Publish status
     * @return json_endcode  data
     */
    public function delete() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $ids = $this->input->get('ids') ? $this->input->get('ids') : $id;
        $param['id'] = explode(',', $ids);
        
        $param['year'] = $this->input->get("year");
        //$info = $this->Teacher_model->getById($param);
        $members = $this->Teacher_model->getAll($param);
        
        if ($param['year'] != $this->year) {
            echo json_encode($data);
            exit;
        }

        $delete = $this->Teacher_model->delete($members, $param['year']);
        if ($delete) {
            $data['code'] = 'success';
            $data['content'] = $this->getContent();
        }
        echo json_encode($data);
        exit;
    }

    function download() {

        $type = $this->input->post('_type');

        if ($type == self::DOWNLOAD_CSV) {
            $this->generateCsv($data);
        } else {
            $data['fileName'] = 'Members_list_' . $this->aauthGroupId . '_' . $this->aauthOfficeId . '.pdf';
            $this->generatePdf($data);
        }
        unset($data['data']);
        $data['filePath'] = base_url(FILE_UPLOAD_PATH_TEMP);

        $mime = get_mime_by_extension($data['filePath']);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($data['filePath'])) . ' GMT');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime); // Add the mime type from Code igniter. 
        header('Content-Disposition: inline; filename="' . basename($data['fileName']) . '"'); // Add the file name 
        header('Content-Transfer-Encoding: binary');
        header('Transfer-Encoding: chunked');
//        header('Content-Length: ' . filesize($data['filePath'])); // provide file size 
        header('Connection: close');
        readfile($data['filePath']); // push it out 
        exit();
    }

    function generatePdf($data = array()) {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['view'] = trim((string)$this->input->get('view'));
        $param['year'] = trim((string)$this->input->get('year'));
        if (empty($param["year"])) {
            $param["year"] = $this->year;
        }

        $data['data'] = $this->Teacher_model->getAll($param);
//        $data['count'] = $this->Teacher_model->getConsolidatedCount($param);
//        $data['designatioCount'] = $this->Teacher_model->getDesignationCount($param);
//        $data['schoolCount'] = $this->School_model->getSchoolTypeCount($param);
        ob_start();
        $data['fileName'] = 'Members_list_' . date('Y-m-d') . '.pdf';
//        $this->load->view('membership/teacher/download/pdf', $data, TRUE);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->viewName = $this->getViewName($param['view']);
        if ($pdf->viewName) {
            $pdf->SetTitle('MEMBERS REPORT [' . $pdf->viewName . ']');
        } else {
            $pdf->SetTitle('MEMBERS REPORT');
        }

        $pdf->SetHeaderMargin(10);
        $pdf->SetTopMargin(25);
        $pdf->setFooterMargin(15);
        $pdf->SetAutoPageBreak(true);
        $pdf->SetAuthor('Author');
        $pdf->SetDisplayMode('real', 'default');

//set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 11);

        $pdf->Ln(2);

        if ($pdf->viewName) {
            $pdf->Cell(180, 5, 'MEMBERSHIP REPORT-' . $param["year"] . ' (' . $pdf->viewName . ')', '', 1, 'C');
        } else {
            $pdf->Cell(180, 5, 'MEMBERSHIP REPORT-' . $param["year"], '', 1, 'C');
        }

        $pdf->Cell(180, 5, $this->getTitleSub($param), '', 1, 'C');

        $titleSub = false;
        if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
            $titleSub = $this->session->userdata("officeName") . ' District';
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
            $titleSub = $this->session->userdata("officeName") . ' Education District';
        } else if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
            $titleSub = $this->session->userdata("officeName") . ' Sub District';
        }

        if ($titleSub) {
            $pdf->Cell(180, 5, $titleSub, '', 1, 'C');
        }
        $pdf->Ln();

// column titles
        $header = array('Sl.No', 'Name', 'Design.', 'Phone', 'School', 'Branch', 'Type', 'AS Sub');
        $headerWidth = array(7, 22, 16, 12, 18, 14, 6, 5);

// Colors, line width and bold font
        $pdf->SetFillColor(249, 249, 249);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.1);
        $pdf->SetFont('', ' ', '10');
// Header
        $w = array(30, 30, 40, 32, 27, 31);
        $num_headers = count($header);
//        for ($i = 0; $i < $num_headers; ++$i) {
//            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
//        }
//        $pdf->Ln();
// Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('', '', '9');
// Data
        $fill = 0;
        $subDistId = 0;
//        foreach ($data['data'] as $row) {
//            if ($subDistId != $row['subDistId']) {
//                $pdf->Cell(190, 6, $row['subDist'], 1, 1, 'C');
//                $subDistId = $row['subDistId'];
//            }
//            $pdf->Cell($w[0], 6, $row['name'], 'LR', 0, 'L');
//            $pdf->Cell($w[1], 6, $row['designation'], 'LR', 0, 'L');
//            $pdf->Cell($w[2], 6, $row['mobile'], 'LR', 0, 'L');
//            $pdf->Cell($w[3], 6, $row['school'], 'LR', 0, 'L');
//            $pdf->Cell($w[4], 6, $row['branch'], 'LR', 0, 'L');
//            if ($row['adhyapaka_sabdham_subscriber'] == 1) {
//                $value = "Yes";
//            } else {
//                $value = "No";
//            }
////            $pdf->Cell($w[5], 6, $value, 'LR', 0, 'C');
//            $pdf->Ln();
//            $fill = !$fill;
//        }
//        $pdf->Cell(array_sum($w), 0, '', 'T');

        $tbl = '<table cellspacing="0" cellpadding="3" border="0.1" BORDERCOLOR="#0000FF">';
        $tbl .= '<tr>';
        for ($i = 0; $i < $num_headers; ++$i) {

            $tbl .= '<th style="border:1px solid #e3e3e3" width="' . $headerWidth[$i] . '%"  ><b>' . $header[$i] . '</b></th>';
        }
        $tbl .= '</tr>';
        foreach ($data['data'] as $key => $row) {
            if ($subDistId != $row['subDistId']) {
                $tbl .= '<tr><td style="border:1px solid #e3e3e3" align="center" colspan="' . $num_headers . '"><b> SUB DISTRICT : ' . $row['subDist'] . '</b></td></tr>';
                $subDistId = $row['subDistId'];
            }
            $tbl .= '<tr >';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . ++$key . '</td>';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $row['name'] . '</td>';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $row['designation'] . '</td>';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $row['mobile'] . '</td>';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $row['school'] . '</td>';
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $row['branch'] . '</td>';

            if ($row['teacherType'] == 1) {
                $value = "Govt";
            } else {
                $value = "Aided";
            }
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $value . '</td>';
            if ($row['adhyapaka_sabdham_subscriber'] == 1) {
                $value = "Yes";
            } else {
                $value = "No";
            }
            $tbl .= '<td style="border:1px solid #e3e3e3">' . $value . '</td>';
            $tbl .= '</tr>';
        }

        $tbl .= '</table> ';

        $pdf->writeHTML($tbl, true, false, false, false, '');




        $pdf->Ln(10);
//        $pdf->Cell(180, 5, 'Number of Aided Members: ' . $data['count']['aidedMembers'], '', 1, 'L');
//        $pdf->Cell(180, 5, 'Number of Govt. Members: ' . $data['count']['govtMembers'], '', 1, 'L');
//        $pdf->Cell(180, 5, 'Total Number of members: ' . $data['count']['totalCount'], '', 1, 'L');
//        $pdf->Ln(2);
//        $pdf->Cell(180, 5, '****Designation wise*****', '', 1, 'L');
//        foreach ($data['designatioCount'] as $row) {
//            $pdf->Cell(180, 5, $row['designation'] . ': ' . $row['designationCount'], '', 1, 'L');
//        }
//        $pdf->Ln(2);
//        $pdf->Cell(180, 5, '****School type count*****', '', 1, 'L');
//        $totalSchool = 0;
//        foreach ($data['schoolCount'] as $row) {
//            $totalSchool = $row['count'] + $totalSchool;
//            $name = '';
//            if ($row['school_type'] == 1) {
//                $name = 'Government';
//            } else if ($row['school_type'] == 2) {
//                $name = 'Aided';
//            }
//            $pdf->Cell(180, 5, $name . ': ' . $row['count'], '', 1, 'L');
//        }
//        $pdf->Cell(180, 5, 'Total School: ' . $totalSchool, '', 1, 'L');
//        ob_end_flush();
//        $pdf->Output(PDF_PATH_TEMP . $data['fileName'], 'F');  //save pdf
        $this->designationWiseReport($pdf);

        $this->consolidationReport($pdf);

        $pdf->Output($data['fileName'], 'I');
    }

    function generatePdfConsolidated($data = array()) {

        ini_set('max_execution_time', 0);

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['view'] = trim((string)$this->input->get('view'));
        $param['year'] = trim((string)$this->input->get('year'));

        $data['data'] = $this->Teacher_model->getAll($param);
//        $data['count'] = $this->Teacher_model->getConsolidatedCount($param);
//        $data['designatioCount'] = $this->Teacher_model->getDesignationCount($param);
//        $data['schoolCount'] = $this->School_model->getSchoolTypeCount($param);
        ob_start();
        $data['fileName'] = 'Members_list_' . date('Y-m-d') . '.pdf';
//        $this->load->view('membership/teacher/download/pdf', $data, TRUE);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->viewName = $this->getViewName($param['view']);
        if ($pdf->viewName) {
            $pdf->SetTitle('MEMBERS REPORT [' . $pdf->viewName . ']');
        } else {
            $pdf->SetTitle('MEMBERS REPORT');
        }

        $pdf->SetHeaderMargin(10);
        $pdf->SetTopMargin(25);
        $pdf->setFooterMargin(15);
        $pdf->SetAutoPageBreak(true);
        $pdf->SetAuthor('Author');
        $pdf->SetDisplayMode('real', 'default');

//set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $pdf->Ln(2);

        if ($pdf->viewName) {
            $pdf->Cell(180, 5, 'MEMBERSHIP REPORT-' . $param['year'] . ' [' . $pdf->viewName . ']', '', 1, 'C');
        } else {
            $pdf->Cell(180, 5, 'MEMBERSHIP REPORT-' . $param['year'], '', 1, 'C');
        }
        $pdf->Ln();


        $data['count'] = $this->Teacher_model->getConsolidatedCount($param);
        $data['designatioCount'] = $this->Teacher_model->getDesignationCount($param);
        $data['schoolCount'] = $this->School_model->getSchoolTypeCount($param);



        $tbl = '
            <table border = "1">
            <tr>
            <th rowspan = "3">Left column</th>
            <th colspan = "5">Heading Column Span 5</th>
            <th colspan = "9">Heading Column Span 9</th>
            </tr>
            <tr>
            <th rowspan = "2">Rowspan 2<br />This is some text that fills the table cell.</th>
            <th colspan = "2">span 2</th>
            <th colspan = "2">span 2</th>
            <th rowspan = "2">2 rows</th>
            <th colspan = "8">Colspan 8</th>
            </tr>
            <tr>
            <th>1a</th>
            <th>2a</th>
            <th>1b</th>
            <th>2b</th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
            </tr>
            </table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');
    }

    function getViewName($id) {
        if ($id == 1) {
            $val = "Entered";
        } else if ($id == 2) {
            $val = "Confirmed";
        } else if ($id == 6) {
            $val = "Checked";
        } else if ($id == 3) {
            $val = "Verified";
        } else if ($id == 4) {
            $val = "Approved";
        } else {
            $val = false;
        }
        return $val;
    }

    function generateCsv() {
        
    }

    function process() {
        $officeId = $this->input->get('officeProcess');
        $groupId = $this->input->get('groupProcess');
        $param['process'] = $this->input->get('process');
        $param['year'] = $this->input->get('year');
        $param['action'] = $this->input->get('action') ? $this->input->get('action') : "accept";
        $ids = $this->input->get('ids');
        
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId($groupId, $officeId);

        $param['officeId'] = $getofficeId;
        $param['groupId'] = $getGroupId;
//        if (!$getofficeId && !$getGroupId) {
//            $param['id'] = explode(',', $ids);
//        }
        
        if(!$ids && $getofficeId == 0){
           $data['code'] = 'error';
            echo json_encode($data);
            exit; 
        }
        
        if($ids){
            $param['id'] = explode(',', $ids);
        }
        $this->load->model("membership/TeacherProcess_model", "TeacherProcess_model");
        $members = $this->Teacher_model->getAll($param);

        if (in_array($this->aauthGroupId, [static::AAUTH_GROUP_STATE]) && $param['process'] == "approve") {
            $this->TeacherProcess_model->approve($members, $param);
        } else if (in_array($this->aauthGroupId, [static::AAUTH_GROUP_DISTRICT, static::AAUTH_GROUP_STATE]) && $param['process'] == "verify") {
            $this->TeacherProcess_model->verify($members, $param);
        } else if (in_array($this->aauthGroupId, [static::AAUTH_GROUP_EDUCATION_DIST, static::AAUTH_GROUP_DISTRICT, static::AAUTH_GROUP_STATE]) && $param['process'] == "check") {
            $this->TeacherProcess_model->check($members, $param);
        } else if (in_array($this->aauthGroupId, [static::AAUTH_GROUP_SUB_DIST, static::AAUTH_GROUP_EDUCATION_DIST, static::AAUTH_GROUP_DISTRICT, static::AAUTH_GROUP_STATE]) && $param['process'] == "confirm") {
            $this->TeacherProcess_model->confirm($members, $param);
        }

        $data['content'] = $this->getContent($this->input->get('groupSel'), $this->input->get('officeSel'));
        $data['code'] = 'success';
        echo json_encode($data);
        exit;
    }

    function consoliated() {
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['view'] = trim((string)$this->input->get('view'));
        $param['year'] = trim((string)$this->input->get('year'));

        $content['designationCount'] = $this->Teacher_model->getDesignationCount($param);
        $content['schoolCount'] = $this->School_model->getSchoolTypeCount($param);

        $data['content'] = $this->load->view('membership/teacher/consolidatedCount', $content, TRUE);
        $data['code'] = 'success';
        echo json_encode($data);
        exit;
    }

    function consolidationReport($continue = false) {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['view'] = trim((string)$this->input->get('view'));
        $param['year'] = trim((string)$this->input->get('year'));
        $param['groupView'] = $this->input->get('group_view') != 'undefined' ? trim((string)$this->input->get('group_view')) : 6;

        $structure = $this->consolidationReportStructure($param);

        if ($continue) {
            $pdf = $continue;
        }else{
            ob_start();
            $data['fileName'] = $structure['fileName'] . '_' . date('Y-m-d') . '.pdf';
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->viewName = $this->getViewName($param['view']);

            $pdf->SetTitle('MEMBERS REPORT');
            $pdf->SetHeaderMargin(10);
            $pdf->SetTopMargin(25);
            $pdf->setFooterMargin(15);
            $pdf->SetAutoPageBreak(true);
            $pdf->SetAuthor('Author');
            $pdf->SetDisplayMode('real', 'default');

//set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        }

        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $pdf->Ln(2);


        $pdf->SetFont('helvetica', 'B', 11);

        if ($pdf->viewName) {
            $pdf->Cell(180, 5, $structure['title'] . ' (' . $pdf->viewName . ')', '', 1, 'C');
        } else {
            $pdf->Cell(180, 5, $structure['title'], '', 1, 'C');
        }
        if ($structure['titleSub']) {
            $pdf->Cell(180, 5, $structure['titleSub'], '', 1, 'C');
        }
        $pdf->Ln();

        $header = $structure['header'];
        $headerWidth = $structure['headerWidth'];
        $extraHeader = $structure['extraHeader'];

        $pdf->SetFont('', ' ', '10');
// Header
        $w = array(30, 30, 40, 32, 27, 31);
        $num_headers = count($header);

        $tbl = '<table cellspacing = "0" cellpadding = "3" border = "0.1" BORDERCOLOR = "#0000FF">';
        $tbl .= '<tr>';
        for ($i = 0; $i < $num_headers; ++$i) {
            $tbl .= '<th style = "border:1px solid #e3e3e3" width = "' . $headerWidth[$i] . '%" ><b>' . $header[$i] . '</b></th>';
        }
        $tbl .= '</tr>';
        $aidedTotal = $govtTotal = $total = 0;
        foreach ($structure['data'] as $key => $row) {
            $aidedTotal += $row['aidedMembers'];
            $govtTotal += $row['govtMembers'];
            $subtotal = ($row['aidedMembers'] + $row['govtMembers']);
            $total += $subtotal;
            $tbl .= '<tr >';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . ++$key . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['name'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['govtMembers'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['aidedMembers'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $subtotal . '</td>';
            foreach ($extraHeader as $extra) {
                if (isset($row[$extra])) {
                    $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row[$extra] . '</td>';
                }
            }
            $tbl .= '</tr>';
        }
        $tbl .= '<tr >';
        $tbl .= '<td style = "border:1px solid #e3e3e3">Total</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3"></td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $govtTotal . '</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $aidedTotal . '</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $total . '</td>';
        $tbl .= '</tr>';

        $tbl .= '</table> ';

        $pdf->writeHTML($tbl, true, false, false, false, '');

        $pdf->Ln(10);

        if (!$continue) {
            $pdf->Output($data['fileName'], 'I');
        }
    }

    function consolidationReportStructure($param) {

        switch ($param['groupView']) {
            case static::AAUTH_GROUP_DISTRICT:
                $return = array(
                    'title' => 'Membership Distrcit Wise Consolidation Report ' . $param['year'],
                    'titleSub' => '',
                    'header' => ['SL.NO', 'Name of District', 'Govt.', 'Aided', 'Total'],
                    'headerWidth' => [10, 40, 14, 14, 18],
                    'extraHeader' => [],
                    'data' => $this->Teacher_model->getConsolidationDistrict($param),
                    'fileName' => 'District_wise_consolidation'
                );
                break;
            case static::AAUTH_GROUP_SUB_DIST:
                $return = array(
                    'title' => 'Membership Sub Distrcit Wise Consolidation Report ' . $param['year'],
                    'titleSub' => '',
                    'header' => ['SL.NO', 'Name of Sub District', 'Govt.', 'Aided', 'Total', 'Edu. District'],
                    'headerWidth' => [10, 40, 10, 10, 10, 20],
                    'extraHeader' => ['eduDistrict'],
                    'data' => $this->Teacher_model->getConsolidationSubDistrict($param),
                    'fileName' => 'Sub_district_wise_consolidation'
                );
                break;
            case static::AAUTH_GROUP_SCHOOL:
                $return = array(
                    'title' => 'Membership School Wise Consolidation Report ' . $param['year'],
                    'titleSub' => '',
                    'header' => ['SL.NO', 'Name of School', 'Govt.', 'Aided', 'Total', 'Branch Name'],
                    'headerWidth' => [10, 40, 10, 10, 10, 20],
                    'extraHeader' => ['branch'],
                    'data' => $this->Teacher_model->getConsolidationSchool($param),
                    'fileName' => 'School_wise_consolidation'
                );

                break;
            default :
                die('die');
        }

        $return['titleSub'] = $this->getTitleSub($param);

        return $return;
    }

    function getTitleSub($param) {
        $titleSub = '';
        if ($param['groupId'] && $param['officeId']) {
            $officeById = $this->getOfficeById($param['groupId'], $param['officeId']);
            $titleSub = $officeById['officeSelect']['name'] . ' - ' . $officeById['officeLabel'];
        } else {
            if ($this->aauthGroupId == static::AAUTH_GROUP_STATE) {
                $titleSub = $this->session->userdata("officeName") . ' - State';
            } else if ($this->aauthGroupId == static::AAUTH_GROUP_DISTRICT) {
                $titleSub = $this->session->userdata("officeName") . ' - District';
            } else if ($this->aauthGroupId == static::AAUTH_GROUP_EDUCATION_DIST) {
                $titleSub = $this->session->userdata("officeName") . ' - Edu District';
            } else if ($this->aauthGroupId == static::AAUTH_GROUP_SUB_DIST) {
                $titleSub = $this->session->userdata("officeName") . ' - Sub District';
            }
        }
        return $titleSub;
    }

    function designationWiseReport($continue = false) {
        ini_set('max_execution_time', 0);
        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['search'] = trim((string)$this->input->get('search'));
        $param['view'] = trim((string)$this->input->get('view'));
        $param['year'] = trim((string)$this->input->get('year'));
        $param['groupView'] = trim((string)$this->input->get('group_view'));

        if (empty($param['year'])) {
            $param['year'] = $this->year;
        }

        if ($continue) {
            $pdf = $continue;
        }else{
            ob_start();
            $data['fileName'] = 'Designation_wise_' . date('Y-m-d') . '.pdf';
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->viewName = $this->getViewName($param['view']);

            $pdf->SetTitle('MEMBERS REPORT');
            $pdf->SetHeaderMargin(10);
            $pdf->SetTopMargin(25);
            $pdf->setFooterMargin(15);
            $pdf->SetAutoPageBreak(true);
            $pdf->SetAuthor('Author');
            $pdf->SetDisplayMode('real', 'default');

//set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        }

        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $pdf->Ln(2);

        $result = $this->Teacher_model->getDesignationCount($param);

        $pdf->SetFont('helvetica', 'B', 11);

        if ($pdf->viewName) {
            $pdf->Cell(180, 5, 'Membership Designation Wise Consolidation Report ' . $param['year'] . ' (' . $pdf->viewName . ')', '', 1, 'C');
        } else {
            $pdf->Cell(180, 5, 'Membership Designation Wise Consolidation Report ' . $param['year'], '', 1, 'C');
        }

        $pdf->Cell(180, 5, $this->getTitleSub($param), '', 1, 'C');

        $pdf->Ln();

        $header = ['SL.NO', 'Designation', 'Govt.', 'Aided', 'Total'];
        $headerWidth = [10, 40, 15, 15, 20];


        $pdf->SetFont('', ' ', '10');
        $num_headers = count($header);

        $tbl = '<table cellspacing = "0" cellpadding = "3" border = "0.1" BORDERCOLOR = "#0000FF">';
        $tbl .= '<tr>';
        for ($i = 0; $i < $num_headers; ++$i) {
            $tbl .= '<th style = "border:1px solid #e3e3e3" width = "' . $headerWidth[$i] . '%" ><b>' . $header[$i] . '</b></th>';
        }
        $tbl .= '</tr>';
        $aidedTotal = $govtTotal = $total = 0;
        foreach ($result as $key => $row) {
            $aidedTotal += (int) $row['aidedMembers'];
            $govtTotal += (int) $row['govtMembers'];
            $total += (int) $row['designationCount'];
            $tbl .= '<tr >';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . ++$key . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['designation'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['govtMembers'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['aidedMembers'] . '</td>';
            $tbl .= '<td style = "border:1px solid #e3e3e3">' . $row['designationCount'] . '</td>';

            $tbl .= '</tr>';
        }
        $tbl .= '<tr >';
        $tbl .= '<td style = "border:1px solid #e3e3e3">Total</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3"></td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $govtTotal . '</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $aidedTotal . '</td>';
        $tbl .= '<td style = "border:1px solid #e3e3e3">' . $total . '</td>';
        $tbl .= '</tr>';

        $tbl .= '</table> ';

        $pdf->writeHTML($tbl, true, false, false, false, '');

        $pdf->Ln(10);
        if (!$continue) {
            $pdf->Output($data['fileName'], 'I');
        }
    }

    public function view() {
        $data['code'] = 'error';
        $id = $this->uri->segment(4);

        list($getGroupId, $getofficeId) = $this->getGroupOfficeId();
        $param['groupId'] = $getGroupId;
        $param['officeId'] = $getofficeId;
        $param['id'] = $id;

        $formInfo = $this->Teacher_model->getById($param);

        if ($formInfo) {
            $formInfo['designation'] = $formInfo['designation_id'];
            $formInfo['school'] = $formInfo['school_id'];

            $url = base_url('membership/teacher/update/' . $id);

            $data['form'] = $this->load->view('membership/teacher/view', $formInfo, TRUE);
            $data['code'] = 'success';
        }
        echo json_encode($data);
        exit;
    }

}
