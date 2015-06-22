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
 * Tests for Moodle 2 format backup operation.
 *
 * @package core_backup
 * @copyright 2014 Russell Smith
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/course/format/topics/lib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Tests for Moodle 2 course format section_options backup operation.
 *
 * @package core_backup
 * @copyright 2014 Russell Smith
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_moodle2_course_format_testcase extends advanced_testcase {

    /**
     * Tidy up open files that may be left open.
     */
    protected function tearDown() {
        gc_collect_cycles();
    }

    /**
     * Tests a backup and restore adds the required section option data
     * when the same course format is used.
     */
    public function test_course_format_options_restore() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
            array('format' => 'test_cs_options', 'numsections' => 3,
                'enablecompletion' => COMPLETION_ENABLED),
            array('createsections' => true));

        $courseobject = format_base::instance($course->id);
        $section = $DB->get_record('course_sections',
            array('course' => $course->id, 'section' => 1), '*', MUST_EXIST);
        $data = array('id' => $section->id,
            'numdaystocomplete' => 2);
        $courseobject->update_section_format_options($data);
        // Backup and restore it.
        $this->backup_and_restore($course);

        $sectionoptions = $courseobject->get_format_options(1);
        $this->assertArrayHasKey('numdaystocomplete', $sectionoptions);
        $this->assertEquals(2, $sectionoptions['numdaystocomplete']);
    }

    /**
     * Tests an import into the same subject successfully
     * restores the options without error.
     */
    public function test_course_format_options_import_myself() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('format' => 'test_cs_options', 'numsections' => 3,
                    'enablecompletion' => COMPLETION_ENABLED),
                array('createsections' => true));

        $courseobject = format_base::instance($course->id);
        $section = $DB->get_record('course_sections',
            array('course' => $course->id, 'section' => 1), '*', MUST_EXIST);
        $data = array('id' => $section->id,
                      'numdaystocomplete' => 2);
        $courseobject->update_section_format_options($data);

        $this->backup_and_restore($course, $course);

        $sectionoptions = $courseobject->get_format_options(1);
        $this->assertArrayHasKey('numdaystocomplete', $sectionoptions);
        $this->assertArrayNotHasKey('secondparameter', $sectionoptions);
        $this->assertEquals(2, $sectionoptions['numdaystocomplete']);
    }

    /**
     * Tests that all section options are copied when the course format is changed.
     * None of the data is copied.
     *
     * It is a future enhancement to copy;
     * 1. Only the relevant options.
     * 2. Only the data associated with relevant options.
     */
    public function test_course_format_options_restore_new_format() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
            array('format' => 'test_cs2_options', 'numsections' => 3,
                'enablecompletion' => COMPLETION_ENABLED),
            array('createsections' => true));
        $newcourse = $generator->create_course(
            array('format' => 'test_cs_options', 'numsections' => 3,
                'enablecompletion' => COMPLETION_ENABLED),
            array('createsections' => true));

        $courseobject = format_base::instance($course->id);
        $section = $DB->get_record('course_sections',
            array('course' => $course->id, 'section' => 1), '*', MUST_EXIST);

        $data = array('id' => $section->id,
            'numdaystocomplete' => 2,
            'secondparameter' => 8);
        $courseobject->update_section_format_options($data);
        // Backup and restore it.
        $this->backup_and_restore($course, $newcourse);

        $newcourseobject = format_base::instance($newcourse->id);
        $sectionoptions = $newcourseobject->get_format_options(1);
        $this->assertArrayHasKey('numdaystocomplete', $sectionoptions);
        $this->assertArrayHasKey('secondparameter', $sectionoptions);
        $this->assertEquals(0, $sectionoptions['numdaystocomplete']);
        $this->assertEquals(0, $sectionoptions['secondparameter']);
    }

    /**
     * Backs a course up and restores it.
     *
     * @param stdClass $srccourse Course object to backup
     * @param stdClass $dstcourse Course object to restore into
     * @return int ID of newly restored course
     */
    protected function backup_and_restore($srccourse, $dstcourse = null) {
        global $USER, $CFG;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $srccourse->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Do restore to new course with default settings.
        if ($dstcourse !== null) {
            $newcourseid = $dstcourse->id;
        } else {
            $newcourseid = restore_dbops::create_new_course(
                $srccourse->fullname, $srccourse->shortname . '_2', $srccourse->category);
        }
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }
}

/**
 * Class format_test_cs_options
 *
 * Test course format that has 1 option.
 */
class format_test_cs_options extends format_topics {
    public function section_format_options($foreditform = false) {
        return array(
            'numdaystocomplete' => array(
                 'type' => PARAM_INT,
                 'label' => 'Test days',
                 'element_type' => 'text',
                 'default' => 0,
             ),
         );
    }
}

/**
 * Class format_test_cs2_options
 *
 * Test course format that has 2 options, 1 inherited.
 */
class format_test_cs2_options extends format_test_cs_options {
    public function section_format_options($foreditform = false) {
        return array(
            'secondparameter' => array(
                'type' => PARAM_INT,
                'label' => 'Test Parmater',
                'element_type' => 'text',
                'default' => 0,
            ),
        ) + parent::section_format_options($foreditform);
    }
}