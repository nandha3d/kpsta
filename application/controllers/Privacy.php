<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 

class Privacy extends Public_Controller {


    public function __construct() {
        parent::__construct();
         // Load form helper library
         // Load form validation library
    }

    public function index() {

        

        $this->load->view('header');
        $this->load->view('privacy/privacy' );
        $this->load->view('footer');
    }

    


}

