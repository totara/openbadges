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
 * Core external functions and service definitions.
 *
 * The functions and services defined on this file are
 * processed and registered into the Moodle DB after any
 * install or upgrade operation. All plugins support this.
 *
 * For more information, take a look to the documentation available:
 *     - Webservices API: {@link http://docs.moodle.org/dev/Web_services_API}
 *     - External API: {@link http://docs.moodle.org/dev/External_functions_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package    core_webservice
 * @category   webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // Cohort related functions.

    'core_cohort_create_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'create_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Creates new cohorts.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_delete_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'delete_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Deletes all specified cohorts.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_get_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'get_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Returns cohort details.',
        'type'        => 'read',
        'capabilities'=> 'moodle/cohort:view',
    ),

    'core_cohort_update_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'update_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Updates existing cohorts.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_add_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'add_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Adds cohort members.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:assign',
    ),

    'core_cohort_delete_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'delete_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Deletes cohort members.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/cohort:assign',
    ),

    'core_cohort_get_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'get_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Returns cohort members.',
        'type'        => 'read',
        'capabilities'=> 'moodle/cohort:view',
    ),

    // Comments related functions.

    'core_comment_get_comments' => array(
        'classname'     => 'core_comment_external',
        'methodname'    => 'get_comments',
        'description'   => 'Returns comments.',
        'type'          => 'read',
        'capabilities'  => 'moodle/comment:view',
    ),

    // Grade related functions.

    'core_grades_get_grades' => array(
        'classname'     => 'core_grades_external',
        'methodname'    => 'get_grades',
        'description'   => 'Returns student course total grade and grades for activities.
                                This function does not return category or manual items.
                                This function is suitable for managers or teachers not students.',
        'type'          => 'read',
        'capabilities'  => 'moodle/grade:view, moodle/grade:viewall, moodle/grade:viewhidden',
    ),

    'core_grades_update_grades' => array(
        'classname'     => 'core_grades_external',
        'methodname'    => 'update_grades',
        'description'   => 'Update a grade item and associated student grades.',
        'type'          => 'write',
        'capabilities'  => '',
    ),

    // === group related functions ===

    'moodle_group_create_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_create_groups(). ',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_create_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Creates new groups.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_get_groups()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group details.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_course_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_course_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_get_course_groups()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_course_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_course_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns all groups in specified course.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_delete_groups()',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_delete_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes all specified groups.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_get_group_members()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group members.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_add_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'add_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_add_group_members()',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_add_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'add_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Adds group members.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_group_delete_group_members()',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_delete_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes group members.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_create_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'create_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Creates new groupings',
        'type'        => 'write',
    ),

    'core_group_update_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'update_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Updates existing groupings',
        'type'        => 'write',
    ),

    'core_group_get_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns groupings details.',
        'type'        => 'read',
    ),

    'core_group_get_course_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_course_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns all groupings in specified course.',
        'type'        => 'read',
    ),

    'core_group_delete_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes all specified groupings.',
        'type'        => 'write',
    ),

    'core_group_assign_grouping' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'assign_grouping',
        'classpath'   => 'group/externallib.php',
        'description' => 'Assing groups from groupings',
        'type'        => 'write',
    ),

    'core_group_unassign_grouping' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'unassign_grouping',
        'classpath'   => 'group/externallib.php',
        'description' => 'Unassing groups from groupings',
        'type'        => 'write',
    ),

    'core_group_get_course_user_groups' => array(
        'classname'     => 'core_group_external',
        'methodname'    => 'get_course_user_groups',
        'classpath'     => 'group/externallib.php',
        'description'   => 'Returns all groups in specified course for the specified user.',
        'type'          => 'read',
        'capabilities'  => 'moodle/course:managegroups',
    ),

    'core_group_get_activity_allowed_groups' => array(
        'classname'     => 'core_group_external',
        'methodname'    => 'get_activity_allowed_groups',
        'classpath'     => 'group/externallib.php',
        'description'   => 'Gets a list of groups that the user is allowed to access within the specified activity.',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'core_group_get_activity_groupmode' => array(
        'classname'     => 'core_group_external',
        'methodname'    => 'get_activity_groupmode',
        'classpath'     => 'group/externallib.php',
        'description'   => 'Returns effective groupmode used in a given activity.',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'core_notes_get_course_notes' => array(
        'classname'     => 'core_notes_external',
        'methodname'    => 'get_course_notes',
        'classpath'     => 'notes/externallib.php',
        'description'   => 'Returns all notes in specified course (or site) for the specified user.',
        'type'          => 'read',
        'capabilities'  => 'moodle/notes:view',
    ),

    // === file related functions ===

    'moodle_file_get_files' => array(
        'classname'   => 'moodle_file_external',
        'methodname'  => 'get_files',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_files_get_files()',
        'type'        => 'read',
        'classpath'   => 'files/externallib.php',
    ),

    'core_files_get_files' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'get_files',
        'description' => 'browse moodle files',
        'type'        => 'read',
        'classpath'   => 'files/externallib.php',
    ),

    'moodle_file_upload' => array(
        'classname'   => 'moodle_file_external',
        'methodname'  => 'upload',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_files_upload()',
        'type'        => 'write',
        'classpath'   => 'files/externallib.php',
    ),

    'core_files_upload' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'upload',
        'description' => 'upload a file to moodle',
        'type'        => 'write',
        'classpath'   => 'files/externallib.php',
    ),

    // === user related functions ===

    'moodle_user_create_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_user_create_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:create',
    ),

    'core_user_create_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Create users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:create',
    ),

    'core_user_get_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'search for users matching the parameters',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'moodle_user_get_users_by_id' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. Use core_user_get_users_by_field service instead',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'core_user_get_users_by_field' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_field',
        'classpath'   => 'user/externallib.php',
        'description' => 'Retrieve users information for a specified unique field - If you want to do a user search, use core_user_get_users()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'core_user_get_users_by_id' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been replaced by core_user_get_users_by_field()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'moodle_user_get_users_by_courseid' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_users_by_courseid',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_enrol_get_enrolled_users()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_get_course_participants_by_id' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_course_participants_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_user_get_course_user_profiles()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'core_user_get_course_user_profiles' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_course_user_profiles',
        'classpath'   => 'user/externallib.php',
        'description' => 'Get course user profiles (each of the profils matching a course id and a user id).',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_delete_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_user_delete_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:delete',
    ),

    'core_user_delete_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Delete users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:delete',
    ),

    'moodle_user_update_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_user_update_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),

    'core_user_update_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Update users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),

    'core_user_add_user_device' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'add_user_device',
        'classpath'   => 'user/externallib.php',
        'description' => 'Store mobile user devices information for PUSH Notifications.',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_user_remove_user_device' => array(
        'classname'     => 'core_user_external',
        'methodname'    => 'remove_user_device',
        'classpath'     => 'user/externallib.php',
        'description'   => 'Remove a user device from the Moodle database.',
        'type'          => 'write',
        'capabilities'  => '',
    ),

    'core_user_view_user_list' => array(
        'classname'     => 'core_user_external',
        'methodname'    => 'view_user_list',
        'classpath'     => 'user/externallib.php',
        'description'   => 'Simulates the web-interface view of user/index.php (triggering events).',
        'type'          => 'write',
        'capabilities'  => 'moodle/course:viewparticipants',
    ),

    'core_user_view_user_profile' => array(
        'classname'     => 'core_user_external',
        'methodname'    => 'view_user_profile',
        'classpath'     => 'user/externallib.php',
        'description'   => 'Simulates the web-interface view of user/view.php and user/profile.php (triggering events).',
        'type'          => 'write',
        'capabilities'  => 'moodle/user:viewdetails',
    ),

    'core_user_add_user_private_files' => array(
        'classname'     => 'core_user_external',
        'methodname'    => 'add_user_private_files',
        'classpath'     => 'user/externallib.php',
        'description'   => 'Copy files from a draft area to users private files area.',
        'type'          => 'write',
        'capabilities'  => 'moodle/user:manageownfiles',
    ),

    // === enrol related functions ===

    'core_enrol_get_enrolled_users_with_capability' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users_with_capability',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'For each course and capability specified, return a list of the users that are enrolled in the course
                          and have that capability',
        'type'        => 'read',
    ),

    'moodle_enrol_get_enrolled_users' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. Please use core_enrol_get_enrolled_users() (previously known as moodle_user_get_users_by_courseid).',
        'type'        => 'read',
        'capabilities'=> 'moodle/site:viewparticipants, moodle/course:viewparticipants,
            moodle/role:review, moodle/site:accessallgroups, moodle/course:enrolreview',
    ),

    'core_enrol_get_enrolled_users' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get enrolled users by course id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_enrol_get_users_courses' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_enrol_get_users_courses()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),

    'core_enrol_get_users_courses' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get the list of courses where a user is enrolled in',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),

    'core_enrol_get_course_enrolment_methods' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_course_enrolment_methods',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get the list of course enrolment methods',
        'type'        => 'read',
    ),

    // === Role related functions ===

    'moodle_role_assign' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'role_assign',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_role_assign_role()',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'core_role_assign_roles' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'assign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role assignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'moodle_role_unassign' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'role_unassign',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_role_unassign_role()',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'core_role_unassign_roles' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'unassign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role unassignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    // === course related functions ===

    'core_course_get_contents' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_course_contents',
        'classpath'   => 'course/externallib.php',
        'description' => 'Get course contents',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'moodle_course_get_courses' => array(
        'classname'   => 'moodle_course_external',
        'methodname'  => 'get_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_course_get_courses()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'core_course_get_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return course details',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'core_course_search_courses' => array(
        'classname'     => 'core_course_external',
        'methodname'    => 'search_courses',
        'classpath'     => 'course/externallib.php',
        'description'   => 'Search courses by (name, module, block, tag)',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'moodle_course_create_courses' => array(
        'classname'   => 'moodle_course_external',
        'methodname'  => 'create_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_course_create_courses()',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:create,moodle/course:visibility',
    ),

    'core_course_create_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'create_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Create new courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:create,moodle/course:visibility',
    ),

    'core_course_delete_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'delete_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Deletes all specified courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:delete',
    ),

    'core_course_delete_modules' => array(
        'classname' => 'core_course_external',
        'methodname' => 'delete_modules',
        'classpath' => 'course/externallib.php',
        'description' => 'Deletes all specified module instances',
        'type' => 'write',
        'capabilities' => 'moodle/course:manageactivities'
    ),

    'core_course_duplicate_course' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'duplicate_course',
        'classpath'   => 'course/externallib.php',
        'description' => 'Duplicate an existing course (creating a new one) without user data',
        'type'        => 'write',
        'capabilities'=> 'moodle/backup:backupcourse,moodle/restore:restorecourse,moodle/course:create',
    ),

    'core_course_update_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'update_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Update courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:update,moodle/course:changecategory,moodle/course:changefullname,moodle/course:changeshortname,moodle/course:changeidnumber,moodle/course:changesummary,moodle/course:visibility',
    ),

    'core_course_view_course' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'view_course',
        'classpath'   => 'course/externallib.php',
        'description' => 'Log that the course was viewed',
        'type'        => 'write'
    ),

    'core_course_get_course_module' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_course_module',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return information about a course module',
        'type'        => 'read'
    ),

    'core_course_get_course_module_by_instance' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_course_module_by_instance',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return information about a given module name and instance id',
        'type'        => 'read'
    ),

    // === course category related functions ===

    'core_course_get_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return category details',
        'type'        => 'read',
        'capabilities'=> 'moodle/category:viewhiddencategories',
    ),

    'core_course_create_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'create_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Create course categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_update_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'update_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Update categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_delete_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'delete_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Delete course categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_import_course' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'import_course',
        'classpath'   => 'course/externallib.php',
        'description' => 'Import course data from a course into another course. Does not include any user data.',
        'type'        => 'write',
        'capabilities'=> 'moodle/backup:backuptargetimport, moodle/restore:restoretargetimport',
    ),

    // === message related functions ===

    'moodle_message_send_instantmessages' => array(
        'classname'   => 'moodle_message_external',
        'methodname'  => 'send_instantmessages',
        'classpath'   => 'message/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_message_send_instant_messages()',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),

    'core_message_send_instant_messages' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'send_instant_messages',
        'classpath'   => 'message/externallib.php',
        'description' => 'Send instant messages',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),

    'core_message_create_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'create_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Add contacts to the contact list',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_delete_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'delete_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Remove contacts from the contact list',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_block_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'block_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Block contacts',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_unblock_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'unblock_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Unblock contacts',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_get_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'get_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Retrieve the contact list',
        'type'        => 'read',
        'capabilities'=> '',
    ),

    'core_message_search_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'search_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Search for contacts',
        'type'        => 'read',
        'capabilities'=> '',
    ),

    'core_message_get_messages' => array(
        'classname'     => 'core_message_external',
        'methodname'    => 'get_messages',
        'classpath'     => 'message/externallib.php',
        'description'   => 'Retrieve a list of messages sent and received by a user (conversations, notifications or both)',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'core_message_get_blocked_users' => array(
        'classname'     => 'core_message_external',
        'methodname'    => 'get_blocked_users',
        'classpath'     => 'message/externallib.php',
        'description'   => 'Retrieve a list of users blocked',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'core_message_mark_message_read' => array(
        'classname'     => 'core_message_external',
        'methodname'    => 'mark_message_read',
        'classpath'     => 'message/externallib.php',
        'description'   => 'Mark a single message as read, trigger message_viewed event.',
        'type'          => 'write',
        'capabilities'  => '',
    ),

    // === notes related functions ===

    'moodle_notes_create_notes' => array(
        'classname'   => 'moodle_notes_external',
        'methodname'  => 'create_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_notes_create_notes()',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_create_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'create_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Create notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_delete_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'delete_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Delete notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_get_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'get_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Get notes',
        'type'        => 'read',
        'capabilities'=> 'moodle/notes:view',
    ),

    'core_notes_update_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'update_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Update notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_view_notes' => array(
        'classname'     => 'core_notes_external',
        'methodname'    => 'view_notes',
        'classpath'     => 'notes/externallib.php',
        'description'   => 'Simulates the web interface view of notes/index.php: trigger events.',
        'type'          => 'write',
        'capabilities'  => 'moodle/notes:view',
    ),

    // === grading related functions ===

    'core_grading_get_definitions' => array(
        'classname'   => 'core_grading_external',
        'methodname'  => 'get_definitions',
        'description' => 'Get grading definitions',
        'type'        => 'read'
    ),

    'core_grade_get_definitions' => array(
        'classname'   => 'core_grade_external',
        'methodname'  => 'get_definitions',
        'classpath'   => 'grade/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_grading_get_definitions()',
        'type'        => 'read'
    ),

    'core_grading_save_definitions' => array(
        'classname'   => 'core_grading_external',
        'methodname'  => 'save_definitions',
        'description' => 'Save grading definitions',
        'type'        => 'write'
    ),

    'core_grading_get_gradingform_instances' => array(
        'classname'   => 'core_grading_external',
        'methodname'  => 'get_gradingform_instances',
        'description' => 'Get grading form instances',
        'type'        => 'read'
    ),

    // === webservice related functions ===

    'moodle_webservice_get_siteinfo' => array(
        'classname'   => 'moodle_webservice_external',
        'methodname'  => 'get_siteinfo',
        'classpath'   => 'webservice/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_webservice_get_site_info()',
        'type'        => 'read',
    ),

    'core_webservice_get_site_info' => array(
        'classname'   => 'core_webservice_external',
        'methodname'  => 'get_site_info',
        'classpath'   => 'webservice/externallib.php',
        'description' => 'Return some site info / user info / list web service functions',
        'type'        => 'read',
    ),

    'core_get_string' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_string',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return a translated string - similar to core get_string() call',
        'type'        => 'read',
        'loginrequired' => false,
        'ajax'        => true,
    ),

    'core_get_strings' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_strings',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return some translated strings - like several core get_string() calls',
        'type'        => 'read',
        'loginrequired' => false,
        'ajax'        => true,
    ),

    'core_get_component_strings' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_component_strings',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return all raw strings (with {$a->xxx}) for a specific component
            - similar to core get_component_strings() call',
        'type'        => 'read',
        'loginrequired' => false,
        'ajax'        => true,
    ),


    // === Calendar related functions ===

    'core_calendar_delete_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'delete_calendar_events',
        'description' => 'Delete calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),


    'core_calendar_get_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'get_calendar_events',
        'description' => 'Get calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'read',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),

    'core_calendar_create_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'create_calendar_events',
        'description' => 'Create calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),

    'core_output_load_template' => array(
        'classname'   => 'core\output\external',
        'methodname'  => 'load_template',
        'description' => 'Load a template for a renderable',
        'type'        => 'read',
        'loginrequired' => false,
        'ajax'        => true,
    ),

    // Completion related functions.
    'core_completion_update_activity_completion_status_manually' => array(
        'classname'   => 'core_completion_external',
        'methodname'  => 'update_activity_completion_status_manually',
        'description' => 'Update completion status for the current user in an activity, only for activities with manual tracking.',
        'type'        => 'write',
    ),

    'core_completion_mark_course_self_completed' => array(
        'classname'   => 'core_completion_external',
        'methodname'  => 'mark_course_self_completed',
        'description' => 'Update the course completion status for the current user (if course self-completion is enabled).',
        'type'        => 'write',
    ),

    'core_completion_get_activities_completion_status' => array(
        'classname'     => 'core_completion_external',
        'methodname'    => 'get_activities_completion_status',
        'description'   => 'Return the activities completion status for a user in a course.',
        'type'          => 'read',
        'capabilities'  => '',
    ),

    'core_completion_get_course_completion_status' => array(
        'classname'    => 'core_completion_external',
        'methodname'   => 'get_course_completion_status',
        'description'  => 'Returns course completion status.',
        'type'         => 'read',
        'capabilities' => 'report/completion:view',
    ),

    // Rating functions.
    'core_rating_get_item_ratings' => array(
        'classname'     => 'core_rating_external',
        'methodname'    => 'get_item_ratings',
        'description'   => 'Retrieve all the ratings for an item.',
        'type'          => 'read',
        'capabilities'  => 'moodle/rating:view'
    ),

    // Tag functions.
   'core_tag_update_tags' => array(
        'classname'   => 'core_tag_external',
        'methodname'  => 'update_tags',
        'description' => 'Updates tags',
        'type'        => 'write',
        'ajax'        => true
    ),

    'core_tag_get_tags' => array(
        'classname'   => 'core_tag_external',
        'methodname'  => 'get_tags',
        'description' => 'Gets tags by their ids',
        'type'        => 'read',
        'ajax'        => true
    ),

);

$services = array(
   'Moodle mobile web service'  => array(
        'functions' => array (
            'moodle_enrol_get_users_courses',
            'moodle_enrol_get_enrolled_users',
            'moodle_user_get_users_by_id',
            'moodle_webservice_get_siteinfo',
            'moodle_notes_create_notes',
            'moodle_user_get_course_participants_by_id',
            'moodle_user_get_users_by_courseid',
            'moodle_message_send_instantmessages',
            'core_course_get_contents',
            'core_get_component_strings',
            'core_user_add_user_device',
            'core_calendar_get_calendar_events',
            'core_enrol_get_users_courses',
            'core_enrol_get_enrolled_users',
            'core_enrol_get_course_enrolment_methods',
            'enrol_self_enrol_user',
            'core_user_get_users_by_id',
            'core_webservice_get_site_info',
            'core_notes_create_notes',
            'core_user_get_course_user_profiles',
            'core_message_send_instant_messages',
            'mod_assign_get_grades',
            'mod_assign_get_assignments',
            'mod_assign_get_submissions',
            'mod_assign_get_user_flags',
            'mod_assign_set_user_flags',
            'mod_assign_get_user_mappings',
            'mod_assign_revert_submissions_to_draft',
            'mod_assign_lock_submissions',
            'mod_assign_unlock_submissions',
            'mod_assign_save_submission',
            'mod_assign_submit_for_grading',
            'mod_assign_save_grade',
            'mod_assign_save_user_extensions',
            'mod_assign_reveal_identities',
            'message_airnotifier_is_system_configured',
            'message_airnotifier_are_notification_preferences_configured',
            'core_grades_update_grades',
            'mod_forum_get_forums_by_courses',
            'mod_forum_get_forum_discussions_paginated',
            'mod_forum_get_forum_discussion_posts',
            'mod_forum_add_discussion_post',
            'mod_forum_add_discussion',
            'core_files_get_files',
            'core_message_get_messages',
            'core_message_create_contacts',
            'core_message_delete_contacts',
            'core_message_block_contacts',
            'core_message_unblock_contacts',
            'core_message_get_contacts',
            'core_message_search_contacts',
            'core_message_get_blocked_users',
            'gradereport_user_get_grades_table',
            'core_group_get_course_user_groups',
            'core_group_get_activity_allowed_groups',
            'core_group_get_activity_groupmode',
            'core_user_remove_user_device',
            'core_course_get_courses',
            'core_completion_update_activity_completion_status_manually',
            'core_completion_mark_course_self_completed',
            'mod_data_get_databases_by_courses',
            'core_comment_get_comments',
            'mod_forum_view_forum',
            'core_course_view_course',
            'core_course_search_courses',
            'core_course_get_course_module',
            'core_course_get_course_module_by_instance',
            'core_completion_get_activities_completion_status',
            'core_notes_get_course_notes',
            'core_completion_get_course_completion_status',
            'core_user_view_user_list',
            'core_message_mark_message_read',
            'core_notes_view_notes',
            'mod_forum_view_forum_discussion',
            'core_user_view_user_profile',
            'gradereport_user_view_grade_report',
            'core_rating_get_item_ratings',
            'mod_url_view_url',
            'core_user_get_users_by_field',
            'core_user_add_user_private_files',
            'mod_assign_view_grading_table',
            'mod_scorm_view_scorm',
            'mod_scorm_get_scorm_scoes',
            'mod_scorm_get_scorm_user_data',
            'mod_scorm_insert_scorm_tracks',
            'mod_scorm_get_scorm_sco_tracks',
            'mod_scorm_get_scorm_attempt_count',
            'mod_scorm_get_scorms_by_courses',
            'mod_survey_get_surveys_by_courses',
            'mod_survey_view_survey',
            'mod_survey_get_questions',
            'mod_survey_submit_answers',
            'mod_page_view_page',
            'mod_resource_view_resource',
            'mod_folder_view_folder',
            'mod_chat_login_user',
            'mod_chat_get_chat_users',
            'mod_chat_send_chat_message',
            'mod_chat_get_chat_latest_messages',
            'mod_chat_view_chat',
            'mod_chat_get_chats_by_courses',
            'mod_book_view_book',
            'mod_book_get_books_by_courses',
            'mod_choice_get_choice_results',
            'mod_choice_get_choice_options',
            'mod_choice_submit_choice_response',
            'mod_choice_view_choice',
            'mod_choice_get_choices_by_courses',
            'mod_choice_delete_choice_responses',
            'mod_lti_get_tool_launch_data',
            'mod_lti_get_ltis_by_courses',
            'mod_lti_view_lti',
            'mod_imscp_view_imscp',
            'mod_imscp_get_imscps_by_courses',
            ),
        'enabled' => 0,
        'restrictedusers' => 0,
        'shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE,
        'downloadfiles' => 1,
        'uploadfiles' => 1
    ),
);
