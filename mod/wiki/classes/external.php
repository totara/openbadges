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
 * Wiki module external API.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');

/**
 * Wiki module external functions.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_wiki_external extends external_api {

    /**
     * Describes the parameters for get_wikis_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course ID'), 'Array of course ids.', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of wikis in a provided list of courses,
     * if no list is provided all wikis that the user can view will be returned.
     *
     * @param array $courseids The courses IDs.
     * @return array Containing a list of warnings and a list of wikis.
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses($courseids = array()) {

        $returnedwikis = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_wikis_by_courses_parameters(), array('courseids' => $courseids));

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the wikis in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $wikis = get_all_instances_in_courses('wiki', $courses);

            foreach ($wikis as $wiki) {

                $context = context_module::instance($wiki->coursemodule);

                // Entry to return.
                $module = array();

                // First, we return information that any user can see in (or can deduce from) the web interface.
                $module['id'] = $wiki->id;
                $module['coursemodule'] = $wiki->coursemodule;
                $module['course'] = $wiki->course;
                $module['name']  = external_format_string($wiki->name, $context->id);

                $viewablefields = [];
                if (has_capability('mod/wiki:viewpage', $context)) {
                    list($module['intro'], $module['introformat']) =
                        external_format_text($wiki->intro, $wiki->introformat, $context->id, 'mod_wiki', 'intro', $wiki->id);

                    $viewablefields = array('firstpagetitle', 'wikimode', 'defaultformat', 'forceformat', 'editbegin', 'editend',
                                            'section', 'visible', 'groupmode', 'groupingid');
                }

                // Check additional permissions for returning optional private settings.
                if (has_capability('moodle/course:manageactivities', $context)) {
                    $additionalfields = array('timecreated', 'timemodified');
                    $viewablefields = array_merge($viewablefields, $additionalfields);
                }

                foreach ($viewablefields as $field) {
                    $module[$field] = $wiki->{$field};
                }

                // Check if user can add new pages.
                $module['cancreatepages'] = wiki_can_create_pages($context);

                $returnedwikis[] = $module;
            }
        }

        $result = array();
        $result['wikis'] = $returnedwikis;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_wikis_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_wikis_by_courses_returns() {

        return new external_single_structure(
            array(
                'wikis' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Wiki ID.'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module ID.'),
                            'course' => new external_value(PARAM_INT, 'Course ID.'),
                            'name' => new external_value(PARAM_RAW, 'Wiki name.'),
                            'intro' => new external_value(PARAM_RAW, 'Wiki intro.', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('Wiki intro format.', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'Time of creation.', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification.', VALUE_OPTIONAL),
                            'firstpagetitle' => new external_value(PARAM_RAW, 'First page title.', VALUE_OPTIONAL),
                            'wikimode' => new external_value(PARAM_TEXT, 'Wiki mode (individual, collaborative).', VALUE_OPTIONAL),
                            'defaultformat' => new external_value(PARAM_TEXT, 'Wiki\'s default format (html, creole, nwiki).',
                                                                            VALUE_OPTIONAL),
                            'forceformat' => new external_value(PARAM_INT, '1 if format is forced, 0 otherwise.',
                                                                            VALUE_OPTIONAL),
                            'editbegin' => new external_value(PARAM_INT, 'Edit begin.', VALUE_OPTIONAL),
                            'editend' => new external_value(PARAM_INT, 'Edit end.', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section ID.', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, '1 if visible, 0 otherwise.', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode.', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group ID.', VALUE_OPTIONAL),
                            'cancreatepages' => new external_value(PARAM_BOOL, 'True if user can create pages.'),
                        ), 'Wikis'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_wiki.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_wiki_parameters() {
        return new external_function_parameters (
            array(
                'wikiid' => new external_value(PARAM_INT, 'Wiki instance ID.')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $wikiid The wiki instance ID.
     * @return array of warnings and status result.
     * @since Moodle 3.1
     */
    public static function view_wiki($wikiid) {

        $params = self::validate_parameters(self::view_wiki_parameters(),
                                            array(
                                                'wikiid' => $wikiid
                                            ));
        $warnings = array();

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki($params['wikiid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Permission validation.
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check if user can view this wiki.
        // We don't use wiki_user_can_view because it requires to have a valid subwiki for the user.
        if (!has_capability('mod/wiki:viewpage', $context)) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        }

        // Trigger course_module_viewed event and completion.
        wiki_view($wiki, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_wiki return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_wiki_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success.'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for view_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_page_parameters() {
        return new external_function_parameters (
            array(
                'pageid' => new external_value(PARAM_INT, 'Wiki page ID.'),
            )
        );
    }

    /**
     * Trigger the page viewed event and update the module completion status.
     *
     * @param int $pageid The page ID.
     * @return array of warnings and status result.
     * @since Moodle 3.1
     * @throws moodle_exception if page is not valid.
     */
    public static function view_page($pageid) {

        $params = self::validate_parameters(self::view_page_parameters(),
                                            array(
                                                'pageid' => $pageid
                                            ));
        $warnings = array();

        // Get wiki page.
        if (!$page = wiki_get_page($params['pageid'])) {
            throw new moodle_exception('incorrectpageid', 'wiki');
        }

        // Get wiki instance.
        if (!$wiki = wiki_get_wiki_from_pageid($params['pageid'])) {
            throw new moodle_exception('incorrectwikiid', 'wiki');
        }

        // Permission validation.
        list($course, $cm) = get_course_and_cm_from_instance($wiki, 'wiki');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check if user can view this wiki.
        if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
            throw new moodle_exception('incorrectsubwikiid', 'wiki');
        }
        if (!wiki_user_can_view($subwiki, $wiki)) {
            throw new moodle_exception('cannotviewpage', 'wiki');
        }

        // Trigger page_viewed event and completion.
        wiki_page_view($wiki, $page, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_page_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success.'),
                'warnings' => new external_warnings()
            )
        );
    }

}
