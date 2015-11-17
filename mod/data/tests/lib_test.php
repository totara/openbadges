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
 * Unit tests for lib.php
 *
 * @package    mod_data
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/data/lib.php');

/**
 * Unit tests for lib.php
 *
 * @package    mod_data
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_data_lib_testcase extends advanced_testcase {

    public function test_data_delete_record() {
        global $DB;

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";

        $module = $this->getDataGenerator()->create_module('data', $record);

        $field = data_get_field_new('text', $module);

        $fielddetail = new stdClass();
        $fielddetail->d = $module->id;
        $fielddetail->mode = 'add';
        $fielddetail->type = 'text';
        $fielddetail->sesskey = sesskey();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        // Check to make sure that we have a database record.
        $data = $DB->get_records('data', array('id' => $module->id));
        $this->assertEquals(1, count($data));

        $datacontent = $DB->get_records('data_content', array('id' => $contentid));
        $this->assertEquals(1, count($datacontent));

        $datafields = $DB->get_records('data_fields', array('id' => $field->field->id));
        $this->assertEquals(1, count($datafields));

        $datarecords = $DB->get_records('data_records', array('id' => $recordid));
        $this->assertEquals(1, count($datarecords));

        // Test to see if a failed delete returns false.
        $result = data_delete_record(8798, $module, $course->id, $cm->id);
        $this->assertFalse($result);

        // Delete the record.
        $result = data_delete_record($recordid, $module, $course->id, $cm->id);

        // Check that all of the record is gone.
        $datacontent = $DB->get_records('data_content', array('id' => $contentid));
        $this->assertEquals(0, count($datacontent));

        $datarecords = $DB->get_records('data_records', array('id' => $recordid));
        $this->assertEquals(0, count($datarecords));

        // Make sure the function returns true on a successful deletion.
        $this->assertTrue($result);
    }

    /**
     * Test comment_created event.
     */
    public function test_data_comment_created_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module);

        $fielddetail = new stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        $context = context_module::instance($module->cmid);
        $cmt = new stdClass();
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'database_entry';
        $cmt->itemid = $recordid;
        $cmt->showcount = true;
        $cmt->component = 'mod_data';
        $comment = new comment($cmt);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->add('New comment');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new moodle_url('/mod/data/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_data_comment_deleted_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module);

        $fielddetail = new stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        $context = context_module::instance($module->cmid);
        $cmt = new stdClass();
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'database_entry';
        $cmt->itemid = $recordid;
        $cmt->showcount = true;
        $cmt->component = 'mod_data';
        $comment = new comment($cmt);
        $newcomment = $comment->add('New comment 1');

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new moodle_url('/mod/data/view.php', array('id' => $module->cmid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the user
     * has the mod/data:manageentries capability.
     */
    public function test_data_user_can_manage_entry_return_true_with_capability() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();

        $this->setUser($user);

        assign_capability('mod/data:manageentries', CAP_ALLOW, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the user has mod/data:manageentries capability');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the data
     * is set to readonly.
     */
    public function test_data_user_can_manage_entry_return_false_readonly() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        // Causes readonly mode to be enabled.
        $data = new stdClass();
        $now = time();
        // Add a small margin around the periods to prevent errors with slow tests.
        $data->timeviewfrom = $now - 1;
        $data->timeviewto = $now + 5;

        $this->assertFalse(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns false if the data is read only');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the record
     * can't be found in the database.
     */
    public function test_data_user_can_manage_entry_return_false_no_record() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        // Pass record id instead of object to force DB lookup.
        $this->assertFalse(data_user_can_manage_entry(1, $data, $context),
            'data_user_can_manage_entry() returns false if the record cannot be found');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the record
     * isn't owned by the user.
     */
    public function test_data_user_can_manage_entry_return_false_not_owned_record() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // Make sure the record isn't owned by this user.
        $record->userid = $user->id + 1;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertFalse(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns false if the record isnt owned by the user');
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the data
     * doesn't require approval.
     */
    public function test_data_user_can_manage_entry_return_true_data_no_approval() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record doesn't need approval.
        $data->approval = false;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the record doesnt require approval');
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the record
     * isn't yet approved.
     */
    public function test_data_user_can_manage_entry_return_true_record_unapproved() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record needs approval.
        $data->approval = true;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;
        // The record hasn't yet been approved.
        $record->approved = false;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the record is not yet approved');
    }

    /**
     * Checks that data_user_can_manage_entry will return the 'manageapproved'
     * value if the record has already been approved.
     */
    public function test_data_user_can_manage_entry_return_manageapproved() {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record needs approval.
        $data->approval = true;
        // Can the user managed approved records?
        $data->manageapproved = false;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;
        // The record has been approved.
        $record->approved = true;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $canmanageentry = data_user_can_manage_entry($record, $data, $context);

        // Make sure the result of the check is what ever the manageapproved setting
        // is set to.
        $this->assertEquals($data->manageapproved, $canmanageentry,
            'data_user_can_manage_entry() returns the manageapproved setting on approved records');
    }

    /**
     * Helper method to create a set of test data for data_user_can_manage tests
     *
     * @return array contains user, course, roleid, module, context and record
     */
    private function create_user_test_data() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $roleid = $this->getDataGenerator()->create_role();
        $record = new stdClass();
        $record->name = "test name";
        $record->intro = "test intro";
        $record->comments = 1;
        $record->course = $course->id;
        $record->userid = $user->id;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);
        $context = context_module::instance($module->cmid);

        $this->getDataGenerator()->role_assign($roleid, $user->id, $context->id);

        return array(
            'user' => $user,
            'course' => $course,
            'roleid' => $roleid,
            'module' => $module,
            'context' => $context,
            'record' => $record
        );
    }

    /**
     * Tests for mod_data_rating_can_see_item_ratings().
     *
     * @throws coding_exception
     * @throws rating_exception
     */
    public function test_mod_data_rating_can_see_item_ratings() {
        global $DB;

        $this->resetAfterTest();

        // Setup test data.
        $course = new stdClass();
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        $course = $this->getDataGenerator()->create_course($course);
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('data', $data->id);
        $context = context_module::instance($cm->id);

        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Groups and stuff.
        $role = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, $role->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1, $user1);
        groups_add_member($group1, $user2);
        groups_add_member($group2, $user3);
        groups_add_member($group2, $user4);

        // Add data.
        $field = data_get_field_new('text', $data);

        $fielddetail = new stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();

        // Add a record with a group id of zero (all participants).
        $recordid1 = data_add_record($data, 0);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid1;
        $datacontent['content'] = 'Obelix';
        $DB->insert_record('data_content', $datacontent);

        $recordid = data_add_record($data, $group1->id);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';
        $DB->insert_record('data_content', $datacontent);

        // Now try to access it as various users.
        unassign_capability('moodle/site:accessallgroups', $role->id);
        // Eveyone should have access to the record with the group id of zero.
        $params1 = array('contextid' => 2,
                        'component' => 'mod_data',
                        'ratingarea' => 'entry',
                        'itemid' => $recordid1,
                        'scaleid' => 2);

        $params = array('contextid' => 2,
                        'component' => 'mod_data',
                        'ratingarea' => 'entry',
                        'itemid' => $recordid,
                        'scaleid' => 2);

        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertFalse(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertFalse(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

        // Now try with accessallgroups cap and make sure everything is visible.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $role->id, $context->id);
        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

        // Change group mode and verify visibility.
        $course->groupmode = VISIBLEGROUPS;
        $DB->update_record('course', $course);
        unassign_capability('moodle/site:accessallgroups', $role->id);
        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

    }
}
