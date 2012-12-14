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

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$type       = required_param('type', PARAM_INT);
$courseid   = optional_param('id', 0, PARAM_INT);
$sortby     = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'DESC', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);
$updatepref = optional_param('updatepref', false, PARAM_BOOL);
$perpage    = optional_param('perpage', 20, PARAM_INT);
$search     = optional_param('search', '', PARAM_CLEAN);

require_login();

if (!in_array($sortby, array('name', 'dateissued'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' && $sorthow != 'DESC') {
    $sorthow = 'ACS';
}

if ($page < 0) {
    $page = 0;
}

if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $PAGE->set_url('/badges/view.php', array('type' => $type, 'id' => $course->id, 'sort' => $sortby, 'dir' => $sorthow));
} else {
    $PAGE->set_url('/badges/view.php', array('type' => $type, 'sort' => $sortby, 'dir' => $sorthow));
}

if ($type == BADGE_TYPE_SITE) {
    $title = get_string('sitebadges', 'badges');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title);
} else {
    require_login($course);
    $title = get_string('coursebadges', 'badges');
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($course->fullname . ": " . $title);
    navigation_node::override_active_url(
        new moodle_url('/badges/view.php', array('type' => BADGE_TYPE_COURSE, 'id' => $course->id))
    );
}

$PAGE->set_title($title);
$output = $PAGE->get_renderer('core', 'badges');

if ($updatepref) {
    require_sesskey();
    if ($perpage > 0) {
        set_user_preference('badges_perpage', $perpage);
    }
    redirect($PAGE->url);
}

echo $output->header();
$perpage = (int)get_user_preferences('badges_perpage', 20);

$totalcount = count(get_badges($type, $courseid, true, '', '', '', '', '', $USER->id));
$records = get_badges($type, $courseid, true, $sortby, $sorthow, $page, $perpage, $search, $USER->id);

if ($totalcount) {
    echo $output->heading(get_string('badgestoearn', 'badges', $totalcount), 2);
    $badges             = new badge_collection($records);
    $badges->sort       = $sortby;
    $badges->dir        = $sorthow;
    $badges->page       = $page;
    $badges->perpage    = $perpage;
    $badges->totalcount = $totalcount;

    echo $output->render($badges);
} else {
    echo $output->notification(get_string('nobadges', 'badges'));
}

echo $output->footer();