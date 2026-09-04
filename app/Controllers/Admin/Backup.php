<?php

namespace App\Controllers\Admin;

use App\Controllers\AppController;
//session_start(); //we need to start session in order to access it through CI

class Backup extends AppController {

    private $imageWidthThumb, $imageHeightThumb;

    public function ci3Init(): void {
        parent::ci3Init();

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

        $dir = rtrim(FILE_UPLOAD_PATH_TEMP, '/');
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return $this->response->setStatusCode(500)->setBody('Unable to create the backup directory.');
        }

        $PATH = $dir . '/' . $filename;

        // Values are escaped rather than interpolated: they come from config,
        // but a password containing a shell metacharacter would otherwise break
        // out of the command. The password goes through MYSQL_PWD instead of
        // --password so it does not show up in the process list.
        $command = sprintf(
            'mysqldump --user=%s --host=%s %s > %s',
            escapeshellarg($DBUSER),
            escapeshellarg($DBHOST),
            escapeshellarg($DATABASE),
            escapeshellarg($PATH)
        );

        $previousPwd = getenv('MYSQL_PWD');
        putenv('MYSQL_PWD=' . $DBPASSWD);

        $output = [];
        $status = 0;
        exec($command . ' 2>&1', $output, $status);

        // Restore the environment rather than leaving the password set.
        if ($previousPwd === false) {
            putenv('MYSQL_PWD');
        } else {
            putenv('MYSQL_PWD=' . $previousPwd);
        }

        if ($status !== 0 || ! is_file($PATH)) {
            log_message('error', 'mysqldump failed: ' . implode("\n", $output));

            return $this->response->setStatusCode(500)->setBody('The database backup could not be created.');
        }

        // Stat the file that was actually written. This previously measured
        // $filename, a bare name relative to the working directory that never
        // existed, so Content-Length was wrong and the stat raised a warning.
        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Length', (string) filesize($PATH))
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody((string) file_get_contents($PATH));
    }

    function getContent() {

        $content['content'] = [];
        return $this->load->view('admin/backup/content', $content, TRUE);
    }

}
