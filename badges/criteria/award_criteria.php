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
 * Badge award criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Role completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_OVERALL', 0);

/**
 * Activity completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('BADGE_CRITERIA_TYPE_ACTIVITY', 1);

/**
 * Duration completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('BADGE_CRITERIA_TYPE_MANUAL', 2);

/**
 * Grade completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('BADGE_CRITERIA_TYPE_SOCIAL', 3);

/**
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('BADGE_CRITERIA_TYPE_COURSE', 4);

/**
 * Courseset completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_COURSESET', 5);

/**
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_PROFILE', 6);

/**
 * Criteria type constant to class name mapping
*/
global $BADGE_CRITERIA_TYPES;
$BADGE_CRITERIA_TYPES = array(
    BADGE_CRITERIA_TYPE_OVERALL   => 'overall',
    BADGE_CRITERIA_TYPE_ACTIVITY  => 'activity',
    BADGE_CRITERIA_TYPE_MANUAL    => 'manual',
    BADGE_CRITERIA_TYPE_SOCIAL    => 'social',
    BADGE_CRITERIA_TYPE_COURSE    => 'course',
    BADGE_CRITERIA_TYPE_COURSESET => 'courseset',
    BADGE_CRITERIA_TYPE_PROFILE   => 'profile'
);

/**
 * Award criteria abstract definition
 *
 */
abstract class award_criteria {

    public $id;

    public $method;

    public $badgeid;

    /**
     * The base constructor
     *
     * @param array $params
     */
    public function __construct($params) {
        $this->id = isset($params['id']) ? $params['id'] : 0;
        $this->method = isset($params['method']) ? $params['method'] : BADGE_CRITERIA_AGGREGATION_ALL;
        $this->badgeid = $params['badgeid'];
    }

    /**
     * Factory method for creating criteria class object
     *
     * @param array $params associative arrays varname => value
     * @return award_criteria
     */
    public static function build($params) {
        global $CFG, $BADGE_CRITERIA_TYPES;

        if (!isset($params['criteriatype']) || !isset($BADGE_CRITERIA_TYPES[$params['criteriatype']])) {
            print_error('error:invalidcriteriatype', 'badges');
        }

        $class = 'award_criteria_' . $BADGE_CRITERIA_TYPES[$params['criteriatype']];
        require_once($CFG->dirroot . '/badges/criteria/' . $class . '.php');

        return new $class($params);
    }

    /**
     * Return criteria title
     *
     * @return string
     */
    abstract public function get_title();

    /**
     * Add appropriate criteria elemetnts to the form
     *
     */
    abstract public function config_form_criteria(&$mform, $data);

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    abstract public function config_form_criteria_param($data);

    /**
     * Save the criteria information stored in the database
     *
     * @param array $data Form data
     * @return void
     */
    abstract public function save(&$data);

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    abstract public function review($userid);

    /**
     * Mark this criteria as complete for a user
     *
     * @param int $userid User whose criteria is completed.
     */
    public function mark_complete($userid) {
        global $DB;
        $obj = array();
        $obj['critid'] = $this->id;
        $obj['userid'] = $userid;
        $obj['datemet'] = time();
        $DB->insert_record('badge_criteria_met', $obj);
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid){
        global $DB;
        $params = array();

        $records = $DB->get_records('badge_criteria_param', array('critid' => $cid));
        foreach ($records as $rec) {
            $arr = explode('_', $rec->name);
            $params[$arr[1]][$arr[0]] = $rec->value;
        }

        return $params;
    }

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records if it has already been met.
        $DB->delete_records('badge_criteria_met', array('critid' => $this->id));

        // Remove all parameters records.
        $DB->delete_records('badge_criteria_param', array('critid' => $this->id));

        // Finally remove criterion itself.
        $DB->delete_records('badge_criteria', array('id' => $this->id));
    }
}