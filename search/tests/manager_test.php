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
 * Search manager unit tests.
 *
 * @package     core_search
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/testable_core_search.php');

/**
 * Unit tests for search manager.
 *
 * @package     core_search
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_manager_testcase extends advanced_testcase {

    protected $forumpostareaid = null;

    public function setUp() {
        $this->forumpostareaid = \core_search\manager::generate_areaid('mod_forum', 'post');
    }

    public function test_search_enabled() {

        $this->resetAfterTest();

        // Disabled by default.
        $this->assertFalse(\core_search\manager::is_global_search_enabled());

        set_config('enableglobalsearch', true);
        $this->assertTrue(\core_search\manager::is_global_search_enabled());

        set_config('enableglobalsearch', false);
        $this->assertFalse(\core_search\manager::is_global_search_enabled());
    }

    public function test_search_areas() {
        global $CFG;

        $this->resetAfterTest();

        set_config('enableglobalsearch', true);

        $fakeareaid = \core_search\manager::generate_areaid('mod_unexisting', 'chihuaquita');

        $searcharea = \core_search\manager::get_search_area($this->forumpostareaid);
        $this->assertInstanceOf('\core_search\area\base', $searcharea);

        $this->assertFalse(\core_search\manager::get_search_area($fakeareaid));

        $this->assertArrayHasKey($this->forumpostareaid, \core_search\manager::get_search_areas_list());
        $this->assertArrayNotHasKey($fakeareaid, \core_search\manager::get_search_areas_list());

        // Enabled by default once global search is enabled.
        $this->assertArrayHasKey($this->forumpostareaid, \core_search\manager::get_search_areas_list(true));

        list($componentname, $varname) = $searcharea->get_config_var_name();
        set_config($varname . '_enabled', false, $componentname);
        \core_search\manager::clear_static();

        $this->assertArrayNotHasKey('mod_forum', \core_search\manager::get_search_areas_list(true));

        set_config($varname . '_enabled', true, $componentname);

        // Although the result is wrong, we want to check that \core_search\manager::get_search_areas_list returns cached results.
        $this->assertArrayNotHasKey($this->forumpostareaid, \core_search\manager::get_search_areas_list(true));

        // Now we check the real result.
        \core_search\manager::clear_static();
        $this->assertArrayHasKey($this->forumpostareaid, \core_search\manager::get_search_areas_list(true));
    }

    public function test_search_config() {

        $this->resetAfterTest();

        $search = testable_core_search::instance();

        // We should test both plugin types and core subsystems. No core subsystems available yet.
        $searcharea = $search->get_search_area($this->forumpostareaid);

        list($componentname, $varname) = $searcharea->get_config_var_name();

        // Just with a couple of vars should be enough.
        $start = time() - 100;
        $end = time();
        set_config($varname . '_indexingstart', $start, $componentname);
        set_config($varname . '_indexingend', $end, $componentname);

        $configs = $search->get_areas_config(array($this->forumpostareaid => $searcharea));
        $this->assertEquals($start, $configs[$this->forumpostareaid]->indexingstart);
        $this->assertEquals($end, $configs[$this->forumpostareaid]->indexingend);

        try {
            $fakeareaid = \core_search\manager::generate_areaid('mod_unexisting', 'chihuaquita');
            $search->reset_config($fakeareaid);
            $this->fail('An exception should be triggered if the provided search area does not exist.');
        } catch (moodle_exception $ex) {
            $this->assertContains($fakeareaid . ' search area is not available.', $ex->getMessage());
        }

        // We clean it all but enabled components.
        $search->reset_config($this->forumpostareaid);
        $this->assertEquals(1, get_config($componentname, $varname . '_enabled'));
        $this->assertEquals(0, get_config($componentname, $varname . '_indexingstart'));
        $this->assertEquals(0, get_config($componentname, $varname . '_indexingend'));
        $this->assertEquals(0, get_config($componentname, $varname . '_lastindexrun'));
        // No caching.
        $configs = $search->get_areas_config(array($this->forumpostareaid => $searcharea));
        $this->assertEquals(0, $configs[$this->forumpostareaid]->indexingstart);
        $this->assertEquals(0, $configs[$this->forumpostareaid]->indexingend);

        set_config($varname . '_indexingstart', $start, $componentname);
        set_config($varname . '_indexingend', $end, $componentname);

        // All components config should be reset.
        $search->reset_config();
        $this->assertEquals(0, get_config($componentname, $varname . '_indexingstart'));
        $this->assertEquals(0, get_config($componentname, $varname . '_indexingend'));
        $this->assertEquals(0, get_config($componentname, $varname . '_lastindexrun'));
        // No caching.
        $configs = $search->get_areas_config(array($this->forumpostareaid => $searcharea));
        $this->assertEquals(0, $configs[$this->forumpostareaid]->indexingstart);
        $this->assertEquals(0, $configs[$this->forumpostareaid]->indexingend);
    }

    /**
     * Adding this test here as get_areas_user_accesses process is the same, results just depend on the context level.
     *
     * @return void
     */
    public function test_search_user_accesses() {
        global $DB;

        $this->resetAfterTest();

        $frontpage = $DB->get_record('course', array('id' => SITEID));
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();
        $noaccess = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, 'student');

        $frontpageforum = $this->getDataGenerator()->create_module('forum', array('course' => $frontpage->id));
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id));
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id));
        $frontpageforumcontext = context_module::instance($frontpageforum->cmid);
        $context1 = context_module::instance($forum1->cmid);
        $context2 = context_module::instance($forum2->cmid);
        $context3 = context_module::instance($forum3->cmid);

        $search = testable_core_search::instance();

        $this->setAdminUser();
        $this->assertTrue($search->get_areas_user_accesses());

        // Can access the frontpage ones.
        $this->setUser($noaccess);
        $contexts = $search->get_areas_user_accesses();
        $this->assertEquals(array($frontpageforumcontext->id => $frontpageforumcontext->id), $contexts[$this->forumpostareaid]);

        $this->setUser($teacher);
        $contexts = $search->get_areas_user_accesses();
        $frontpageandcourse1 = array($frontpageforumcontext->id => $frontpageforumcontext->id, $context1->id => $context1->id,
            $context2->id => $context2->id);
        $this->assertEquals($frontpageandcourse1, $contexts[$this->forumpostareaid]);

        $this->setUser($student);
        $contexts = $search->get_areas_user_accesses();
        $this->assertEquals($frontpageandcourse1, $contexts[$this->forumpostareaid]);

        // Hide the activity.
        set_coursemodule_visible($forum2->cmid, 0);
        $contexts = $search->get_areas_user_accesses();
        $this->assertEquals(array($frontpageforumcontext->id => $frontpageforumcontext->id, $context1->id => $context1->id),
            $contexts[$this->forumpostareaid]);
    }
}
