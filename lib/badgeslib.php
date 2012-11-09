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

/*
 * Badge award criteria aggregation method.
 */
define('BADGE_CRITERIA_AGGREGATION_ALL', 1);

/*
 * Badge award criteria aggregation method.
 */
define('BADGE_CRITERIA_AGGREGATION_ANY', 2);

/*
 * Inactive badge means that this badge cannot be earned and has not been awarded
 * yet. Its award criteria can be changed.
 */
define('BADGE_STATUS_INACTIVE', 0);

/*
 * Active badge means that this badge can we earned, but it has not been awarded
 * yet. Can be deactivated for the purpose of changing its criteria.
 */
define('BADGE_STATUS_ACTIVE', 1);

/*
 * Active badge means that it can be earned and has already been awarded to users.
 * Its criteria cannot be changed any more.
 */
define('BADGE_STATUS_ACTIVE_LOCKED', 2);

/*
 * Inactive badge can no longer be earned, but it has been awarded in the past and
 * therefore its criteria cannot be changed.
 */
define('BADGE_STATUS_INACTIVE_LOCKED', 3);

/*
 * Archived badge is considered deleted and can no longer be earned and is not
 * displayed in the list of all badges.
 */
define('BADGE_STATUS_ARCHIVED', 4);

/**
 * Class that represents badge.
 *
 */
class badge {
    /** @var int Badge id */
    public $bid;

    /** @var array Badge criteria */
    private $criteria;

    /**
     * Constructs with badge details.
     *
     * @param stdClass $course Moodle course object.
     */
    public function __construct($badgeid) {
        $this->bid = $badgeid;
    }

    /**
     * Checks if badges is active.
     * Used in badge award.
     *
     * @return bool A status indicating badge is active
     */
    public function is_active() {

        return true;
    }

    /**
     * Use to mark the badge as active or inactive.
     * Possible status: BADGE_STATUS_ACTIVE or BADGE_STATUS_ACTIVE_LOCKED.
     * Only active badges can be earned/awarded.
     *
     * @param bool $status If set to false, makes badge inactive (optional)
     * @return bool Returns true on success.
     */
    public function set_active($status = true) {

        return true;
    }

    /**
     * Checks if badges is locked.
     * Used in badge award and editing.
     *
     * @return bool A status indicating badge is locked
     */
    public function is_locked() {

        return true;
    }

    /**
     * Checks if badges has been awarded.
     * Used in badge editing.
     *
     * @return bool A status indicating badge has been awarded at least once
     */
    public function has_awards() {

        return true;
    }

    /**
     * Gets list of users who have earned an instance of this badge.
     *
     * @return array An array of users (id) who have earned the badge
     */
    public function get_awards() {

        return array();
    }

    /**
     * Checks if badges has award criteria set up.
     *
     * @return bool A status indicating badge has at least one criterion
     */
    public function has_criteria() {

        return true;
    }

    /**
     * Returns badge award criteria
     *
     * @return array An array of badge criteria
     */
    public function get_criteria() {

        return array();
    }

    /**
     * Marks the badge as archived.
     * For reporting and historical purposed we cannot completely delete badges.
     * We will just change their status to BADGE_STATUS_ARCHIVED.
     *
     * @return bool Returns true on success.
     */
    public function delete() {

        return true;
    }
}

/**
 * Gets badges for a specific user.
 *
 * @param int $userid User ID
 * @param int $limitnum return the first $limitnum records (optional). If omitted all records are returned
 * @return array of badges ordered by decreasing date of issue
 */
function get_user_badges($userid, $limitnum = 0) {
    global $DB, $USER;
    $badges = array();

    $badges = $DB->get_records_sql('
            SELECT
                bi.dateissued,
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
            ORDER BY bi.dateissued DESC',
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
                bi.dateissued,
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

        $a['issued_on'] = date('Y-m-d', $record->dateissued);
        $a['evidence'] = new moodle_url('/badges/badge.php', array('b' => $hash)); // URL.
        $a['badge'] = array();
        $a['badge']['version'] = '0.5.0'; // Version of OBI specification, 0.5.0 - current beta.
        $a['badge']['name'] = $record->name;
        $a['badge']['image'] = ''; // Image URL.
        $a['badge']['description'] = $record->description;
        $a['badge']['criteria'] = new moodle_url('/badges/badge.php', array('b' => $hash)); // URL.
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

            if (has_capability('moodle/badges:viewawarded', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/index.php',
                        array('type' => 'course', 'id' => $course->id));

                $coursenode->get('coursebadges')->add(get_string('managebadges', 'badges'), $url,
                    navigation_node::TYPE_SETTING, null, 'coursebadges');
            }

            if (has_capability('moodle/badges:createbadge', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/newbadge.php',
                        array('type' => 'course', 'id' => $course->id));

                $coursenode->get('coursebadges')->add(get_string('newbadge', 'badges'), $url,
                        navigation_node::TYPE_SETTING, null, 'newbadge');
            }
        }
    }
}