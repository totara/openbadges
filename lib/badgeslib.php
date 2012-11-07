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

/**
 * Gets full badge info for assertion URL
 *
 * @param string $hash
 */
function get_user_badges($userid, $limitnum = 0) {
    global $DB, $USER;
    $badges = array();

    $badges = $DB->get_records_sql('
            SELECT
                bi.dateissue,
                bi.dateexpire,
                u.email,
                b.*
            FROM
                {badge} b,
                {badge_issued} bi,
                {user} u
            WHERE b.id = bi.badgeid
                AND u.id = bi.userid
                AND bi.userid = ?
            ORDER BY bi.dateissue DESC',
            array($userid), 0, $limitnum);

    return $badges;
}

/**
 * Get issued badge details for assertion URL
 *
 * @param string $hash
 */
function get_issued_badge_info($hash) {
    global $DB, $CFG;

    $a = array();

    $record = $DB->get_record_sql('
            SELECT
                bi.dateissue,
                bi.dateexpire,
                u.email,
                b.*
            FROM
                {badge} b,
                {badge_issued} bi,
                {user} u
            WHERE b.id = bi.badgeid
                AND u.id = bi.userid
                AND bi.uniquehash = ?',
            array($hash), IGNORE_MISSING);

    if ($record) {
        // Recipient's email is hashed: <algorithm>$<hash(email + salt)>.
        $a['recipient'] = 'sha256$' . hash('sha256', $record->email . $CFG->badges_badgesalt);
        $a['salt'] = $CFG->badges_badgesalt;

        if ($record->dateexpire) {
            $a['expires'] = date('Y-m-d', $record->dateexpire);
        }

        $a['issued_on'] = date('Y-m-d', $record->dateissue);
        $a['evidence'] = new moodle_url('/badges/badge.php', array('b' => $hash)); //URL
        $a['badge'] = array();
        $a['badge']['version'] = '0.5.0'; //Version of OBI specification, 0.5.0 - current beta.
        $a['badge']['name'] = $record->name;
        $a['badge']['image'] = ''; // Image URL
        $a['badge']['description'] = $record->description;
        $a['badge']['criteria'] = new moodle_url('/badges/badge.php', array('b' => $hash)); //URL
        $a['badge']['issuer'] = array();
        $a['badge']['issuer']['origin'] = $record->issuerurl;
        $a['badge']['issuer']['name'] = $record->issuername;
        $a['badge']['issuer']['contact'] = $record->issuercontact;
    }

    return $a;
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

            $coursenode->add(get_string('coursebadges', 'badges'), $url,
                    navigation_node::TYPE_CONTAINER, null, 'coursebadges',
                    new pix_icon('i/badge', get_string('coursebadges', 'badges')));

            if (has_capability('moodle/badges:createbadge', $coursecontext) ||
                has_capability('moodle/badges:viewawarded', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/index.php',
                        array('type' => 'course', 'id' => $course->id));

                $coursenode->get('coursebadges')->add(get_string('managebadges', 'badges'), $url,
                    navigation_node::TYPE_SETTING, null, 'coursebadges');
            }
        }
    }
}