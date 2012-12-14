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
require_once($CFG->dirroot . '/badges/edit_form.php');

$badgeid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);

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
$statusmsg = '';
$errormsg  = '';

$form_class = 'edit_' . $action . '_form';
$form = new $form_class($currenturl, array('badge' => $badge, 'action' => $action));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/badges/overview.php', array('id' => $badgeid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    if ($action == 'details') {
        $badge->name = $data->name;
        $badge->description = $data->description;
        $badge->visible = $data->visible;
        $badge->usermodified = $USER->id;
        $badge->issuername = $data->issuername;
        $badge->issuerurl = $data->issuerurl;
        $badge->issuercontact = $data->issuercontact;
        $badge->expiredate = ($data->expiry == 1) ? $data->expiredate : null;
        $badge->expireperiod = ($data->expiry == 2) ? $data->expireperiod : null;

        if ($badge->save()) {
            badges_process_badge_image($badge, $form->save_temp_file('image')); // @TODO: image is not refreshed after submit.
            $form->set_data($badge);
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'badges');
        }
    } else if ($action == 'message') {
        $badge->message = $data->message;
        $badge->messagesubject = $data->messagesubject;
        $badge->notification = $data->notification;
        $badge->attachment = $data->attachment;

        if ($badge->save()) {
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'badges');
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($badge->name . ': ' . get_string('b' . $action, 'badges'));

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

$output->print_badge_tabs($badgeid, $context, $action);

if ($badge->is_locked() || $badge->is_active()) {
    echo $OUTPUT->notification(get_string('lockedbadge', 'badges'));
}
$form->display();

echo $OUTPUT->footer();