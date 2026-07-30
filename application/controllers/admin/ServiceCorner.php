<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceCorner extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ServiceCorner_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    public function index() {
        $data['services'] = $this->ServiceCorner_model->getAll();
        $this->load->view('admin/header');
        $this->load->view('admin/service_corner/list', $data);
        $this->load->view('admin/footer');
    }

    public function add() {
        $this->form_validation->set_rules('title', 'Title', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/header');
            $this->load->view('admin/service_corner/form');
            $this->load->view('admin/footer');
        } else {
            $data = array(
                'service_number' => $this->input->post('service_number'),
                'title'          => $this->input->post('title'),
                'description'    => $this->input->post('description'),
                'link'           => $this->input->post('link') ?: 'Home/service_corner_details',
                'icon'           => $this->input->post('icon') ?: 'info',
                'status'         => $this->input->post('status') !== null ? $this->input->post('status') : 1,
            );
            $insert = $this->ServiceCorner_model->insert($data);
            if ($insert) {
                $this->session->set_flashdata('success_msg', 'Service has been added successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Some problems occurred, please try again.');
            }
            redirect('admin/ServiceCorner');
        }
    }

    public function edit($id) {
        $data['service'] = $this->ServiceCorner_model->getAll(array('id' => $id));
        if (empty($data['service'])) {
            redirect('admin/ServiceCorner');
        }

        $this->form_validation->set_rules('title', 'Title', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/header');
            $this->load->view('admin/service_corner/form', $data);
            $this->load->view('admin/footer');
        } else {
            $updateData = array(
                'service_number' => $this->input->post('service_number'),
                'title'          => $this->input->post('title'),
                'description'    => $this->input->post('description'),
                'link'           => $this->input->post('link') ?: 'Home/service_corner_details',
                'icon'           => $this->input->post('icon') ?: 'info',
                'status'         => $this->input->post('status') !== null ? $this->input->post('status') : 1,
            );
            $update = $this->ServiceCorner_model->update($updateData, $id);
            if ($update) {
                $this->session->set_flashdata('success_msg', 'Service has been updated successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Some problems occurred, please try again.');
            }
            redirect('admin/ServiceCorner');
        }
    }

    public function publish($id = null) {
        if (!$id) {
            $id = $this->uri->segment(4);
        }
        $service = $this->ServiceCorner_model->getAll(array('id' => $id));
        if ($id && $service) {
            $newStatus = ($service['status'] == 1) ? 0 : 1;
            $this->ServiceCorner_model->update(array('status' => $newStatus), $id);
            echo json_encode(array('code' => 'success', 'status' => $newStatus));
            exit;
        }
        echo json_encode(array('code' => 'error', 'msg' => 'Service not found.'));
        exit;
    }

    public function deleteAction() {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $delete = $this->ServiceCorner_model->delete($id);
            if ($delete) {
                $data['code'] = 'success';
                $data['msg'] = "Service deleted successfully.";
            } else {
                $data['code'] = 'error';
                $data['msg'] = "Some problem occurred, please try again.";
            }
            echo json_encode($data);
        }
    }

    // RULES / ACCORDION MANAGEMENT UNDER SERVICE
    public function rules($service_id) {
        $data['service'] = $this->ServiceCorner_model->getAll(array('id' => $service_id));
        if (empty($data['service'])) {
            redirect('admin/ServiceCorner');
        }
        $data['rules'] = $this->ServiceCorner_model->getRulesByServiceId($service_id);
        $this->load->view('admin/header');
        $this->load->view('admin/service_corner/rules_list', $data);
        $this->load->view('admin/footer');
    }

    public function add_rule($service_id) {
        $data['service'] = $this->ServiceCorner_model->getAll(array('id' => $service_id));
        if (empty($data['service'])) {
            redirect('admin/ServiceCorner');
        }

        $this->form_validation->set_rules('title', 'Rule Title', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/header');
            $this->load->view('admin/service_corner/rule_form', $data);
            $this->load->view('admin/footer');
        } else {
            $ruleData = array(
                'service_id'  => $service_id,
                'rule_number' => $this->input->post('rule_number'),
                'title'       => $this->input->post('title'),
                'content'     => $this->input->post('content'),
                'form_link'   => $this->input->post('form_link'),
                'position'    => $this->input->post('position') ?: 100,
            );
            $insert = $this->ServiceCorner_model->insertRule($ruleData);
            if ($insert) {
                $this->session->set_flashdata('success_msg', 'Rule item has been added successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Some problem occurred, please try again.');
            }
            redirect('admin/ServiceCorner/rules/' . $service_id);
        }
    }

    public function edit_rule($rule_id) {
        $data['rule'] = $this->ServiceCorner_model->getRuleById($rule_id);
        if (empty($data['rule'])) {
            redirect('admin/ServiceCorner');
        }
        $service_id = $data['rule']['service_id'];
        $data['service'] = $this->ServiceCorner_model->getAll(array('id' => $service_id));

        $this->form_validation->set_rules('title', 'Rule Title', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/header');
            $this->load->view('admin/service_corner/rule_form', $data);
            $this->load->view('admin/footer');
        } else {
            $updateData = array(
                'rule_number' => $this->input->post('rule_number'),
                'title'       => $this->input->post('title'),
                'content'     => $this->input->post('content'),
                'form_link'   => $this->input->post('form_link'),
                'position'    => $this->input->post('position') ?: 100,
            );
            $update = $this->ServiceCorner_model->updateRule($updateData, $rule_id);
            if ($update) {
                $this->session->set_flashdata('success_msg', 'Rule item has been updated successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Some problem occurred, please try again.');
            }
            redirect('admin/ServiceCorner/rules/' . $service_id);
        }
    }

    public function delete_rule($rule_id) {
        $rule = $this->ServiceCorner_model->getRuleById($rule_id);
        if ($rule) {
            $service_id = $rule['service_id'];
            $this->ServiceCorner_model->deleteRule($rule_id);
            $this->session->set_flashdata('success_msg', 'Rule item deleted successfully.');
            redirect('admin/ServiceCorner/rules/' . $service_id);
        }
        redirect('admin/ServiceCorner');
    }
}
