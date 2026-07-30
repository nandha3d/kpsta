<?php

//session_start(); //we need to start session in order to access it through CI

Class Backup extends MY_Controller {

    private $imageWidthThumb, $imageHeightThumb;

    public function __construct() {
        parent::__construct();

        // Load form helper library
        $this->load->helper('form');
        // Load form validation library
        $this->load->library('form_validation');
    }

    public function index() {

        $data = false;
//        $data['form'] = $this->createForm(base_url('admin/adayapaka_sabham/add'));
        $data['content'] = $this->getContent();


        //check whether the request is Ajax, Then send json return data
        if ($this->input->is_ajax_request()) {
            $data['newUrl'] = $this->newUrl;
            $data['code'] = 'success';
            echo json_encode($data);
            exit;
        }


        $header['special_css'] = ['css/imgareaselect-default.css'];
        $footer['special_js'] = ['js/adayapakaSabham.js', 'js/jquery.imgareaselect.min.js'];

        $this->load->view('admin/header', $header);
        $this->load->view('admin/backup/backup', $data);
        $this->load->view('admin/footer', $footer);
    }

    function downloadDB() {

        $this->config->load('database');
        $db = $this->config->item('db_config');

        $dbQuery = $this->input->get('db');

        if ($dbQuery == "membership") {
            $dbConfig = $db['membership'];
            $filename = "backup-db-membership.sql";
        } else {
            $dbConfig = $db['default'];
            $filename = "backup-db-kpsta.sql";
        }

        $DBHOST = $dbConfig['hostname'];
        $DBUSER = $dbConfig['username'];
        $DBPASSWD = $dbConfig['password'];
        $DATABASE = $dbConfig['database'];

        $PATH = FILE_UPLOAD_PATH_TEMP . '/' . $filename;

        exec('mysqldump --user=' . $DBUSER . ' --password=' . $DBPASSWD . ' --host=' . $DBHOST . ' ' . $DATABASE . ' > ' . $PATH);


        $mime = "application/octet-stream";
        header("Content-Type: " . $mime);
        header("Content-Length: " . filesize($filename));
        header('Content-Disposition: attachment;   filename = "' . $filename . '"');

        passthru("cat {$PATH}");
    }

    function getContent() {

        $content['content'] = [];
        return $this->load->view('admin/backup/content', $content, TRUE);
    }

}
