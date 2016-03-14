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
 * Upgrade code for install
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * upgrade this assignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the assign module
 * @return bool
 */
function xmldb_assign_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014051201) {

        // Cleanup bad database records where assignid is missing.

        $DB->delete_records('assign_user_mapping', array('assignment'=>0));
        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014051201, 'assign');
    }

    if ($oldversion < 2014072400) {

        // Add "latest" column to submissions table to mark the latest attempt.
        $table = new xmldb_table('assign_submission');
        $field = new xmldb_field('latest', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'attemptnumber');

        // Conditionally launch add field latest.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072400, 'assign');
    }
    if ($oldversion < 2014072401) {

         // Define index latestattempt (not unique) to be added to assign_submission.
        $table = new xmldb_table('assign_submission');
        $index = new xmldb_index('latestattempt', XMLDB_INDEX_NOTUNIQUE, array('assignment', 'userid', 'groupid', 'latest'));

        // Conditionally launch add index latestattempt.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072401, 'assign');
    }
    if ($oldversion < 2014072405) {

        // Prevent running this multiple times.

        $countsql = 'SELECT COUNT(id) FROM {assign_submission} WHERE latest = ?';

        $count = $DB->count_records_sql($countsql, array(1));
        if ($count == 0) {

            // Mark the latest attempt for every submission in mod_assign.
            $maxattemptsql = 'SELECT assignment, userid, groupid, max(attemptnumber) AS maxattempt
                                FROM {assign_submission}
                            GROUP BY assignment, groupid, userid';

            $maxattemptidssql = 'SELECT souter.id
                                   FROM {assign_submission} souter
                                   JOIN (' . $maxattemptsql . ') sinner
                                     ON souter.assignment = sinner.assignment
                                    AND souter.userid = sinner.userid
                                    AND souter.groupid = sinner.groupid
                                    AND souter.attemptnumber = sinner.maxattempt';

            // We need to avoid using "WHERE ... IN(SELECT ...)" clause with MySQL for performance reason.
            // TODO MDL-29589 Remove this dbfamily exception when implemented.
            if ($DB->get_dbfamily() === 'mysql') {
                $params = array('latest' => 1);
                $sql = 'UPDATE {assign_submission}
                    INNER JOIN (' . $maxattemptidssql . ') souterouter ON souterouter.id = {assign_submission}.id
                           SET latest = :latest';
                $DB->execute($sql, $params);
            } else {
                $select = 'id IN(' . $maxattemptidssql . ')';
                $DB->set_field_select('assign_submission', 'latest', 1, $select);
            }

            // Look for grade records with no submission record.
            // This is when a teacher has marked a student before they submitted anything.
            $records = $DB->get_records_sql('SELECT g.id, g.assignment, g.userid
                                               FROM {assign_grades} g
                                          LEFT JOIN {assign_submission} s
                                                 ON s.assignment = g.assignment
                                                AND s.userid = g.userid
                                              WHERE s.id IS NULL');
            $submissions = array();
            foreach ($records as $record) {
                $submission = new stdClass();
                $submission->assignment = $record->assignment;
                $submission->userid = $record->userid;
                $submission->status = 'new';
                $submission->groupid = 0;
                $submission->latest = 1;
                $submission->timecreated = time();
                $submission->timemodified = time();
                array_push($submissions, $submission);
            }

            $DB->insert_records('assign_submission', $submissions);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2014072405, 'assign');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014122600) {
        // Delete any entries from the assign_user_flags and assign_user_mapping that are no longer required.
        if ($DB->get_dbfamily() === 'mysql') {
            $sql1 = "DELETE {assign_user_flags}
                       FROM {assign_user_flags}
                  LEFT JOIN {assign}
                         ON {assign_user_flags}.assignment = {assign}.id
                      WHERE {assign}.id IS NULL";

            $sql2 = "DELETE {assign_user_mapping}
                       FROM {assign_user_mapping}
                  LEFT JOIN {assign}
                         ON {assign_user_mapping}.assignment = {assign}.id
                      WHERE {assign}.id IS NULL";
        } else {
            $sql1 = "DELETE FROM {assign_user_flags}
                WHERE NOT EXISTS (
                          SELECT 'x' FROM {assign}
                           WHERE {assign_user_flags}.assignment = {assign}.id)";

            $sql2 = "DELETE FROM {assign_user_mapping}
                WHERE NOT EXISTS (
                          SELECT 'x' FROM {assign}
                           WHERE {assign_user_mapping}.assignment = {assign}.id)";
        }

        $DB->execute($sql1);
        $DB->execute($sql2);

        upgrade_mod_savepoint(true, 2014122600, 'assign');
    }

    if ($oldversion < 2015022300) {

        // Define field preventsubmissionnotingroup to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('preventsubmissionnotingroup',
            XMLDB_TYPE_INTEGER,
            '2',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'sendstudentnotifications');

        // Conditionally launch add field preventsubmissionnotingroup.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2015022300, 'assign');
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
