<?php

namespace App\Controllers;
class Home extends PublicController {

    public function ci3Init(): void {
        parent::ci3Init();

        // Load form helper library
        $this->load->helper(array('form', 'url'));

        $this->load->model("OrderCircular_model");
        $this->load->model("news_model");
        $this->load->model("gallery_model");
        $this->load->model("Quicklink_model");
        $this->load->model("FlashNews_model");
        $this->load->model("Slider_model");
        $this->load->model("ReactionGallery_model");
        $this->load->model("OfficeBearer_model");
        $this->load->model("Settings_model");
    }

    /**
     * Web cache Enabled for HOme page
     * Pls refer My_controller contruct function for deleting home page
     */
    public function index() {

        $content['orders'] = $this->OrderCircular_model->getAllByType(['limit' => 6], TRUE);

        $content['listNews'] = $this->news_model->getAllNews(['limit' => 3], TRUE);

        $content['images'] = $this->gallery_model->getAllImages(['limit' => 20, 'isPublish' => TRUE,]);

        $content['quicklink'] = $this->Quicklink_model->getAll([ "limit" => 8, "isPublish" => TRUE]);

        $content['sliderImages'] = $this->Slider_model->getAll([ 'isPublish' => TRUE, 'limit' => 5, 'show_on_home' => 1]);

        $content['reactionGalleryImages'] = $this->ReactionGallery_model->getAll([ 'isPublish' => TRUE, 'limit' => 5]);

        $content['news'] = $this->FlashNews_model->getAll(array('isPublish' => TRUE));

        $active_term = $this->Settings_model->getActiveTerm();
        $content['officeBearer'] = $this->OfficeBearer_model->getAll(array('isPublish' => TRUE, 'is_former' => 0, 'limit' => 3, 'active_term' => $active_term, 'level' => 'State', 'designation' => '1,2,3', 'sort' => 'primary'));


        if (ENVIRONMENT == "development") {
            $this->output->delete_cache(base_url($this->uri->uri_string()));
        } else {
            $this->output->cache(2000);
            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
            $this->output->set_header("Pragma: no-cache");
        }



        $this->load->view('header');
        $this->load->view('home/index', $content);
        $this->load->view('footer');
    }

    public function service_corner() {
        $this->load->model('ServiceCorner_model');
        $content['services'] = $this->ServiceCorner_model->getAll(array('status' => 1));
        
        $this->load->view('header');
        $this->load->view('home/service_corner', $content);
        $this->load->view('footer');
    }

    public function service_corner_details($service_id = null) {
        $this->load->model('ServiceCorner_model');
        $content = array();
        if ($service_id) {
            $service = $this->ServiceCorner_model->getAll(array('id' => $service_id));
            if ($service) {
                $content['service'] = $service;
                $content['rules'] = $this->ServiceCorner_model->getRulesByServiceId($service_id);
            }
        }
        if (empty($content['service'])) {
            $services = $this->ServiceCorner_model->getAll(array('status' => 1));
            if (!empty($services)) {
                $content['service'] = $services[0];
                $content['rules'] = $this->ServiceCorner_model->getRulesByServiceId($services[0]['id']);
            }
        }
        $this->load->view('header');
        $this->load->view('home/service_corner_details', $content);
        $this->load->view('footer');
    }

    public function former_leaders() {
        // Deliberately no active_term filter: former leaders belong to past
        // terms by definition, so restricting to the current term would hide
        // every one of them.
        $raw = $this->OfficeBearer_model->getAll(array('isPublish' => TRUE, 'is_former' => 1, 'limit' => 500));

        // Consolidate persons so that a leader who held multiple positions
        // across different periods appears as a single unified card listing all their positions.
        $leaders = array();
        $personIndex = array(); // unique key => index in $leaders

        if (!empty($raw)) {
            foreach ($raw as $ob) {
                $normName = mb_strtolower(trim((string)$ob['name']));
                $phone = !empty($ob['phone']) ? preg_replace('/[^0-9]/', '', (string)$ob['phone']) : '';

                // Matching key: if phone exists, name + phone, else name
                $lookupKey = !empty($phone) ? ($normName . '|' . $phone) : $normName;

                // Primary position
                $primaryPos = [
                    'designation' => $ob['designation'],
                    'year' => !empty($ob['year']) ? $ob['year'] : '',
                    'level' => !empty($ob['level']) ? $ob['level'] : 'State',
                    'section_heading' => !empty($ob['section_heading']) ? $ob['section_heading'] : '',
                    'position' => isset($ob['position']) && is_numeric($ob['position']) ? (int)$ob['position'] : 25
                ];

                // Previous positions from JSON column
                $additionalPositions = [];
                if (!empty($ob['previous_positions'])) {
                    $decoded = is_array($ob['previous_positions']) ? $ob['previous_positions'] : json_decode($ob['previous_positions'], true);
                    if (is_array($decoded)) {
                        $additionalPositions = $decoded;
                    }
                }

                if (isset($personIndex[$lookupKey])) {
                    $idx = $personIndex[$lookupKey];
                    $leaders[$idx]['all_positions'][] = $primaryPos;
                    foreach ($additionalPositions as $pos) {
                        $leaders[$idx]['all_positions'][] = $pos;
                    }
                    if (empty($leaders[$idx]['image']) && !empty($ob['image'])) {
                        $leaders[$idx]['image'] = $ob['image'];
                    }
                } else {
                    $entry = $ob;
                    $entry['all_positions'] = array_merge([$primaryPos], $additionalPositions);
                    $personIndex[$lookupKey] = count($leaders);
                    $leaders[] = $entry;
                }
            }
        }

        // Deduplicate and sort positions for each leader
        $filteredLeaders = [];
        foreach ($leaders as $leader) {
            $seenPos = [];
            $uniquePositions = [];
            foreach ($leader['all_positions'] as $pos) {
                if (isset($pos['is_enabled']) && ((int)$pos['is_enabled'] === 0 || $pos['is_enabled'] === '0' || $pos['is_enabled'] === false)) {
                    continue;
                }
                if (empty($pos['designation']) && empty($pos['year'])) continue;
                $pDesig = trim((string)$pos['designation']);
                $pYear = isset($pos['year']) ? trim((string)$pos['year']) : '';
                $pLevel = isset($pos['level']) ? trim((string)$pos['level']) : 'State';
                $pSec = isset($pos['section_heading']) ? trim((string)$pos['section_heading']) : '';
                $pPos = isset($pos['position']) && is_numeric($pos['position']) ? (int)$pos['position'] : 25;

                $fp = mb_strtolower("{$pDesig}|{$pYear}|{$pLevel}|{$pSec}");
                if (!isset($seenPos[$fp])) {
                    $seenPos[$fp] = true;
                    $uniquePositions[] = [
                        'designation' => $pDesig,
                        'year' => $pYear,
                        'level' => $pLevel,
                        'section_heading' => $pSec,
                        'position' => $pPos
                    ];
                }
            }

            if (empty($uniquePositions)) {
                continue;
            }

            // Sort positions: latest period first (e.g. 2012-2015 before 2008-2010),
            // and within same period, sort by rank order position (1 = highest / President, 2, 3...)
            usort($uniquePositions, function($a, $b) {
                preg_match_all('/\b(19\d\d|20\d\d)\b/', $a['year'], $mA);
                preg_match_all('/\b(19\d\d|20\d\d)\b/', $b['year'], $mB);
                $yrA = !empty($mA[0]) ? (int)end($mA[0]) : 0;
                $yrB = !empty($mB[0]) ? (int)end($mB[0]) : 0;
                if ($yrA !== $yrB) {
                    return $yrB - $yrA;
                }
                $posA = isset($a['position']) ? (int)$a['position'] : 25;
                $posB = isset($b['position']) ? (int)$b['position'] : 25;
                return $posA - $posB;
            });

            $leader['all_positions'] = $uniquePositions;
            $filteredLeaders[] = $leader;
        }

        $content['former_leaders'] = $filteredLeaders;
        $this->load->view('header');
        $this->load->view('home/former_leaders', $content);
        $this->load->view('footer');
    }

    public function memorandums() {
        $this->load->view('header');
        $this->load->view('home/memorandums');
        $this->load->view('footer');
    }

    public function membership_magazine() {
        $this->load->view('header');
        $this->load->view('home/membership_magazine');
        $this->load->view('footer');
    }


    function siteVisitors() {
        $data['count'] = $this->FlashNews_model->getSiteVisitorsCount();
        $data['code'] = 'success';
        echo json_encode($data);
        exit;
    }

}
