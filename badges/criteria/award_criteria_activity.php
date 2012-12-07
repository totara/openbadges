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
 * This file contains the activity badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Badge award criteria -- award on activity completion
 *
 */
class award_criteria_activity extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_ACTIVITY] */
    public $criteriatype = BADGE_CRITERIA_TYPE_ACTIVITY;

    public $params = array();
    private $courseid;

    protected $required_params = array('module');
    protected $optional_params = array('bydate');

    public function __construct($record) {
        parent::__construct($record);
        if (isset($record['id'])) {
            $this->params = self::get_params($record['id']);
        }
        $this->courseid = self::get_course();
    }

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_criteria(&$mform, $data = null) {
        global $DB, $OUTPUT;

        $course = $DB->get_record('course', array('id' => $this->courseid));
        $info = new completion_info($course);
        $mods = $info->get_activities();

        $aggregation_methods = $data->get_aggregation_methods();

        $output = html_writer::start_tag('div', array('id' => 'criteria-type-' . BADGE_CRITERIA_TYPE_ACTIVITY, 'class' => 'criteria-type'));

        // Aggregation choice.
        $agg = html_writer::label(get_string('aggregationmethod', 'badges'), 'menuagg');
        $agg .= html_writer::select($aggregation_methods, 'agg', 'all', false);
        $aggregatecrit = $OUTPUT->container($agg, 'criteria-aggregation', 'aggregate-criteria-type-' . BADGE_CRITERIA_TYPE_ACTIVITY);

        // Delete criteria button.
        $deletecrit  = html_writer::start_tag('div', array('class'=>'comment-delete'));
        $deletecrit .= html_writer::start_tag('a', array('href' => '#', 'id' => 'remove-criteria-type-' . BADGE_CRITERIA_TYPE_ACTIVITY));
        $deletecrit .= $OUTPUT->pix_icon('t/delete', get_string('delete'));
        $deletecrit .= html_writer::end_tag('a');
        $deletecrit .= html_writer::end_tag('div');
        $output .= $deletecrit . $OUTPUT->heading_with_help('Activity criteria type', 'variablesubstitution', 'badges') . $aggregatecrit;

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
     * Gets the module instance from the database and returns it.
     * If no module instance exists this function returns false.
     *
     * @return stdClass|bool
     */
    public function get_mod_instance($cmid) {
        global $DB;
        $rec = $DB->get_record_sql("SELECT md.name
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid));
        return get_coursemodule_from_id($rec->name, $cmid);
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param($param) {
        global $DB, $OUTPUT;

        $mod = self::get_mod_instance($param['module']);
        $output = html_writer::start_tag('div', array('id' => 'criteria-param-' . $mod->id, 'class' => 'criteria-param'));
        $output .= html_writer::label(ucfirst($mod->modname) . ' - ' . $mod->name, null, false, array('class' => 'param-name'));
        $output .= html_writer::label(get_string('bydate', 'badges'), null, false);
        $dayselector = html_writer::select_time('days', '');
        $monthselector = html_writer::select_time('months', '');
        $yearselector = html_writer::select_time('years', '');

        $output .= $dayselector . $monthselector . $yearselector;

        $deleteparam  = html_writer::start_tag('div', array('class'=>'comment-delete'));
        $deleteparam .= html_writer::start_tag('a', array('href' => '#', 'id' => 'remove-criteria-param-' . $mod->id));
        $deleteparam .= $OUTPUT->pix_icon('t/delete', get_string('delete'));
        $deleteparam .= html_writer::end_tag('a');
        $deleteparam .= html_writer::end_tag('div');
        $output .= $deleteparam . html_writer::end_tag('div');
        return $output;
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
     * Return course ID for activities
     *
     * @return int
     */
    private function get_course() {
        global $DB;
        $courseid = $DB->get_field('badge', 'courseid', array('id' =>$this->badgeid));
        return $courseid;
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;
        $completionstates = array(COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS);
        $course = $DB->get_record('course', array('id' => $this->courseid));
        $info = new completion_info($course);

        foreach ($this->params as $param) {
            $overall = null;
            $cm = new stdClass();
            $cm->id = $param['module'];

            $data = $info->get_data($cm, false, $userid);
            $check_date = true;

            if (isset($param['bydate'])) {
                $date = $data->timemodified;
                $check_date = ($date <= $param['bydate']);
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (in_array($data->completionstate, $completionstates) && $check_date) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if (in_array($data->completionstate, $completionstates) && $check_date) {
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