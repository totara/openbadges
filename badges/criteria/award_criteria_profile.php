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
 * This file contains the profile completion badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/user/lib.php");

/**
 * Profile completion badge award criteria
 *
 */
class award_criteria_profile extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_PROFILE] */
    public $criteriatype = BADGE_CRITERIA_TYPE_PROFILE;

    public $required_param = 'field';
    public $optional_params = array();

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_criteria(&$mform, $data = null) {
        global $DB, $OUTPUT;
        $prefix = 'criteria-' . $this->id;

        $aggregation_methods = $data->get_aggregation_methods();

        $editurl = new moodle_url('/badges/criteria_action.php', array('badgeid' => $this->badgeid, 'edit' => true, 'type' => $this->criteriatype, 'crit' => $this->id));
        $deleteurl = new moodle_url('/badges/criteria_action.php', array('badgeid' => $this->badgeid, 'delete' => true, 'type' => $this->criteriatype));
        $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')), null, array('class' => 'criteria-action'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        // Criteria aggregation.
        $mform->addElement('header', $prefix, '');
        $mform->addElement('html', html_writer::tag('div', $deleteaction . $editaction, array('class' => 'criteria-header')));
        $mform->addElement('html', $OUTPUT->heading_with_help($this->get_title(), 'criteria_' . BADGE_CRITERIA_TYPE_PROFILE, 'badges'));
        if (!empty($this->params) && count($this->params) > 1) {
            $mform->addElement('select', $prefix . '-aggregation', get_string('aggregationmethod', 'badges'), $aggregation_methods);
            $mform->setDefault($prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_PROFILE));
        } else {
            $mform->addElement('hidden', $prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_PROFILE));
            $mform->setType($prefix . '-aggregation', PARAM_INT);
        }

        // Add existing fields to the form.
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $this->config_form_criteria_param($mform, $param);
            }
        }
    }

    /**
     * Add custom profile fields to the form.
     *
     */
    private function get_custom_fields(array $existing, &$none) {
        global $CFG, $DB;
        $html = "";

        $sql = "SELECT uf.id as fieldid, uf.name as name, ic.id as categoryid, ic.name as categoryname, uf.datatype
                FROM {user_info_field} uf
                JOIN {user_info_category} ic
                ON uf.categoryid = ic.id AND uf.visible <> 0
                ORDER BY ic.sortorder ASC, uf.sortorder ASC";

        if ( $fields = $DB->get_records_sql($sql)) {
            foreach ($fields as $field) {
                if (!isset($currentcat) || $currentcat != $field->categoryid) {
                    $currentcat = $field->categoryid;
                    $html .= html_writer::nonempty_tag('h3', format_string($field->categoryname));
                }
                if (!in_array($field, $existing)) {
                    $html .= html_writer::checkbox('options[]', $field->fieldid, false, $field->name) . '<br/>';
                    $none = false;
                }
            }
        }

        return $html;
    }

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options() {
        $options = "";
        $none = true;
        $existing = array();

        // Note: cannot use user_get_default_fields() here because it is not possible to decide which fields user can modify.
        $fields = array('firstname', 'lastname', 'email', 'address', 'phone1', 'phone2',
                        'icq', 'skype', 'yahoo', 'aim', 'msn', 'department', 'institution',
                        'interests', 'description', 'city', 'url', 'country');
        // If it is an existing criterion, show only available params.
        if ($this->id !== 0) {
            $existing = array_keys($this->params);
        }

        if (!empty($fields)) {
            $options .= html_writer::nonempty_tag('h3', get_string('default', 'badges'));
            foreach ($fields as $field) {
                if (!in_array($field, $existing)) {
                    $options .= html_writer::checkbox('options[]', $field, false, get_user_field_name($field)) . '<br/>';
                    $none = false;
                }
            }
        }

        // Load available to users custom profile fields.
        $options .= $this->get_custom_fields($existing, $none);

        return array($none, $options, get_string('noparamstoadd', 'badges'));
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_form_criteria_param(&$mform, $param) {
        global $OUTPUT, $DB;
        $prefix = 'criteria-' . $this->id;

        $params = array(
                'badgeid' => $this->badgeid,
                'crit' => $this->id,
                'param' => 'field_' . $param['field'],
                'type' => $this->criteriatype
        );
        $url = new moodle_url('/badges/criteria_action.php', $params);
        $delete = $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        $parameter = array();
        if (is_numeric($param['field'])) {
            $field = $DB->get_field('user_info_field', 'name', array('id' => $param['field']));
        } else {
            $field = get_user_field_name($param['field']);
        }
        if (!$field) {
            $parameter[] =& $mform->createElement('static', $prefix . '-field_' . $param['field'], null,
                    $OUTPUT->error_text(get_string('error:missingfield', 'badges')));
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['field'], null, $delete);
            $mform->addGroup($parameter, $prefix . 'param' . $param['field'], $OUTPUT->error_text(get_string('error')), array(' '), false);
        } else {
            $parameter[] =& $mform->createElement('static', $prefix . '-field_' . $param['field'], null, $field);
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['field'], null, $delete);
            $mform->addGroup($parameter, $prefix . '-param' . $param['field'], '', array(' '), false);
        }
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
            if (is_numeric($p['field'])) {
                $str = $DB->get_field('user_info_field', 'name', array('id' => $p['field']));
            } else {
                $str = get_user_field_name($p['field']);
            }
            if (!$str) {
                $output[] = $OUTPUT->error_text(get_string('error:nosuchfield', 'badges'));
            } else {
                $output[] = $str;
            }
        }
        return html_writer::alist($output, array(), 'ul');
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;

        $overall = null;
        foreach ($this->params as $param) {
            if (is_numeric($param['field'])) {
                $crit = $DB->get_field('user_info_data', 'data', array('userid' => $userid, 'fieldid' => $param['field']));
            } else {
                $crit = $DB->get_field('user', $param['field'], array('id' => $userid));
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (!$crit) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if (!$crit) {
                    $overall = false;
                    continue;
                } else {
                    return true;
                }
            }
        }
        return $overall;
    }
}