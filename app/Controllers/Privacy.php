<?php

namespace App\Controllers;
class Privacy extends PublicController {


    public function ci3Init(): void {
        parent::ci3Init();
         // Load form helper library
         // Load form validation library
    }

    public function index() {

        

        $this->load->view('header');
        $this->load->view('privacy/privacy' );
        $this->load->view('footer');
    }

    


}
