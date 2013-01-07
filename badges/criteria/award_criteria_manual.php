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

    public $required_param = 'role';
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
        $mform->addElement('html', $OUTPUT->heading_with_help($this->get_title(), 'criteria_' . BADGE_CRITERIA_TYPE_MANUAL, 'badges'));
        if (!empty($this->params) && count($this->params) > 1) {
            $mform->addElement('select', $prefix . '-aggregation', get_string('aggregationmethod', 'badges'), $aggregation_methods);
            $mform->setDefault($prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_MANUAL));
        } else {
            $mform->addElement('hidden', $prefix . '-aggregation', $data->get_aggregation_method(BADGE_CRITERIA_TYPE_MANUAL));
            $mform->setType($prefix . '-aggregation', PARAM_INT);
        }

        // Add existing roles to the form.
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $this->config_form_criteria_param($mform, $param);
            }
        }
    }

    /**
     * Gets role name.
     * If no such role exists this function returns null.
     *
     * @return string|null
     */
    private function get_role_name($rid) {
        global $DB, $PAGE;
        $rec = $DB->get_record('role', array('id' => $rid));

        if ($rec) {
            return role_get_name($rec, $PAGE->context, ROLENAME_ALIAS);
        } else {
            return null;
        }
    }

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options() {
        global $PAGE;
        $options = "";
        $none = true;
        $roles = get_roles_with_capability('moodle/badges:awardbadge', CAP_ALLOW, $PAGE->context);
        $existing = array();
        // If it is an existing criterion, show only available params.
        if ($this->id !== 0) {
            $existing = array_keys($this->params);
        }

        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (!in_array($role->id, $existing)) {
                    $options .= html_writer::checkbox('options[]', $role->id, false, self::get_role_name($role->id)) . '<br/>';
                    $none = false;
                }
            }
        }
        return array($none, $options, get_string('noparamstoadd', 'badges'));
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
                'param' => 'role_' . $param['role'],
                'type' => $this->criteriatype
                );
        $url = new moodle_url('/badges/criteria_action.php', $params);
        $delete = $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        $parameter = array();
        $role = self::get_role_name($param['role']);
        if (!$role) {
            $parameter[] =& $mform->createElement('static', $prefix . '-role_' . $param['role'], null,
                    $OUTPUT->error_text(get_string('error:missingrole', 'badges')));
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['role'], null, $delete);
            $mform->addGroup($parameter, $prefix . 'param' . $param['role'], $OUTPUT->error_text(get_string('error')), array(' '), false);
        } else {
            $parameter[] =& $mform->createElement('static', $prefix . '-role_' . $param['role'], null, $role);
            $parameter[] =& $mform->createElement('static', $prefix . '-action_' . $param['role'], null, $delete);
            $mform->addGroup($parameter, $prefix . '-param' . $param['role'], '', array(' '), false);
        }
    }

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details() {
        $output = array();
        foreach ($this->params as $p) {
            $str = self::get_role_name($p['role']);
            if (!$str) {
                $output[] = $OUTPUT->error_text(get_string('error:nosuchrole', 'badges'));
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
            $crit = $DB->get_record('badge_manual_award', array('issuerrole' => $param['role'], 'recipientid' => $userid));
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

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records of manual award.
        $DB->delete_records('badge_manual_award', array('badgeid' => $this->badgeid));

        parent::delete();
    }
}
