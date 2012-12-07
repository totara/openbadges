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
 * This file contains the overall badge award criteria type
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Overall badge award criteria
 *
 */
class award_criteria_overall extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_OVERALL] */
    public $criteriatype = BADGE_CRITERIA_TYPE_OVERALL;

    public function __construct($record) {
        parent::__construct($record);
    }

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_criteria(&$mform, $data = null) {
        global $DB, $OUTPUT;
        $aggregation_methods = $data->get_aggregation_methods();

        $output = html_writer::start_tag('div', array('id' => 'criteria-type-' . BADGE_CRITERIA_TYPE_OVERALL, 'class' => 'criteria-type'));

        // Aggregation choice.
        $agg = html_writer::label(get_string('aggregationmethod', 'badges'), 'menuagg');
        $agg .= html_writer::select($aggregation_methods, 'agg', 'all', false);
        $aggregatecrit = $OUTPUT->container($agg, 'criteria-aggregation', 'aggregate-criteria-type-' . BADGE_CRITERIA_TYPE_OVERALL);
        $output .= $aggregatecrit . html_writer::end_tag('div');

        return $output;
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param($param) {
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
        return get_string('criteria_' . $this->criteriatype, 'badges');
    }

    /**
     * Review this criteria and decide if it has been completed
     * Overall criteria review should be called only from other criteria handlers.
     *  
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;

        $sql = "SELECT * FROM {badge_criteria} bc 
                LEFT JOIN {badge_criteria_met} bcm 
                    ON bc.id = bcm.critid AND bcm.userid = :userid 
                WHERE bc.badgeid = :badgeid
                    AND bc.criteriatype != :criteriatype ";

        $params = array(
                    'userid' => $userid,
                    'badgeid' => $this->badgeid,
                    'criteriatype' => BADGE_CRITERIA_TYPE_OVERALL
                );

        $criteria = $DB->get_records_sql($sql, $params);
        $overall = null;
        foreach ($criteria as $crit) {
            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if ($crit->datemet === null) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if ($crit->datemet === null) {
                    $overall = false;
                    continue;
                } else {
                    return true;
                }
            }
        }

        return $overall;
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid) { }
}