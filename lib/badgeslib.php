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

/*
 * Badge type for site badges.
 */
define('BADGE_TYPE_SITE', 1);

/*
 * Badge type for course badges.
 */
define('BADGE_TYPE_COURSE', 2);

/**
 * Class that represents badge.
 *
 */
class badge {
    /** @var int Badge id */
    public $id;

    /** Values from the table 'badge' */
    public $name;
    public $description;
    public $visible;
    public $timecreated;
    public $timemodified;
    public $usermodified;
    public $image;
    public $issuername;
    public $issuerurl;
    public $issuercontact;
    public $expiredate;
    public $expireperiod;
    public $context;
    public $courseid;
    public $message;
    public $messagesubject;
    public $attachment;
    public $notification;
    public $status = 0;

    /** @var array Badge criteria */
    public $criteria = array();

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

        $this->criteria = self::get_criteria();
    }


    /**
     * Use to get context instance of a badge.
     * @return context instance.
     */
    public function get_context() {
        if ($this->context == BADGE_TYPE_SITE) {
            return context_system::instance();
        } else if ($this->context == BADGE_TYPE_COURSE) {
            return context_course::instance($this->courseid);
        } else {
            debugging('Something is wrong...');
        }
    }

    /**
     * Return array of aggregation methods
     * @return array
     */
    public static function get_aggregation_methods() {
        return array(
                BADGE_CRITERIA_AGGREGATION_ALL => get_string('all', 'badges'),
                BADGE_CRITERIA_AGGREGATION_ANY => get_string('any', 'badges'),
        );
    }

    /**
     * Return array of accepted criteria types for this badge
     * @return array
     */
    public function get_accepted_criteria() {
        $criteriatypes = array();

        if ($this->context == BADGE_TYPE_COURSE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSE,
                    BADGE_CRITERIA_TYPE_ACTIVITY
            );
        } else if ($this->context == BADGE_TYPE_SITE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSESET,
                    //BADGE_CRITERIA_TYPE_PROFILE,
                    //BADGE_CRITERIA_TYPE_SOCIAL
            );
        }

        return $criteriatypes;
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
        unset($fordb->criteria);

        $fordb->timemodified = time();
        if ($DB->update_record('badge', $fordb)) {
            return true;
        } else {
            print_error('error:save', 'badges');
            return false;
        }
    }

    /**
     * Creates and saves a clone of badge with all its properties.
     * Clone is not active by default and has 'Copy of' attached to its name.
     *
     * @return int ID of new badge.
     */
    public function make_clone() {
        global $DB;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }

        $fordb->name = get_string('copyof', 'badges') . $this->name;
        $fordb->status = BADGE_STATUS_INACTIVE;
        $fordb->image = 0;
        unset($fordb->id);

        $criteria = $fordb->criteria;
        unset($fordb->criteria);

        if ($new = $DB->insert_record('badge', $fordb, true)) {
            $newbadge = new badge($new);

            // Copy badge image.
            $fs = get_file_storage();
            if ($file = $fs->get_file($this->get_context()->id, 'badges', 'badgeimage', $this->id, '/', 'f1.png')) {
                if ($imagefile = $file->copy_content_to_temp()) {
                    badges_process_badge_image($newbadge, $imagefile);
                }
            }

            // Copy badge criteria.

            return $new;
        } else {
            print_error('error:clone', 'badges');
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
     * Use to get the name of badge status.
     *
     */
    public function get_status_name() {
        return get_string('badgestatus_' . $this->status, 'badges');
    }

    /**
     * Use to set badge status.
     * Only active badges can be earned/awarded/issued.
     *
     * @param int $status Status from BADGE_STATUS constants
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
     * Checks if badge has been awarded to users.
     * Used in badge editing.
     *
     * @return bool A status indicating badge has been awarded at least once
     */
    public function has_awards() {
        global $DB;
        if ($DB->record_exists('badge_issued', array('badgeid' => $this->id))) {
            return true;
        }
        return false;
    }

    /**
     * Gets list of users who have earned an instance of this badge.
     *
     * @return array An array of objects with information about badge awards.
     */
    public function get_awards() {
        global $DB;

        $awards = $DB->get_records_sql(
                'SELECT b.userid, b.dateissued, b.uniquehash, u.firstname, u.lastname
                    FROM {badge_issued} b INNER JOIN {user} u
                        ON b.userid = u.id
                    WHERE b.badgeid = ?', array('badgeid' => $this->id));

        return $awards;
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
            // Lock the badge, so that its criteria could not be changed any more;
             if ($this->status == BADGE_STATUS_ACTIVE) {
                 $this->set_status(BADGE_STATUS_ACTIVE_LOCKED);
             }

            // Update details in criteria_met table.
            $compl = $this->get_criteria_completions($userid);
            foreach ($compl as $c) {
                $obj = new stdClass();
                $obj->id = $c->id;
                $obj->issuedid = $result;
                $DB->update_record('badge_criteria_met', $obj, true);
            }

            notify_badge_award($this, $result);
        }
    }

    /**
     * Reviews all badge criteria and checks if badge can be instantly awarded.
     *
     * @return int Number of awards
     */
    public function review_all_criteria() {
        $awards = 0;

        foreach ($this->criteria as $crit) {
            if ($crit->criteriatype != BADGE_CRITERIA_TYPE_OVERALL) {

            }
        }

        return $awards;
    }

    /**
     * Gets an array of completed criteria from 'badge_criteria_met' table.
     *
     * @param int $userid Completions for a user
     * @return array Records of criteria completions
     */
    public function get_criteria_completions($userid) {
        global $DB;
        $completions = array();
        $sql = "SELECT bcm.id
                FROM {badge_criteria_met} bcm
                    INNER JOIN {badge_criteria} bc ON bcm.critid = bc.id
                WHERE bc.badgeid = :badgeid AND bcm.userid = :userid ";
        $completions = $DB->get_records_sql($sql, array('badgeid' => $this->id, 'userid' => $userid));

        return $completions;
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
     * Saves criteria from form data.
     *
     * @return bool A status indicating criteria were saved.
     */
    public function save_criteria($form) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        foreach ($form as $key => $value) {
            if (preg_match('/^criteria-(\d+)-(\w+)$/', $key)) {
                $arr = explode('-', $key);

                // If no value supplied, remove.
                if ($value == 0 || $value == '') {
                    $DB->delete_records('badge_criteria_param', array('critid' => $arr[1], 'name' => $arr[2]));
                } else {
                    // Else update or insert.
                    if ($arr[2] == 'aggregation') {
                        $obj = new stdClass();
                        $obj->id = $arr[1];
                        $obj->method = $value;
                        $DB->update_record('badge_criteria', $obj, true);
                    } else {
                        if ($rec = $DB->get_record('badge_criteria_param', array('critid' => $arr[1], 'name' => $arr[2]))){
                            $obj = new stdClass();
                            $obj->id = $rec->id;
                            $obj->critid = $rec->critid;
                            $obj->name = $rec->name;
                            $obj->value = $value;
                            $DB->update_record('badge_criteria_param', $obj, true);
                        } else {
                            $obj = new stdClass();
                            $obj->critid = $arr[1];
                            $obj->name = $arr[2];
                            $obj->value = $value;
                            $DB->insert_record('badge_criteria_param', $obj, false, true);
                        }
                    }
                }
            }
        }
        $transaction->allow_commit();
        return true;
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
                $criteria[$record->criteriatype] = award_criteria::build((array)$record);
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
        $aggregation = $DB->get_field('badge_criteria', 'method', $params, IGNORE_MULTIPLE);

        if (!$aggregation) {
            return BADGE_CRITERIA_AGGREGATION_ALL;
        }

        return $aggregation;
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
            $expiry = $timestamp + $this->expiryperiod;
        }

        return $expiry;
    }

    /**
     * Checks if badge has manual award criteria set.
     *
     * @return bool A status indicating badge can be awarded manually
     */
    public function has_manual_award_criteria() {
        foreach ($this->criteria as $criterion) {
            if ($criterion->criteriatype == BADGE_CRITERIA_TYPE_MANUAL) {
                return true;
            }
        }
        return false;
    }

    /**
     * Clear all badge criteria
     */
    public function clear_criteria() {
        foreach ($this->criteria as $crit) {
            $crit->delete();
        }
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
 * Sends notifications to users about awarded badges.
 *
 * @param badge $badge Badge that was issued
 * @param int $issuedid ID of issued badge
 */
function notify_badge_award(badge $badge, $issuedid) {
    global $CFG;

    $admin = get_admin();
    $userfrom = new stdClass();
    $userfrom->email = $CFG->badges_defaultissuercontact ? $CFG->badges_defaultissuercontact : $admin->email;
    $userfrom->firstname = $CFG->badges_defaultissuername ? $CFG->badges_defaultissuername : $admin->firstname;
    $userfrom->lastname = $CFG->badges_defaultissuername ? '' : $admin->lastname;

    $plaintext = format_text_email($badge->message, FORMAT_HTML);

    $eventdata = new stdClass();
    $eventdata->component         = 'moodle';
    $eventdata->name              = 'instantmessage';
    $eventdata->userfrom          = $userfrom;
    $eventdata->userto            = $userto; // @TODO
    $eventdata->subject           = $badge->messagesubject;
    $eventdata->fullmessage       = $plaintext;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = $badge->message;
    $eventdata->smallmessage      = '';
    $eventdata->notification      = 1;

    message_send($eventdata);

    // Notify badge creator about the award.
    if ($badge->notification) {
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator; // @TODO
        $eventdata->subject           = $badge->messagesubject;
        $eventdata->fullmessage       = $plaintext;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $badge->message;
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;

        message_send($eventdata);
    }
}

/**
 * Gets all badges.
 *
 * @param int Type of badges to return
 * @param int Course ID for course badges
 * @param bool $visible Return only available badges
 * @param string $sort An SQL field to sort by
 * @param string $dir The sort direction ASC|DESC
 * @param int $page The page or records to return
 * @param int $perpage The number of records to return per page
 * @param string $search A simple string to search for
 * @param int $user User specific search
 */
function get_badges($type, $courseid = 0, $visible = true, $sort = '', $dir = '',
                            $page = 0, $perpage = 20, $search = '', $user = 0) {
    global $DB;
    $records = array();
    $params = array();
    $where = "b.status != :deleted AND b.context = :context ";
    $params['deleted'] = BADGE_STATUS_ARCHIVED;

    $userfields = array('b.id');
    $usersql = "";
    if ($user != 0) {
        $userfields[] = 'bi.dateissued';
        $userfields[] = 'bi.uniquehash';
        $usersql = " LEFT JOIN {badge_issued} bi ON b.id = bi.badgeid AND bi.userid = :userid ";
        $params['userid'] = $user;
        $where .= " AND (b.status = 1 OR b.status = 3) ";
    }
    $fields = implode(', ', $userfields);

    if ($courseid != 0 ) {
        $where .= "AND b.courseid = :courseid ";
        $params['courseid'] = $courseid;
    }

    if ($visible) {
        $where .= "AND b.visible = 1 ";
    }

    $sorting = (($sort != '' && $dir != '') ? 'ORDER BY ' . $sort . ' ' . $dir : '');
    $params['context'] = $type;

    $sql = "SELECT $fields FROM {badge} b $usersql WHERE $where $sorting";
    $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    $badges = array();
    foreach ($records as $r) {
        $badge = new badge($r->id);
        $badges[$r->id] = $badge;
        if ($user != 0) {
            $badges[$r->id]->dateissued = $r->dateissued;
            $badges[$r->id]->uniquehash = $r->uniquehash;
        } else {
            $badges[$r->id]->awards = count($badge->get_awards());
            $badges[$r->id]->statstring = $badge->get_status_name();
        }
    }
    return $badges;
}

/**
 * Get badges for a specific user.
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
        if ($record->context == BADGE_TYPE_SITE) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($record->courseid);
        }

        $url = new moodle_url('/badges/badge.php', array('hash' => $hash));

        // Recipient's email is hashed: <algorithm>$<hash(email + salt)>.
        $a['recipient'] = 'sha256$' . hash('sha256', $record->email . $CFG->badges_defaultbadgesalt);
        $a['salt'] = $CFG->badges_defaultbadgesalt;

        if ($record->dateexpire) {
            $a['expires'] = date('Y-m-d', $record->dateexpire);
        }

        $a['issued_on'] = date('Y-m-d', $record->dateissued);
        $a['evidence'] = $url->out(); // Issued badge URL.
        $a['badge'] = array();
        $a['badge']['version'] = '0.5.0'; // Version of OBI specification, 0.5.0 - current beta.
        $a['badge']['name'] = $record->name;
        $a['badge']['image'] = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $record->id, '/', 'f1')->out();
        $a['badge']['description'] = $record->description;
        $a['badge']['criteria'] = $url->out(); // Issued badge URL.
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
                    array('type' => BADGE_TYPE_COURSE, 'id' => $course->id));

            $coursenode->add(get_string('coursebadges', 'badges'), $url,
                    navigation_node::TYPE_CONTAINER, null, 'coursebadges',
                    new pix_icon('i/badge', get_string('coursebadges', 'badges')));

            if (has_capability('moodle/badges:viewawarded', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/index.php',
                        array('type' => BADGE_TYPE_COURSE, 'id' => $course->id));

                $coursenode->get('coursebadges')->add(get_string('managebadges', 'badges'), $url,
                    navigation_node::TYPE_SETTING, null, 'coursebadges');
            }

            if (has_capability('moodle/badges:createbadge', $coursecontext)) {
                $url = new moodle_url($CFG->wwwroot . '/badges/newbadge.php',
                        array('type' => BADGE_TYPE_COURSE, 'id' => $course->id));

                $coursenode->get('coursebadges')->add(get_string('newbadge', 'badges'), $url,
                        navigation_node::TYPE_SETTING, null, 'newbadge');
            }
        }
    }
}

/**
 * Triggered by the badges_award_criteria_review event, this function
 * marks a badge as awarded to user if all criteria are met
 *
 * @param   object      $eventdata
 * @return  boolean
 */
function badges_award_handle_TYPE_criteria_review($eventdata) {
    $criteriadata = (array)$eventdata;
    $criteria = award_criteria::build($criteriadata);

    // Badge award workflow.

    // Calc if event trigger is among badge criteria:
    // If no -> stop
    // If yes ->
    //     Is this badge active?
    //     If no -> stop
    //     If yes ->
    //         Calc if triggered criteria met:
    //         If not -> stop
    //         If yes ->
    //            Mark it complete
    //            Calc if overall criteria met:
    //            If not -> stop
    //            If yes ->
    //                 Mark overall criteria complete.
    //                 Issue badge to a user.

    // Pseudocode
    // $criteriadata = (array)$eventdata;
    // $criteria = new award_criteria_TYPE($criteriadata);
    // // If it's not criteria, finish here.
    // if (!$criteria->id) {
    //    return true;
    // }
    //
    // $badge = new badge($criteria->badgeid);
    // if (!$badge->is_active()) {
    //     return true;
    // }
    //
    // if ($criteria->review($userid)) {
    //     $criteria->mark_complete($userid);
    //
    //     if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
    //         $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
    //         $badge->issue($userid);
    //     }
    // }

    return true;
}

/**
 * Process badge image from form data
 *
 * @param badge $badge Badge object
 * @param string $iconfile Original file
 */
function badges_process_badge_image(badge $badge, $iconfile) {
    global $CFG, $DB, $USER;
    require_once($CFG->libdir. '/gdlib.php');

    if (!empty($CFG->gdversion)) {
        if ($fileid = (int)process_new_icon($badge->get_context(), 'badges', 'badgeimage', $badge->id, $iconfile)) {
            $badge->image = $fileid;
            $badge->save();
        }
        @unlink($iconfile);

        // Clean up file draft area after badge image has been saved.
        $context = context_user::instance($USER->id, MUST_EXIST);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'user', 'draft');
    }
}

/**
 * Print badge image.
 *
 * @param badge $badge Badge object
 * @param stdClass $context
 * @param string $size
 */
function print_badge_image($badge, $context, $size = 'small') {
    $image = '';

    if ($size == 'small') {
        $fsize = 'f2';
    } else {
        $fsize = 'f1';
    }

    $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', $fsize, false);
    $attributes = array('src' => $imageurl, 'alt' => s($badge->name));
    $image .= html_writer::empty_tag('img', $attributes);

    return $image;
}

/**
 * Bake issued badge.
 *
 * @param string $hash Unique hash of an issued badge.
 * @param int $badgeid ID of the original badge.
 */
function bake($hash, $badgeid) {
    global $CFG, $USER;
    require_once(dirname(dirname(__FILE__)) . '/badges/utils/bakerlib.php');

    $badge = new badge($badgeid);
    $badge_context = $badge->get_context();
    $user_context = context_user::instance($USER->id);

    $fs = get_file_storage();
    if (!$fs->file_exists($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash . '.png')) {
        $file = $fs->get_file($badge_context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1.png');
        $contents = $file->get_content();

        $filehandler = new PNG_MetaDataHandler($contents);
        $assertion = new moodle_url('/badges/badge.php', array('b' => $hash));
        if ($filehandler->check_chunks("tEXt", "openbadges")) {
            // Add assertion URL tExt chunk.
            $newcontents = $filehandler->add_chunks("tEXt", "openbadges", $assertion->out(false));
            $fileinfo = array(
                    'contextid' => $user_context->id,
                    'component' => 'badges',
                    'filearea' => 'userbadge',
                    'itemid' => $badge->id,
                    'filepath' => '/',
                    'filename' => $hash . '.png',
            );

            // Create a file with added contents.
            $fs->create_file_from_string($fileinfo, $newcontents);
        }
    }

    $fileurl = moodle_url::make_pluginfile_url($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash, true);
    header('Location: ' . $fileurl);
}
