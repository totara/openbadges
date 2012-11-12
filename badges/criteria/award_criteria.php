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
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_PROFILE', 5);

/**
 * Criteria type constant to class name mapping
*/
global $BADGE_CRITERIA_TYPES;
$BADGE_CRITERIA_TYPES = array(
    BADGE_CRITERIA_TYPE_OVERALL  => 'overall',
    BADGE_CRITERIA_TYPE_ACTIVITY => 'activity',
    BADGE_CRITERIA_TYPE_MANUAL   => 'manual',
    BADGE_CRITERIA_TYPE_SOCIAL   => 'social',
    BADGE_CRITERIA_TYPE_COURSE   => 'course',
    BADGE_CRITERIA_TYPE_PROFILE  => 'profile'
);

/**
 * Award criteria abstract definition
 *
 */
abstract class award_criteria {
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
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform Moodle forms object
     * @param mixed $data optional Any additional data that can be used to set default values in the form
     */
    abstract public function config_form_display(&$mform, $data = null);
}