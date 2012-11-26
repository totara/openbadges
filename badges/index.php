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
$search  = optional_param('search', '', PARAM_CLEAN);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$activate = optional_param('activate', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$sortby = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow  = optional_param('dir', 'ASC', PARAM_ALPHA);
$confirm  = optional_param('confirm', false, PARAM_BOOL);
$delete = optional_param('delete', 0, PARAM_INT);
$updatepref = optional_param('updatepref', false, PARAM_BOOL);

if (!in_array($sortby, array('name', 'status'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login($SITE);

$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'perpage' => $perpage, 'page' => $page);

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

$PAGE->set_title($title);
$output = $PAGE->get_renderer('core', 'badges');

if ($updatepref) {
    require_sesskey();
    if ($perpage > 0) {
        set_user_preference('badgesmng_perpage', $perpage);
    }
    redirect($returnurl);
}

if ($delete && has_capability('moodle/badges:deletebadge', $PAGE->context)) {
    $badge = new badge($delete);
    if (!$confirmed) {
        echo $output->header();
        echo $output->confirm(
                    get_string('delconfirm', 'badges', $badge->name),
                    new moodle_url($PAGE->url, array('delete' => $badge->id, 'confirmed' => 1)),
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

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebadges', 'badges'));

if (has_capability('moodle/badges:createbadge', $PAGE->context)) {
    $params = array();
    $params['type'] = $type;
    $params['id'] = $courseid ? $courseid : null;

    echo $OUTPUT->single_button(new moodle_url('/badges/newbadge.php', $params), get_string('newbadge', 'badges'), 'GET');
}

$perpage = (int)get_user_preferences('badgesmng_perpage', 20);

$totalcount = count(get_badges($type, $courseid, false, '', '', '', '', ''));
$records = get_badges($type, $courseid, false, $sortby, $sorthow, $page, $perpage, $search);

if ($totalcount) {
    echo $output->heading(get_string('badgestoearn', 'badges', $totalcount), 2);
    $badges             = new badge_management($records);
    $badges->sort       = $sortby;
    $badges->dir        = $sorthow;
    $badges->page       = $page;
    $badges->perpage    = $perpage;
    $badges->totalcount = $totalcount;

    echo $output->render($badges);
}
else {
    echo $output->notification(get_string('nobadges', 'badges'));
}

echo $OUTPUT->footer();