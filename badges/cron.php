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
 * Cron job for reviewing and aggregating badge award criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/badgeslib.php');

function badge_cron() {
    badge_review_cron();

    //badge_message_cron();
}

/**
 * Awards badges
 *
 * First find all badges that can be earned, then reviews each badge.
 * (Not sure how efficient this is timewise).
 */
function badge_review_cron() {
    global $DB;
    $total = 0;

    $sql = 'SELECT id
                FROM {badge}
                WHERE (status = :active OR status = :activelocked)
                    AND (context = 1 OR courseid IN
                        (SELECT id FROM {course} WHERE visible = 1 AND startdate < :current))';
    $params = array(
            'active' => BADGE_STATUS_ACTIVE,
            'activelocked' => BADGE_STATUS_ACTIVE_LOCKED,
            'current' => time());
    $badges = $DB->get_fieldset_sql($sql, $params);

    mtrace('Stared reviewing available badges.');
    foreach ($badges as $bid) {
        $badge = new badge($bid);

        if ($badge->has_criteria()) {
            if (debugging()) {
                mtrace('Processing badge "' . $badge->name . '"...');
            }

            $issued = $badge->review_all_criteria();

            if (debugging()) {
                mtrace('...badge was issued to ' . $issued . ' users.');
            }
            $total =+ $issued;
        }
    }

    mtrace('Badges were issued ' . $total . ' time(s).');
}

function badge_message_cron() {
    global $CFG, $DB;

    $admin = get_admin();
    $userfrom = new stdClass();
    $userfrom->id = $admin->id;
    $userfrom->email = $CFG->badges_defaultissuercontact ? $CFG->badges_defaultissuercontact : $admin->email;
    $userfrom->firstname = $CFG->badges_defaultissuername ? $CFG->badges_defaultissuername : $admin->firstname;
    $userfrom->lastname = $CFG->badges_defaultissuername ? '' : $admin->lastname;
    $userfrom->maildisplay = true;

    mtrace('Sending scheduled badge notifications.');

    $sql = 'SELECT  bi.badgeid, b.name, bi.uniquehash, bi.userid as recipient,
                    b.usercreated as creator, b.notification, b.timecreated
                FROM {badge_issued} bi INNER JOIN {badge} b
                    ON bi.badgeid = b.id
                WHERE bi.issuernotified IS NULL
                    AND b.notification > 1';

    if ($msgs = $DB->get_record_sql($sql)) {
        foreach ($msgs as $msg) {
            $issuedlink = html_writer::link(new moodle_url('/badges/badge.php', array('hash' => $msg->uniquehash)), $msg->name);
            $userto = $DB->get_record('user', array('id' => $msg->recipient), '*', MUST_EXIST);

            $creator = $DB->get_record('user', array('id' => $msg->creator), '*', MUST_EXIST);
            $a = new stdClass();
            $a->user = fullname($userto);
            $a->link = $issuedlink;
            $creatormessage = get_string('creatorbody', 'badges', $a);
            $creatorsubject = get_string('creatorsubject', 'badges', $msg->name);

            $eventdata = new stdClass();
            $eventdata->component         = 'moodle';
            $eventdata->name              = 'instantmessage';
            $eventdata->userfrom          = $userfrom;
            $eventdata->userto            = $creator;
            $eventdata->subject           = $creatorsubject;
            $eventdata->fullmessage       = $creatormessage;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = format_text($creatormessage, FORMAT_HTML);
            $eventdata->smallmessage      = '';

            message_send($eventdata);
            $DB->set_field('badge_issued', 'issuernotified', time(), array('badgeid' => $msg->badgeid, 'userid' => $msg->recipient));
        }
    }
}