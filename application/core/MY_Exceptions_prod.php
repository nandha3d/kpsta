<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class dealing with errors as exceptions
 */
class MY_Exceptions extends CI_Exceptions {

    /**
     * Force exception throwing on erros
     */
    public function __construct() {

        // get main CI object
        $this->CI = & get_instance();

        // Dependancies
        if (CI_VERSION >= 2.2) {
            $this->CI->load->library('driver');
        }
    }

    public function show_error($heading, $message, $template = 'error_general', $status_code = 500) {

        if (ENVIRONMENT == "development") {
            echo "<pre>";
            echo $heading;
            print_r($message);
            exit;
        }
        set_status_header($status_code);

        $message = implode(" / ", (!is_array($message)) ? array($message) : $message);

        $header = "From:kpsta.in@gmail.com \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";

        $body = $message . ' \r\n';
        $body .=' URL : ' . $this->CI->uri->uri_string;

        try {
            mail("rajeshrkcse@gmail.com", "Kpsta - " . $heading, $message, $header);
        } catch (Exception $e) {
            
        }
    }

}
