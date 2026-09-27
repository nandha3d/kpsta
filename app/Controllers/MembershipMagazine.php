<?php

namespace App\Controllers;

class MembershipMagazine extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->helper(array('form', 'url'));
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('home/membership_magazine');
        $this->load->view('footer');
    }

}
