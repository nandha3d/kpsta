<?php

//session_start(); //we need to start session in order to access it through CI

Class Authentication extends Membership_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library("Aauth");

// Load form helper library
        $this->load->helper('form');

// Load form validation library
        $this->load->library('form_validation');

// Load session library
// Load database
//        $this->load->model('login_database');
    }

    private function isLoggedIn() {
        if ($this->aauth->is_loggedin()) {
            redirect('membership/home');
        }
    }

    public function login() {
        $this->isLoggedIn();
        $data = False;
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == TRUE) {

            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember');

            $aauthCheck = $this->aauth->login($username, $password, $remember);
            if ($aauthCheck) {
                $this->isLoggedIn();
            } else {
                $data['aauthErrors'] = $this->aauth->get_errors_array();
            }
        }

        $data['recaptcha'] = $this->aauth->generate_recaptcha_field();

        $this->load->view('admin/login', ['data' => $data]);
    }

   

// Logout from admin page
    public function logout() {
        $this->aauth->logout();
        redirect('membership/login');
    }

}

?>