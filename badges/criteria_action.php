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
 * Processing actions with badge criteria.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = optional_param('badgeid', 0, PARAM_INT); // Badge ID.
$type    = optional_param('type', 0, PARAM_INT); // Criteria type.
$edit    = optional_param('edit', 0, PARAM_INT); // Edit criteria ID.
$crit    = optional_param('crit', 0, PARAM_INT); // Criteria ID for managing params.
$param   = optional_param('param', '', PARAM_TEXT); // Param name for managing params.
$delete  = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$add     = optional_param('add', 0, PARAM_BOOL);

require_login();

$return = new moodle_url('/badges/criteria.php', array('id' => $badgeid));
$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->context));

// Make sure that no actions available for locked or active badges.
if ($badge->is_active() || $badge->is_locked()) {
    redirect($return);
}

if ($badge->context == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->context, 'id' => $badge->courseid));
}

$PAGE->set_context($context);
$PAGE->set_url('/badges/criteria_action.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);
navigation_node::override_active_url($navurl);

if ($delete && has_capability('moodle/badges:configurecriteria', $context)) {
    if (!$confirm || !confirm_sesskey()) {
        $optionsyes = array('confirm' => 1, 'sesskey' => sesskey(), 'badgeid' => $badgeid, 'delete' => true, 'type' => $type);

        $strdeletecheckfull = get_string('delcritconfirm', 'badges');

        echo $OUTPUT->header();
        $formcontinue = new single_button(new moodle_url('/badges/criteria_action.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
        echo $OUTPUT->footer();

        die();
    }

    if (count($badge->criteria) == 2) {
        // Remove overall criterion as well.
        $badge->criteria[$type]->delete();
        $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->delete();
    } else {
        $badge->criteria[$type]->delete();
    }
    redirect($return);
} else if (!empty($crit) && !empty($param) && has_capability('moodle/badges:configurecriteria', $context)) {
    if (!$confirm || !confirm_sesskey()) {
        $optionsyes = array('confirm' => 1, 'sesskey' => sesskey(), 'badgeid' => $badgeid, 'crit' => $crit, 'param' => $param, 'type' => $type);

        $strdeletecheckfull = get_string('delparamconfirm', 'badges');

        echo $OUTPUT->header();
        $formcontinue = new single_button(new moodle_url('/badges/criteria_action.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
        echo $OUTPUT->footer();

        die();
    }

    if (count($badge->criteria[$type]->params) == 1) {
        // Remove entire criterion when the last param is removed.
        $badge->criteria[$type]->delete();
    } else {
        $p = explode('_', $param);
        $DB->delete_records('badge_criteria_param', array('critid' => $crit, 'name' => $param));
        foreach ($badge->criteria[$type]->optional_params as $opt) {
            $DB->delete_records('badge_criteria_param', array('critid' => $crit, 'name' => $opt . "_" . end($p)));
        }
    }
    redirect($return);
} else if (($add || $edit) && has_capability('moodle/badges:configurecriteria', $context)) {
    require_once($CFG->libdir . '/formslib.php');
    $cparams = array('criteriatype' => $type, 'badgeid' => $badge->id);
    if ($edit) {
        $cparams['id'] = $crit;
    }
    $criteria = award_criteria::build($cparams);

    $options = optional_param_array('options', array(), PARAM_TEXT);
    $goback = optional_param('back', "", PARAM_TEXT);
    if (!empty($goback)) {
        redirect($return);
    }
    if (!empty($options)) {
        require_sesskey();
        // If no criteria yet, add overall aggregation.
        if (count($badge->criteria) == 0) {
            $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
            $criteria_overall->save(array());
        }
        $criteria->save($options);
        redirect($return);
    }

    $urlparams = array('badgeid' => $badgeid, 'add' => $add, 'edit' => $edit, 'type' => $type, 'crit' => $crit);
    $mform = new MoodleQuickForm('preferences',
            'post',
            new moodle_url('/badges/criteria_action.php', $urlparams),
            '',
            array('class' => 'preferences boxaligncenter boxwidthwide'));

    list($none, $options, $message) = $criteria->get_options();

    $mform->addElement('hidden', 'sesskey', sesskey());
    $mform->addElement('header', 'ghoptions', get_string('additionalparameters', 'badges'));
    if ($none) {
        $mform->addElement('html', html_writer::tag('div', $message));
        $mform->addElement('submit', 'back', get_string('back'));
    } else {
        $mform->addElement('html', html_writer::tag('div', $options));
        $buttonarray = array();
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('proceed', 'badges'));
        $buttonarray[] =& $mform->createElement('submit', 'back', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }

    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();

    die();
}

//redirect($return);