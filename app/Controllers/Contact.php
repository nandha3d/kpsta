<?php

namespace App\Controllers;
class Contact extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        // Load form helper library
        $this->load->helper(array('form', 'url'));
        // Load form validation library
        $this->load->library('form_validation');
        /* loding model */
    }

    public function index() {
        $data['isMailSend'] = false;

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('content', 'Content', 'trim|required');
        if ($this->form_validation->run() == TRUE) {

            $data['mailInfo'] = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'content' => $this->input->post('content'),
            ];

            $body = $this->load->view('contact/mailTemplate', $data, TRUE);

            $this->load->library('mail');
            $result = $this->mail->send($data['mailInfo']['email'], 'KPSTA website - Enquiry', $body);

            if ($result) {
                $data['isMailSend'] = TRUE;
            }
        }

        $this->load->model("OfficeBearer_model");
        $data['officeBearer'] = $this->OfficeBearer_model->getAll(array(
            'isPublish' => TRUE,
            'level' => 'State',
            'is_former' => 0,
            'designation' => '1,2,3',
            'sort' => 'primary',
            'limit' => 3,
        ));

        $this->load->view('header');
        $this->load->view('contact/contact', $data);
        $this->load->view('footer');
    }

}
