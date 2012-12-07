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
 * Handling ajax requests for badge criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('badgeid', PARAM_INT);
$ctype   = required_param('criteriatype', PARAM_INT);
$paramid = optional_param('paramid', 0,  PARAM_INT);

$badge = new badge($badgeid);

//$criterion = award_criteria::build($params);

$output = '<div>' . $badge->name . '</div>';

$PAGE->set_url('/badges/criteria_ajax.php');

// Process ajax request.
echo $output;

die();