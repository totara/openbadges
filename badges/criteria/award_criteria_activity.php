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

    public $required_param = 'module';
    public $optional_params = array('bydate');

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
        global $OUTPUT;
        $prefix = 'criteria-' . $this->id;
        $aggregation_methods = $data->get_aggregation_methods();

        $editurl = new moodle_url('/badges/criteria_action.php', array('badgeid' => $this->badgeid, 'edit' => true, 'type' => $this->criteriatype, 'crit' => $this->id));
        $deleteurl = new moodle_url('/badges/criteria_action.php', array('badgeid' => $this->badgeid, 'delete' => true, 'type' => $this->criteriatype));
        $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')), null, array('class' => 'criteria-action'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        // Criteria aggregation.
        $mform->addElement('header', $prefix, '');
        $mform->addElement('html', html_writer::tag('div', $deleteaction . $editaction, array('class' => 'criteria-header')));
        $mform->addElement('html', $OUTPUT->heading_with_help($this->get_title(), 'criteria_' . BADGE_CRITERIA_TYPE_ACTIVITY, 'badges'));
        if (!empty($this->params) && count($this->params) > 1) {
            $mform->addElement('select', $prefix . '-aggregation', get_string('aggregationmethod', 'badges'), $aggregation_methods);
            $mform->setDefault($prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_ACTIVITY));
        } else {
            $mform->addElement('hidden', $prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_ACTIVITY));
            $mform->setType($prefix . '-aggregation', PARAM_INT);
        }

        // Add existing parameters to the form.
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $this->config_form_criteria_param($mform, $param);
            }
        }
    }

    /**
     * Gets the module instance from the database and returns it.
     * If no module instance exists this function returns false.
     *
     * @return stdClass|bool
     */
    private function get_mod_instance($cmid) {
        global $DB;
        $rec = $DB->get_record_sql("SELECT md.name
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid));

        if ($rec) {
            return get_coursemodule_from_id($rec->name, $cmid);
        } else {
            return null;
        }
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param(&$mform, $param) {
        global $OUTPUT;
        $prefix = 'criteria-' . $this->id;

        $params = array(
                'badgeid' => $this->badgeid,
                'crit' => $this->id,
                'param' => 'module_' . $param['module'],
                'type' => $this->criteriatype
        );
        $url = new moodle_url('/badges/criteria_action.php', $params);
        $delete = $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        $parameter = array();
        $mod = self::get_mod_instance($param['module']);
        if (!$mod) {
            $parameter[] =& $mform->createElement('static', $prefix . '-complby_' . $param['module'], null,
                    $OUTPUT->error_text(get_string('error:missingmodule', 'badges')));
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['module'], null, $delete);
            $mform->addGroup($parameter, $prefix . 'param' . $param['module'], $OUTPUT->error_text(get_string('error')), array(' '), false);
        } else {
            $parameter[] =& $mform->createElement('static', $prefix . '-complby_' . $param['module'], null, get_string('bydate', 'badges'));
            $parameter[] =& $mform->createElement('date_selector', $prefix . '-bydate_' . $param['module'], "", array('optional' => true));
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['module'], null, $delete);
            $mform->addGroup($parameter, $prefix . '-param' . $param['module'], ucfirst($mod->modname) . ' - ' . $mod->name, array(' '), false);

            // Set existing values.
            if (isset($param['bydate'])) {
                $mform->setDefault($prefix . '-bydate_' . $param['module'], $param['bydate']);
            }

            if (isset($param['grade'])) {
                $mform->setDefault($prefix . '-grade_' . $param['module'], $param['grade']);
            }
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
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $mod = self::get_mod_instance($p['module']);
            if (!$mod) {
                $str = $OUTPUT->error_text(get_string('error:nosuchmod', 'badges'));
            } else {
                $str = html_writer::tag('b', '"' . ucfirst($mod->modname) . ' - ' . $mod->name . '"');
                if (isset($p['bydate'])) {
                    $str .= get_string('criteria_descr_bydate', 'badges', userdate($p['bydate'], get_string('strftimedate', 'core_langconfig')));
                }
            }
            $output[] = $str;
        }
        return html_writer::alist($output, array(), 'ul');
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
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options() {
        global $DB;
        $options = "";
        $none = true;
        $exisiting = array();

        $course = $DB->get_record('course', array('id' => $this->courseid));
        $info = new completion_info($course);
        $mods = $info->get_activities();

        // If it is an existing criterion, show only available params.
        if ($this->id !== 0) {
            $exisiting = array_keys($this->params);
        }

        if (!empty($mods)) {
            foreach ($mods as $mod) {
                if (!in_array($mod->id, $exisiting)) {
                    $options .= html_writer::checkbox('options[]', $mod->id, false, ucfirst($mod->modname) . ' - ' . $mod->name) . '<br/>';
                    $none = false;
                }
            }
        }
        return array($none, $options, get_string('noparamstoadd', 'badges'));
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

        $overall = null;
        foreach ($this->params as $param) {
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