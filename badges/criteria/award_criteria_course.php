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
 * This file contains the course completion badge award criteria type class
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
 * Badge award criteria -- award on course completion
 *
 */
class award_criteria_course extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_COURSE] */
    public $criteriatype = BADGE_CRITERIA_TYPE_COURSE;

    /* @var array Parameters of course criteria */
    public $params = array();

    public $required_param = 'course';
    public $optional_params = array('grade', 'bydate');

    public function __construct($record) {
        parent::__construct($record);
        if (isset($record['id'])) {
            $this->params = self::get_params($record['id']);
        }
    }

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_criteria(&$mform, $data = null) {
        global $OUTPUT;
        $prefix = 'criteria-' . $this->id;

        $deleteurl = new moodle_url('/badges/criteria_action.php', array('badgeid' => $this->badgeid, 'type' => $this->criteriatype, 'delete' => true));
        $deleteaction = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));
        $mform->addElement('header', $prefix, '');
        $mform->addElement('html', html_writer::tag('div', $deleteaction, array('class' => 'criteria-header')));
        $mform->addElement('html', $OUTPUT->heading_with_help($this->get_title(), 'criteria_' . BADGE_CRITERIA_TYPE_COURSE, 'badges'));
        $mform->addElement('html', $OUTPUT->heading(get_string('coursecompletion', 'badges'), 4));

        // Existing parameters.
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $this->config_form_criteria_param($mform, $param);
            }
        }
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param(&$mform, $param) {
        $prefix = 'criteria-' . $this->id;
        $parameter = array();
        $parameter[] =& $mform->createElement('static', $prefix . '-grade', null, get_string('mingrade', 'badges'));
        $parameter[] =& $mform->createElement('text', $prefix . '-grade_' . $param['course'], '', array('size' => '5'));
        $parameter[] =& $mform->createElement('static', $prefix . '-complby_' . $param['course'], null, get_string('bydate', 'badges'));
        $parameter[] =& $mform->createElement('date_selector', $prefix . '-bydate_' . $param['course'], "", array('optional' => true));
        $mform->addGroup($parameter, $prefix . '-course' . $param['course'], "Optional requirements: ", array(' '), false);

        // Set existing values.
        if (isset($param['bydate'])) {
            $mform->setDefault($prefix . '-bydate_' . $param['course'], $param['bydate']);
        }

        if (isset($param['grade'])) {
            $mform->setDefault($prefix . '-grade_' . $param['course'], $param['grade']);
        }
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
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details() {
        return "";
    }

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options() {
        global $PAGE, $DB;
        $course = $DB->get_record('course', array('id' => $PAGE->course->id));
        $options = html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'options[]', 'value' => $PAGE->course->id));
        $options .= get_string('noparamstoadd', 'badges');
        if (!($course->enablecompletion == COMPLETION_ENABLED)) {
            $none = true;
            $message = get_string('completionnotenabled', 'badges');
        } else {
            $none = false;
            $message = '';
        }
        return array($none, $options, $message);
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
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

            if ($info->is_course_complete($userid) && $check_grade && $check_date) {
                return true;
            }
        }

        return false;
    }
}