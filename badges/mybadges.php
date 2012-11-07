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
 * Displays user badges for badges management in own profile
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login();
if (isguestuser()) {
    die();
}

$context = context_user::instance($USER->id);
require_capability('moodle/badges:manageownbadges', $context);

$action = optional_param('action', null, PARAM_ALPHA);

$url = new moodle_url('/badges/mybadges.php');

if ($action) {
    $url->param('action', $action);
}

$PAGE->set_url($url);
$PAGE->set_context($context);

$title = get_string('mybadges', 'badges');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');

// TODO: Better way of pushing badges to Mozilla backpack?
if (!empty($CFG->badges_allowexternalbackpack)) {
    $PAGE->requires->js(new moodle_url('http://backpack.openbadges.org/issuer.js'), true);
    $PAGE->requires->js('/badges/backpack.js', true);
}

$output = $PAGE->get_renderer('core', 'badges');
$badges = badges_get_user_badges($USER->id);

echo $OUTPUT->header();
// Show how many badges are already earned.

echo $OUTPUT->footer();
