<?php

namespace App\Controllers;

/**
 * Ported from CodeIgniter 3's MY_Controller.
 *
 * The admin access check, the Bootstrap pagination configuration and the
 * shared helpers (createSelectDropDown, runBatchDelete, getNewUrl, ...) are
 * unchanged -- the CI3 APIs they call are provided by the compatibility layer
 * in App\Libraries\Ci3.
 *
 * The one structural change is that the constructor became ci3Init(), which
 * BaseController calls once the request, response and session exist. The guard
 * clauses redirect() and rely on that halting the request, which the CI3-style
 * redirect() in app/Common.php still does.
 */
class AppController extends BaseController
{
    /**
     * Which access rules apply: "admin", "public" or "membership".
     *
     * CI3 passed this as a constructor argument. It is a property here so that
     * every ci3Init() in the hierarchy keeps an identical signature -- PHP does
     * not allow a subclass to drop a parameter the parent declares.
     */
    protected string $ci3View = 'admin';


    public $newUrl = null;
    public static $menuList = false;
    protected $CI = null;

    /**
     * constructor function
     */
    public function ci3Init(): void {
        parent::ci3Init();



        //check whether user is logged in
        if ($this->ci3View === "admin") {
            $this->load->library("Aauth");
            if (!$this->aauth->is_loggedin() || null !== $this->session->userdata('office')) {
                redirect('admin/logout');
            }

            //set menu for admin panel normal users.
            if ($this->session->userdata('group') <> 1) {
                //Check whether the user permitted to use this menu
                if (!$this->aauth->isMenuActive()) {
                    redirect('admin/logout');
                }

                $this->load->vars(array('adminMenuList' => $this->aauth->getAdminMenuList()));
            }


            //delete cached home page
            if (preg_match('(order-circular|news|gallery|quicklink|slider|reaction_gallery|flash_news|office_bearer)', $this->uri->uri_string()) === 1) {
                if (preg_match('(add|update|publish)', $this->uri->uri_string()) === 1) {
                    $this->deleteCachedPage('/home');
                    $this->deleteCachedPage('/');
                }
            }
        }
    }

    function BootsrapPaginationConfig() {
        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
//        $config['first_link'] = '<li  class="paginate_button previous ">';
        $config['last_link'] = false;

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last ';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';


        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        return $config;
    }

    public function getNewUrl($data = array(), $removeKey = array()) {
        $params = array();
        $urlString = base_url($this->uri->uri_string());

        $parsed = parse_url($urlString . '?' . $_SERVER['QUERY_STRING']);

        if (isset($parsed['query'])) {
            $query = $parsed['query'];
            parse_str($query, $params);
        }

        //Reset query string value
        foreach ($data as $key => $val) {
            if (isset($params[$key])) {
                $params[$key] = $val;
            }
        }

        //remove if any query string have null value
        foreach ($params as $key => $val) {
            if (empty($val) || isset(array_flip($removeKey)[$key])) {
                unset($params[$key]);
            }
        }



        if (count($params)) {
            $newUrl = $urlString . '?' . http_build_query($params);
        } else {
            $newUrl = $urlString;
        }

        return $newUrl;
    }

    public function deleteFile($file = false) {
        if ($file && is_file($file)) {
            try {
                unlink($file);
            } catch (\Exception $exc) {
                
            }
        }
        return true;
    }

    public function deleteCachedPage($url) {
        // Deletes cache for $url
        $this->output->delete_cache($url);
    }

    /**
     * This function used to create Dropdown data
     * @param type $data
     * @param type $property
     * @param type $defaultValue
     * @return type
     */
    public function createSelectDropDown($data = array(), $property = '', $defaultValue = false, $exclude = []) {
        if (!is_array($data)) {
            return $data;
        }

        $return = [];
        if ($defaultValue) {
            $return['0'] = $defaultValue;
        }
        foreach ($data as $row) {
            if(in_array($row['id'], $exclude)){
                continue;
            }
            $return[$row['id']] = $row[$property];
        }

        return $return;
    }

    /**
     * Shared plumbing for the "Delete Selected" toolbar button.
     *
     * The posted ids are handed one at a time to the caller's own single-row
     * delete, so per-controller cleanup (uploaded files, thumbnails) stays in
     * one place instead of being duplicated for the batch path.
     *
     * @param callable $deleteOne receives an id, returns TRUE when removed
     * @return array counts plus a human readable message
     */
    protected function runBatchDelete($deleteOne) {
        $ids = $this->input->post('ids');
        if (!is_array($ids)) {
            $ids = ($ids === NULL || $ids === '') ? array() : array($ids);
        }
        // ids arrive as strings from the form post
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $result = array('code' => 'error', 'deleted' => 0, 'failed' => 0);

        if (empty($ids)) {
            $result['message'] = 'No rows were selected.';
            return $result;
        }

        foreach ($ids as $id) {
            if (call_user_func($deleteOne, $id)) {
                $result['deleted']++;
            } else {
                $result['failed']++;
            }
        }

        $result['code'] = $result['deleted'] ? 'success' : 'error';
        $result['message'] = $result['deleted'] . ' item(s) deleted'
                . ($result['failed'] ? ', ' . $result['failed'] . ' could not be deleted' : '');

        return $result;
    }
}
