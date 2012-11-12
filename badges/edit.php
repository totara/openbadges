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

$context = context_system::instance();
$badge = new badge($badgeid);

$PAGE->set_context($context);
$PAGE->set_url('/badges/edit.php', array('id' => $badgeid, 'action' => $action));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($badge->name);

echo $OUTPUT->header();
echo $OUTPUT->heading($badge->name . ': ' . get_string('b' . $action, 'badges'));

$output = $PAGE->get_renderer('core', 'badges');
$output->print_badge_tabs($badgeid, $context, $action);

$form_class = 'edit_' . $action . '_form';
$form = new $form_class();

$form->display();

echo $OUTPUT->footer();