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
$sort = optional_param('sort', 'name', PARAM_ALPHA);
$dir  = optional_param('dir', 'asc', PARAM_ALPHA);
$confirm  = optional_param('confirm', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

require_login($SITE);

$urlparams = array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page'=>$page);

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
} else {
    require_login($course);
    $title = get_string('coursebadges', 'badges');
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($course->fullname . ": " . $title);
}

$output = $PAGE->get_renderer('core', 'badges');

if ($confirm && confirm_sesskey()) {

}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebadges', 'badges'));

if (has_capability('moodle/badges:createbadge', $PAGE->context)) {
    $params = array();
    $params['type'] = $type;
    $params['id'] = $courseid ? $courseid : null;

    echo $OUTPUT->single_button(new moodle_url('/badges/newbadge.php', $params), get_string('newbadge', 'badges'), 'GET');
}

$badges = get_badges($type, $courseid);
$output->print_badges_table($badges, $PAGE->context, $perpage);

echo $OUTPUT->footer();