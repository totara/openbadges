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
 * Page for badges management
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$type = required_param('type', PARAM_INT);
$courseid = optional_param('id', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$activate = optional_param('activate', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$sortby = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow  = optional_param('dir', 'ASC', PARAM_ALPHA);
$confirm  = optional_param('confirm', false, PARAM_BOOL);
$delete = optional_param('delete', 0, PARAM_INT);

if (!in_array($sortby, array('name', 'status'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login();

$msg = '';
$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'page' => $page);

if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $urlparams['type'] = $type;
    $urlparams['id'] = $course->id;
} else {
    $urlparams['type'] = $type;
}

$returnurl = new moodle_url('/badges/index.php', $urlparams);
$PAGE->set_url($returnurl);

if ($type == BADGE_TYPE_SITE) {
    $title = get_string('sitebadges', 'badges');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title);
    navigation_node::override_active_url(new moodle_url('/badges/index.php', array('type' => BADGE_TYPE_SITE)));
} else {
    require_login($course);
    $title = get_string('coursebadges', 'badges');
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($course->fullname . ": " . $title);
    navigation_node::override_active_url(
        new moodle_url('/badges/index.php', array('type' => BADGE_TYPE_COURSE, 'id' => $course->id))
    );
}

if (!has_capability('moodle/badges:awardbadge', $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($title);
$output = $PAGE->get_renderer('core', 'badges');

if ($delete && has_capability('moodle/badges:deletebadge', $PAGE->context)) {
    $badge = new badge($delete);
    if (!$confirm) {
        echo $output->header();
        echo $output->confirm(
                    get_string('delconfirm', 'badges', $badge->name),
                    new moodle_url($PAGE->url, array('delete' => $badge->id, 'confirm' => 1)),
                    $returnurl
                );
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $badge->delete();
        redirect($returnurl);
    }
}

if ($activate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    $badge = new badge($activate);

    if (!$badge->has_criteria()) {
        $msg = get_string('error:cannotact', 'badges') . get_string('nocriteria', 'badges');
    } else {
        if ($badge->is_locked()) {
            $badge->set_status(BADGE_STATUS_ACTIVE_LOCKED);
        } else {
            $badge->set_status(BADGE_STATUS_ACTIVE);
        }
        redirect($returnurl);
    }
} else if ($deactivate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    $badge = new badge($deactivate);
    if ($badge->is_locked()) {
        $badge->set_status(BADGE_STATUS_INACTIVE_LOCKED);
    } else {
        $badge->set_status(BADGE_STATUS_INACTIVE);
    }
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebadges', 'badges'));

$totalcount = count(get_badges($type, $courseid, '', '' , '', ''));
$records = get_badges($type, $courseid, $sortby, $sorthow, $page, BADGE_PERPAGE);

if ($totalcount) {
    echo $output->heading(get_string('badgestoearn', 'badges', $totalcount), 2);

    if ($course && $course->startdate > time()) {
        echo $OUTPUT->box(get_string('error:notifycoursedate', 'badges'), 'generalbox notifyproblem');
    }

    if ($msg !== '') {
        echo $OUTPUT->notification($msg, 'notifyproblem');
    }

    $badges             = new badge_management($records);
    $badges->sort       = $sortby;
    $badges->dir        = $sorthow;
    $badges->page       = $page;
    $badges->perpage    = BADGE_PERPAGE;
    $badges->totalcount = $totalcount;

    echo $output->render($badges);
} else {
    echo $output->notification(get_string('nobadges', 'badges'));
}

echo $OUTPUT->footer();