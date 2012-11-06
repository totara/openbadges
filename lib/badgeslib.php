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
 * Contains classes, functions and constants used in badges.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

function get_user_badges($userid, $limitnum = null) {
    global $DB, $USER;
    $badges = array();
    
    $badges = $DB->get_records('badge_issued', array('userid' => $userid), 'dateissued DESC', '*', $limitnum);

    return $badges;
}

/**
 * Extends the course navigation with the Badges page
 *
 * @param navigation_node $coursenode
 * @param object $course
 */
function badges_add_course_navigation(navigation_node $coursenode, $course) {
    global $CFG, $USER, $SITE;

    $allowcoursebadges = $CFG->badges_allowcoursebadges;

    $coursecontext = context_course::instance($course->id);
    $isfrontpage = (!$coursecontext || $course->id == $SITE->id);

    if (($allowcoursebadges == 1) && !$isfrontpage) {
        if (has_capability('moodle/badges:viewbadges', $coursecontext) && can_access_course($course, $USER)) {
            $url = new moodle_url($CFG->wwwroot . '/badges/view.php',
                    array('type' => 'course', 'id' => $course->id));

            $coursenode->add(get_string('coursebadges','badges'), $url,
                    navigation_node::TYPE_CONTAINER, null, 'coursebadges',
                    new pix_icon('i/badge', get_string('coursebadges','badges')));

            if (has_capability('moodle/badges:createbadge', $coursecontext) ||
                has_capability('moodle/badges:viewawarded', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/index.php',
                        array('type' => 'course', 'id' => $course->id));
                
                $coursenode->get('coursebadges')->add(get_string('managebadges','badges'), $url,
                    navigation_node::TYPE_SETTING, null, 'coursebadges');
            }
        }
    }
}