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
 * Displays available badges to a user
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once('../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$type = required_param('type', PARAM_TEXT);
$courseid = optional_param('id', 0, PARAM_INT);

require_login($SITE);
if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $PAGE->set_url('/badges/view.php', array('type' => $type, 'id' => $course->id));
} else {
    $PAGE->set_url('/badges/view.php', array('type' => $type));
}

$title = get_string($type . 'badges','badges');

if ($type == 'site') {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title);
} else {
    require_login($course);
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($course->fullname . ": " . $title);
}

$PAGE->set_title($title);

echo $OUTPUT->header();
//Calculate how many badges are available in the course/site

echo $OUTPUT->footer();