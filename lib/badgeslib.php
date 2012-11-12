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

/* Include required award criteria library. */
require_once($CFG->dirroot . '/badges/criteria/award_criteria.php');

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
 * Inactive badge can no longer be earned, but it has been awarded in the past and
 * therefore its criteria cannot be changed.
 */
define('BADGE_STATUS_INACTIVE_LOCKED', 2);

/*
 * Active badge means that it can be earned and has already been awarded to users.
* Its criteria cannot be changed any more.
*/
define('BADGE_STATUS_ACTIVE_LOCKED', 3);

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
    protected $id;

    /** Values from the table 'badge' */
    protected $name;
    protected $description;
    protected $visible;
    protected $timecreated;
    protected $timemodified;
    protected $usermodified;
    protected $image;
    protected $issuername;
    protected $issuerurl;
    protected $issuercontact;
    protected $expiredate;
    protected $expireperiod;
    protected $context;
    protected $courseid;
    protected $message;
    protected $attachment;
    protected $notification;
    protected $status = 0;

    /** @var array Badge criteria */
    protected $criteria = array();

    /** @var array Badge awards */
    protected $awards = array();
    /**
     * Constructs with badge details.
     *
     * @param int $badgeid badge ID.
     */
    public function __construct($badgeid) {
        global $DB;
        $this->id = $badgeid;

        $data = $DB->get_record('badge', array('id' => $badgeid));

        if (empty($data)) {
            print_error('error:nosuchbadge', 'badges', $badgeid);
        }

        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        $this->awards = array();
        $this->criteria = self::get_criteria();
    }

    /**
     * Badge property getter.
     * @param string $key
     * @return mixed value
     */
    public function __get($key) {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
    }

    /**
     * Badge property setter.
     * Does not save to database. Use save() to save changes.
     *
     * @param string $key
     * @param mixed $value
     * @return object $this
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
            $this->timemodified = time();
            return $this;
        }
        print_error('error:setter', 'badges', array('field' => $field, 'class' => get_class($this)));
    }

    /**
     * Save/update badge information in 'badge' table only.
     * Cannot be used for updating awards and criteria settings.
     *
     * @return bool Returns true on success.
     */
    public function save() {
        global $DB;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }
        unset($fordb->awards);
        unset($fordb->criteria);

        if ($DB->update_record('badge', $fordb)) {
            return true;
        } else {
            print_error('error:save', 'badges');
            return false;
        }
    }

    /**
     * Checks if badges is active.
     * Used in badge award.
     *
     * @return bool A status indicating badge is active
     */
    public function is_active() {
        if (($this->status == BADGE_STATUS_ACTIVE) ||
            ($this->status == BADGE_STATUS_ACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Use to set badge status.
     * Only active badges can be earned/awarded/issued.
     *
     * @param int $status from BADGE_STATUS constants
     */
    public function set_status($status = 0) {
        $this->status = $status;
        $this->save();
    }

    /**
     * Checks if badges is locked.
     * Used in badge award and editing.
     *
     * @return bool A status indicating badge is locked
     */
    public function is_locked() {
        if (($this->status == BADGE_STATUS_ACTIVE_LOCKED) ||
                ($this->status == BADGE_STATUS_INACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if badges has been awarded.
     * Used in badge editing.
     *
     * @return bool A status indicating badge has been awarded at least once
     */
    public function has_awards() {
        if (count($this->awards) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets list of users who have earned an instance of this badge.
     *
     * @return array An array of users (id) who have earned the badge
     */
    public function get_awards() {
        return $this->awards;
    }

    /**
     * Issue a badge to user.
     *
     */
    public function issue($userid) {
        global $DB;

        $now = time();
        $issued = new stdClass();
        $issued->badgeid = $this->id;
        $issued->userid = $userid;
        $issued->uniquehash = sha1(rand() . $userid . $this->id . $now);
        $issued->dateissued = $now;

        if ($this->can_expire()) {
            $issued->dateexpire = $this->calculate_expiry($now);
        } else {
            $issued->dateexpire = null;
        }

        // Issued badges always being issued as private.
        $issued->visible = 0;

        $result = $DB->insert_record('badge_issued', $issued, true);

        if ($result) {
            notify_badge_award();
        }
    }

    /**
     * Checks if badges has award criteria set up.
     *
     * @return bool A status indicating badge has at least one criterion
     */
    public function has_criteria() {
        if (count($this->criteria) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns badge award criteria
     *
     * @return array An array of badge criteria
     */
    public function get_criteria() {
        global $DB;
        $criteria = array();

        if ($records = (array)$DB->get_records('badge_criteria', array('badgeid' => $this->id))) {
            foreach ($records as $record) {
                $criteria[$record->id] = award_criteria::build((array)$record);
            }
        }

        return $criteria;
    }

    /**
     * Get aggregation method for badge criteria
     *
     * @param int $criteriatype If none supplied, get overall aggregation method (optional)
     * @return int One of BADGE_CRITERIA_AGGREGATION_ALL or BADGE_CRITERIA_AGGREGATION_ANY
     */
    public function get_aggregation_method($criteriatype = 0) {
        global $DB;
        $params = array('badgeid' => $this->id, 'criteriatype' => $criteriatype);

        $aggregation = $DB->get_record('badge_criteria', $params, IGNORE_MULTIPLE);

        // If this criteria doesn't have aggregation method, return null.
        if (!$aggregation->method) {
            return null;
        }

        return $aggregation->method;
    }

    /**
     * Checks if badge has expiry period or date set up.
     *
     * @return bool A status indicating badge can expire
     */
    public function can_expire() {
        if ($this->expireperiod || $this->expiredate) {
            return true;
        }
        return false;
    }

    /**
     * Calculates badge expiry date based on either expirydate or expiryperiod.
     *
     * @param int $timestamp Time of badge issue
     * @return int A timestamp
     */
    public function calculate_expiry($timestamp) {
        $expiry = null;

        if (isset($this->expirydate)) {
            $expiry = $this->expirydate;
        } else if (isset($this->expiryperiod)) {
            $expiry = $timestamp + $this->expiryperiod * 24 * 60 * 60;
        }

        return $expiry;
    }

    /**
     * Marks the badge as archived.
     * For reporting and historical purposed we cannot completely delete badges.
     * We will just change their status to BADGE_STATUS_ARCHIVED.
     */
    public function delete() {
        $this->status = BADGE_STATUS_ARCHIVED;
        $this->save();
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