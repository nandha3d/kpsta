<?php

namespace App\Controllers;
require APPPATH. 'libraries/razorpay/Razorpay.php';
use Razorpay\Api\Api;

class Donation extends PublicController {

    private $id = "rzp_test_fkTHmuBoSCPJ5s";
    private $secret = "q5V3abmFVXngDA5YqOwq4s5Z";

    public function ci3Init(): void {
        parent::ci3Init();
         // Load form helper library
         $this->load->helper('form');
         // Load form validation library
         $this->load->library('session');
         $this->load->library('form_validation');
        $this->load->model("Donation_model");
    }

    public function index() {

        $data['form'] = $this->createForm();
        $data['key'] =  $this->id;

        $this->load->view('donation/index', $data );
    }

    public function createForm($formValues= []) {
        $db = $this->load->database('dbname');
        $data['designationSelect'] = $this->createSelectDropDown($this->Donation_model->getDesignation(), 'name', '--SELECT DESIGNATION --');
        $data['districtSelect'] = $this->createSelectDropDown($this->Donation_model->getDistrict(), 'name', '--SELECT DISTRICT --');
       

        return $this->load->view('donation/form', $data, TRUE);
    }

    public function pay(){

        $formValues = $this->formValidation();

        //validation FALSE
        if ($this->form_validation->run() == FALSE) {
            $data['code'] = 'error';
            $url = base_url('donation/form');
            $data['form'] = $this->createForm($formValues);
            echo json_encode($data);
            exit;
        }

        $uuid = $this->input->cookie('uuid', TRUE);
        $order = [];

        //check order already exist
        if( $uuid){
            $entry = $this->Donation_model->getActiveByUuid($uuid);
            // print_r($entry);exit;
            if($entry){
                $order = $entry;
                $order['id'] = $entry['order_id'];
                $trans = $this->Donation_model->update($entry['id'], $formValues);
            }
        }
        
        //create new order
        if(count($order) == 0){
            $uuid = $this->uuid_key();
            $order = $this->createOrder($uuid);

              //Insert values
            $formValues['order_id'] = $order['id'];
            $formValues['uuid'] = $uuid;
            $formValues['amount'] = 100;

            $trans = $this->Donation_model->add($formValues);
        }

      
        if ($trans) {
            $data['form'] = $formValues;
            $data['amount'] =   $order['amount'];
            $data['orderId'] =   $order['id'];
            $data['uuid'] = $uuid;
            $data['code'] = 'success';
          } else {
            $data['code'] = 'failed';
            $data['data'] = 'Something went wrong! Pls try again';
        }

        echo json_encode($data);
        exit;
      
    }

    public function createOrder($uuid){
        $api =  new API($this->id, $this->secret);
        $orderData = [
            'receipt'         => $uuid,
            'amount'          => 10000,
            'currency'        => 'INR'
        ];
       return $api->order->create($orderData);
    }

    
    function uuid_key() {
        $this->load->library('uuid');
        //Output a v4 UUID 
        $id = $this->uuid->v4();
        return (string) str_replace('-', '', $id);
    }

    function validateEmail($email) {
        if (preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
        return true;  
        }
        return false;
    }

    
    function formValidation() {
        
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone no', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|callback_validateEmail');
        $this->form_validation->set_rules('place', 'Location', 'trim');
        $this->form_validation->set_rules('designation', 'Designation', 'trim');
        $this->form_validation->set_rules('district', 'District', 'trim');
        $this->form_validation->set_message('is_natural_no_zero', 'The %s is required');

        $this->form_validation->set_error_delimiters("<p>", "</p>");
        //set all form values into array
        $formValues = [
            'name'              => $this->input->post('name'),
            'phone'            => $this->input->post('phone'),
            'email'             => $this->input->post('email'),
            'designation_id'       => $this->input->post('designation'),
            'district_id'       => $this->input->post('district'),
            'place'      => $this->input->post('place'),
        ];

        return $formValues;
    }

    public function paymentStatus(){
        $api = new Api($this->id, $this->secret);

        $order_id = $this->input->post('razorpay_order_id');
        $payment_id =  $this->input->post('razorpay_payment_id');
        $signature =  $this->input->post('razorpay_signature');

        $api->utility->verifyPaymentSignature(
            array(
                'razorpay_order_id' =>  $order_id, 
                'razorpay_payment_id' => $payment_id, 
                'razorpay_signature' => $signature)
            );

        $generated_signature = hash_hmac("sha256", $order_id . "|" . $payment_id, $this->secret);
        
        $status = "failure";
        if ($generated_signature == $signature) {
            $status = 'success';
        }

        $values = [
            'status' => $status,
            'payment_id' => $payment_id,
            'signature' => $signature
        ];
        $trans = $this->Donation_model->updateByOrderId($order_id, $values);

        if($trans){
            $trans = $this->Donation_model->getByOrderId($order_id);
            redirect(base_url()."donation/success/".$trans['uuid']);
        }
      
    }

    public function success(){  

        $uuid = $this->uri->segment(3);

        $entry = $this->Donation_model->getActiveByUuid($uuid);


        $this->load->view('donation/success');
    }



}
