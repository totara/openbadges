<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the manual badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Manual badge award criteria
 *
 */
class award_criteria_manual extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_MANUAL] */
    public $criteriatype = BADGE_CRITERIA_TYPE_MANUAL;

    /* @var array Parameters for criteria */
    public $params = array();

    protected $required_params = array('roleid');
    protected $optional_params = array();

    public function __construct($record) {
        parent::__construct($record);
        $this->params = self::get_params($record['id']);
    }

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_display(&$mform, $data = null) {
        //@TODO
    }

    /**
     * Save the criteria information stored in the database
     *
     * @param stdClass $data Form data
     */
    public function save(&$data) {
        global $DB;

    }

    /**
     * Return criteria name
     *
     * @return string
     */
    public function get_title() {
        return get_string('criteria_type_manual', 'badges');
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        return false;

    }

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records of manual award.
        $sql = "SELECT bm.id
                FROM {badge_criteria_param} bp
                    INNER JOIN {badge_manual_award} bm
                        ON bm.paramid = bp.id
                WHERE bp.critid = :critid ";
        $list = $DB->get_fieldset_sql($sql, array('critid' => $this->id));
        $DB->delete_records_list('badge_manual_award', 'id', $list);

        parent::delete();
    }
}
