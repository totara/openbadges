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
 * First step page for creating a new badge
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

$type = required_param('type', PARAM_TEXT);
$courseid = optional_param('id', 0, PARAM_INT);

require_login();

$title = get_string('create', 'badges');

if (($type == 'course') && ($course = $DB->get_record('course', array('id' => $courseid)))) {
    require_login($course);
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_url('/badges/newbadge.php', array('type' => $type, 'id' => $course->id));
    $PAGE->set_heading($course->fullname . ": " . $title);
    $PAGE->set_title($course->fullname . ": " . $title);
} else {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_url('/badges/newbadge.php', array('type' => $type));
    $PAGE->set_heading($title);
    $PAGE->set_title($title);
}
$currenturl = qualified_me();

$form = new edit_details_form($currenturl, array('action' => 'new'));

if ($form->is_cancelled()){
    redirect(new moodle_url('/badges/index.php', array('type' => $type, 'id' => $courseid)));
} else if ($data = $form->get_data()) {
    // Creating new badge here.
    $now = time();

    $fordb = new stdClass();
    $fordb->name = $data->name;
    $fordb->description = $data->description;
    $fordb->visible = 0;
    $fordb->timecreated = $now;
    $fordb->timemodified = $now;
    $fordb->usermodified = $USER->id;
    $fordb->image = $data->image;
    $fordb->issuername = $data->issuername;
    $fordb->issuerurl = $data->issuerurl;
    $fordb->issuercontact = $data->issuercontact;
    switch($data->expiry) {
        case 0:
            $fordb->expiredate = null;
            $fordb->expireperiod = null;
            break;
        case 1:
            $fordb->expiredate = $data->expirydate;
            $fordb->expireperiod = null;
            break;
        case 2:
            $fordb->expiredate = null;
            $fordb->expireperiod = $data->expiryvalue;
            break;
    }
    $fordb->context = ($type == 'course') ? BADGE_TYPE_COURSE : BADGE_TYPE_SITE;
    $fordb->courseid = ($type == 'course') ? $courseid : null;
    $fordb->messagesubject = get_string('messagesubject', 'badges');
    $fordb->message = get_string('messagebody', 'badges',
            html_writer::link($CFG->wwwroot . '/badges/mybadges.php', get_string('mybadges','badges')));
    $fordb->attachment = 1;
    $fordb->notification = 0;
    $fordb->status = 0;

    $newbadge = $DB->insert_record('badge', $fordb, true);
    redirect(new moodle_url('/badges/overview.php', array('id' => $newbadge)));
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();