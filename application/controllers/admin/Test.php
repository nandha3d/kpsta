<?php
class Test extends CI_Controller {
    public function index() {
        $config['upload_path'] = './uploads/';
        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('image')) {
            $error_msg = $this->upload->display_errors();
            $data['code'] = 'error';
            $data['error'] = $error_msg;
            var_dump($data);
        }
        
        try {
            $html = $this->load->view("admin/adayapakaSabham/form", $data, TRUE);
            echo "SUCCESS\n";
        } catch (\Throwable $e) {
            echo "ERROR: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
        }
    }
}
