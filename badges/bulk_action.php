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
 * Processing bulk badge action from index.php
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

if (isguestuser() || !confirm_sesskey()) {
    redirect($CFG->wwwroot);
}

$returnto = optional_param('returnto', $CFG->wwwroot, PARAM_LOCALURL);
$action = optional_param('action', '', PARAM_TEXT);

if ($action == '') {
    redirect($returnto);
}

$bids = array();

// Get Badges IDs from the POST parameters.
foreach ($_POST as $par => $value) {
    if (preg_match('/^badgeid\_(\d+)$/', $par)) {
        $bid = optional_param($par, NULL, PARAM_INT);
        if ($bid) {
            $bids[]=$bid;
        }
    }
}
if (!empty($bids)) {
    list($sql, $params) = $DB->get_in_or_equal($bids);
    if ($action == 'hide') {
        $DB->set_field_select('badge', 'visible', 0, "id $sql", $params);
    } else if ($action == 'show') {
        $DB->set_field_select('badge', 'visible', 1, "id $sql", $params);
    } else if ($action == 'delete') {
        $DB->set_field_select('badge', 'status', BADGE_STATUS_ARCHIVED, "id $sql", $params);
    }
}
redirect($returnto);