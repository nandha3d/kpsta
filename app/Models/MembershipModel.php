<?php

namespace App\Models;

use App\Libraries\Ci3\Database\Connection as Ci3Connection;
use Config\Database;

/**
 * Ported from CodeIgniter 3's Membership_Model.
 *
 * Opens the 'membership' connection group and resolves the signed-in user's
 * position in the organisation hierarchy, which the membership models filter
 * every query by.
 */
abstract class MembershipModel extends Ci3Model
{
    public const AAUTH_GROUP_STATE          = 1;
    public const AAUTH_GROUP_DISTRICT       = 2;
    public const AAUTH_GROUP_EDUCATION_DIST = 3;
    public const AAUTH_GROUP_SUB_DIST       = 4;
    public const AAUTH_GROUP_BRANCH         = 5;
    public const AAUTH_GROUP_SCHOOL         = 6;
    public const AAUTH_GROUP_STATE_STAFF    = 7;

    public $aauthGroupId = false;
    public $aauthOfficeId = false;

    /** Declared so it is not resolved through Ci3Model::__get(). */
    public ?Ci3Connection $db = null;

    public $year = false;
    public $startedYear = 2017;

    public function __construct()
    {
        parent::__construct();

        $this->db = new Ci3Connection(Database::connect('membership'));

        $controller = get_instance();

        if ($controller !== null && isset($controller->session)) {
            $this->aauthGroupId  = $controller->session->userdata('group');
            $this->aauthOfficeId = $controller->session->userdata('office');
        }

        // State staff act on the State office's data.
        if ($this->aauthGroupId == self::AAUTH_GROUP_STATE_STAFF) {
            $this->aauthGroupId = self::AAUTH_GROUP_STATE;
        }
    }

    /**
     * Resolve the configured membership year.
     */
    public function getYear()
    {
        $controller = get_instance();

        if ($controller === null) {
            return $this->year;
        }

        $controller->load->model('membership/Config_model');
        $this->year = $controller->Config_model->getLabelValue('year');

        return $this->year;
    }
}
