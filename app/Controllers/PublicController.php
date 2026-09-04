<?php

namespace App\Controllers;

/**
 * Ported from CodeIgniter 3's Public_Controller.
 *
 * Resolves the heading background image for the current page and shares it
 * with every view, as before.
 */
class PublicController extends AppController
{
    protected string $ci3View = 'public';


    public function ci3Init(): void {
        parent::ci3Init();

        $this->load->model('Slider_model');

        // Candidate page keys for the heading background, most specific first:
        // "download/forms" style keys win over the bare "download" section, and
        // a detail page (gallery/<album>) still falls back to its section.
        $pages = array();
        $seg1 = $this->uri->segment(1);
        $seg2 = $this->uri->segment(2);
        if ($seg1) {
            if ($seg2 && !is_numeric($seg2)) {
                $pages[] = $seg1 . '/' . $seg2;
            }
            $pages[] = $seg1;
        }

        $headingBgResult = $this->Slider_model->getHeadingBgForPage($pages);
        $headingBgImage = !empty($headingBgResult) ? base_url('uploads/slider/' . $headingBgResult['image']) : '';
        $this->load->vars(['heading_bg_image' => $headingBgImage]);
    }

    public function httpify($link, $append = 'http://', $allowed = array('http://', 'https://')) {
        $link = trim($link);

        if (empty($link)) {
            return false;
        }

        $found = false;
        foreach ($allowed as $protocol) {
            if (strpos($link, $protocol) !== 0) {
                $found = true;
            }
        }

        if ($found) {
            return $link;
        }
        return $append . $link;
    }
}
