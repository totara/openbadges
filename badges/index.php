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

$type = required_param('type', PARAM_TEXT);
$courseid = optional_param('id', 0, PARAM_INT);
$search  = optional_param('search', '', PARAM_CLEAN);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$activate = optional_param('activate', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$sort = optional_param('sort', 'name', PARAM_ALPHA);
$dir  = optional_param('dir', 'asc', PARAM_ALPHA);

require_login($SITE);
if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $PAGE->set_url('/badges/index.php', array('type' => $type, 'id' => $course->id));
} else {
    $PAGE->set_url('/badges/index.php', array('type' => $type));
}

$title = get_string($type . 'badges', 'badges');

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

if (!has_any_capability(array(
        'moodle/badges:viewawarded',
        'moodle/badges:createbadge',
        'moodle/badges:awardbadge',
        'moodle/badges:configuremessages',
        'moodle/badges:configuredetails',
        'moodle/badges:deletebadge'), $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($hdr);
$PAGE->requires->js('/badges/backpack.js');
$PAGE->requires->js_init_call('check_site_access', null, false);
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
        $err = get_string('error:cannotact', 'badges') . get_string('nocriteria', 'badges');
    } else {
        if ($badge->is_locked()) {
            $badge->set_status(BADGE_STATUS_ACTIVE_LOCKED);
            $msg = get_string('activatesuccess', 'badges');
        } else {
            require_sesskey();
            $badge->set_status(BADGE_STATUS_ACTIVE);
            $msg = get_string('activatesuccess', 'badges');
        }
        $returnurl->param('msg', $msg);
        redirect($returnurl);
    }
} else if ($deactivate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    $badge = new badge($deactivate);
    if ($badge->is_locked()) {
        $badge->set_status(BADGE_STATUS_INACTIVE_LOCKED);
        $msg = get_string('deactivatesuccess', 'badges');
    } else {
        require_sesskey();
        $badge->set_status(BADGE_STATUS_INACTIVE);
        $msg = get_string('deactivatesuccess', 'badges');
    }
    $returnurl->param('msg', $msg);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebadges', 'badges'));

if (has_capability('moodle/badges:createbadge', $PAGE->context)) {
    $params = array();
    $params['type'] = $type;
    $params['id'] = $courseid ? $courseid : null;

    echo $OUTPUT->single_button(new moodle_url('/badges/newbadge.php', $params), get_string('newbadge', 'badges'), 'GET');
}

echo $OUTPUT->footer();

