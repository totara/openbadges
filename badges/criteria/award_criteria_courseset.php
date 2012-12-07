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
 * This file contains the courseset completion badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once('award_criteria_course.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Badge award criteria -- award on courseset completion
 *
 */
class award_criteria_courseset extends award_criteria_course {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_COURSESET] */
    public $criteriatype = BADGE_CRITERIA_TYPE_COURSESET;

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_criteria(&$mform, $data = null) {
        global $DB, $OUTPUT;
        $aggregation_methods = $data->get_aggregation_methods();

        $output = html_writer::start_tag('div', array('id' => 'criteria-type-' . BADGE_CRITERIA_TYPE_COURSESET, 'class' => 'criteria-type'));

        // Aggregation choice.
        $agg = html_writer::label(get_string('aggregationmethod', 'badges'), 'menuagg');
        $agg .= html_writer::select($aggregation_methods, 'agg', 'all', false);
        $aggregatecrit = $OUTPUT->container($agg, 'criteria-aggregation', 'aggregate-criteria-type-' . BADGE_CRITERIA_TYPE_COURSESET);

        // Delete criteria button.
        $deletecrit  = html_writer::start_tag('div', array('class'=>'comment-delete'));
        $deletecrit .= html_writer::start_tag('a', array('href' => '#', 'id' => 'remove-criteria-type-' . BADGE_CRITERIA_TYPE_COURSESET));
        $deletecrit .= $OUTPUT->pix_icon('t/delete', get_string('delete'));
        $deletecrit .= html_writer::end_tag('a');
        $deletecrit .= html_writer::end_tag('div');
        $output .= $deletecrit . $OUTPUT->heading_with_help('Course set criteria type', 'variablesubstitution', 'badges') . $aggregatecrit;

        // Existing parameters.
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $output .= $this->config_form_criteria_param($param);
            }
        }

        // Add more parameters button.
        $addmore = html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('addparameter', 'badges'), 'id' => 'add-param'));
        $output .= $addmore . html_writer::end_tag('div');
        return $output;
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param($param) {
        return "";
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
        return get_string('criteria_type_courseset', 'badges');
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;
        foreach ($this->params as $param) {
            $course = $DB->get_record('course', array('id' => $param['courseid']));
            $info = new completion_info($course);
            $check_grade = true;
            $check_date = true;

            if (isset($param['grade'])) {
                $grade = grade_get_course_grade($userid, $course->id);
                $check_grade = ($grade->grade >= $param['grade']);
            }

            if (isset($param['bydate'])) {
                $cparams = array(
                        'userid' => $userid,
                        'course' => $course->id,
                );
                $completion = new completion_completion($cparams);
                $date = $completion->timecompleted;
                $check_date = ($date <= $param['bydate']);
            }

            $overall = null;
            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if ($info->is_course_complete($userid) && $check_grade && $check_date) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if ($info->is_course_complete($userid) && $check_grade && $check_date) {
                    return true;
                } else {
                    $overall = false;
                    continue;
                }
            }
        }

        return $overall;
    }
}