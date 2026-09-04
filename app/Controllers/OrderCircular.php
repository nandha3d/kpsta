<?php

namespace App\Controllers;
class OrderCircular extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();
        $this->load->model("OrderCircular_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index() {
        $segment = $this->getType($this->uri->segment(2));

        $content['contentTitle'] = $segment['contentTitle'];

        $param['category'] = $this->input->get('category');
        $param['search'] = $this->input->get('search');

        $param['limit'] =30;
        $param['page'] = (int)($this->input->get('page') && $this->input->get('page') !== 'undefined' ? $this->input->get('page') : 1);
        $param['offset'] = ($param['page'] > 0) ? ($param['page'] - 1) * $param['limit'] : 0;
        $param['type'] = $segment['type'];
        $content['categories'] = $this->OrderCircular_model->getOrderCircularCategory($param);

        $config["total_rows"] = $this->OrderCircular_model->getAllByTypeCount($param, TRUE);
        if ($param['category'] > 0 && $param['search']) {
            $config['suffix'] = '&category=' . $param['category'] . '&search=' . $param['search'];
            $config['first_url'] = '?category=' . $param['category'] . '&search=' . $param['search'];
        } else if ($param['category'] > 0) {
            $config['suffix'] = '&category=' . $param['category'];
            $config['first_url'] = '?category=' . $param['category'];
        } else if ($param['search']) {
            $config['suffix'] = '&search=' . $param['search'];
            $config['first_url'] = '?search=' . $param['search'];
        }
        $config["base_url"] = base_url() . "order-circular/" . $this->uri->segment(2);
        $config["per_page"] = $param['limit'];

        if ($config["total_rows"] <= $param['offset']) {
            $param['offset'] = 0;
        }
        $configBootrap = $this->BootsrapPaginationConfig();
        $config = array_merge($config, $configBootrap);
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $content["links"] = $this->pagination->create_links();

        $content['selectedCategory'] = $param['category'];
        $content['selectedCategoryName'] = $this->OrderCircular_model->getCategoryById($param['category']);
        $content['search'] = $param['search'];

        $content['urlString'] = base_url($this->uri->uri_string());

        $orders = $this->OrderCircular_model->getAllByType($param, TRUE);

        $content['orders'] = array();
        foreach ($orders as $order) {
            try {
                $dataExp = explode('-', $order['date']);
                $month = date('F', strtotime($order['date_unformat']));
                $key = $month . ', ' . $dataExp[2];
                $content['orders'][$key][] = $order;
            } catch (\Exception $exc) {
                
            }
        }

        $this->load->view('header');
        $this->load->view('orderCircular/orderCircular', $content);
        $this->load->view('footer');
    }

    function getType($url) {
        switch ($url) {
            case 'general':
                $data['type'] = 1;
                $data['contentTitle'] = 'General';
                break;
            case 'hse':
                $data['type'] = 2;
                $data['contentTitle'] = 'HSE';
                break;
            case 'vhse':
                $data['type'] = 3;
                $data['contentTitle'] = 'VHSE';
                break;

            default:
                $data['type'] = 1;
                $data['contentTitle'] = 'General';
                break;
        }
        return $data;
    }

}
