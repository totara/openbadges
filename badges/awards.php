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
 * Badge awards information
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('id', PARAM_INT);

require_login($SITE);

$badge = new badge($badgeid);
$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url('/badges/awards.php', array('id' => $badgeid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($badge->name);

echo $OUTPUT->header();
echo $OUTPUT->heading($badge->name . ': ' . get_string('awards', 'badges'));

$output = $PAGE->get_renderer('core', 'badges');
$output->print_badge_tabs($badgeid, $context, 'awards');

var_dump($badge);

if ($badge->has_awards()) {
    $output->print_awarded_table($badge->awards);
}
else {
    echo $OUTPUT->notification(get_string('noawards', 'badges'));
}

echo $OUTPUT->footer();