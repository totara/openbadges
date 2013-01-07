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
 * Editing badge details, criteria, messages
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/criteria_form.php');

$badgeid = required_param('id', PARAM_INT);

require_login();

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->context));

if ($badge->context == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->context, 'id' => $badge->courseid));
}

$currenturl = qualified_me();

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);

// Set up navigation and breadcrumbs.
navigation_node::override_active_url($navurl);
$PAGE->navbar->add($badge->name);

$output = $PAGE->get_renderer('core', 'badges');
$msg = optional_param('msg', '', PARAM_TEXT);
$emsg = optional_param('emsg', '', PARAM_TEXT);

$form = new edit_criteria_form($currenturl, array('badge' => $badge));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/badges/overview.php', array('id' => $badgeid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    if ($badge->save_criteria($data)) {
        $msg = get_string('changessaved');
        redirect(new moodle_url('/badges/criteria.php', array('id' => $badgeid, 'msg' => $msg)));
    } else {
        $emsg = get_string('error:save', 'badges');
        redirect(new moodle_url('/badges/criteria.php', array('id' => $badgeid, 'emsg' => $emsg)));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($badge->name . ': ' . get_string('bcriteria', 'badges'));

if ($emsg !== '') {
    echo $OUTPUT->notification($emsg);
} else if ($msg !== '') {
    echo $OUTPUT->notification($msg, 'notifysuccess');
}

$output->print_badge_tabs($badgeid, $context, 'criteria');

if ($badge->is_locked() || $badge->is_active()) {
    echo $OUTPUT->notification(get_string('lockedbadge', 'badges'));
    echo html_writer::tag('div',
            $OUTPUT->heading(get_string('criteriasummary', 'badges'), 3) .
            $output->print_badge_criteria($badge),
            array('class' => 'generalbox'));
} else {
    echo $output->print_criteria_actions($badge);
    $form->display();
}
echo $OUTPUT->footer();