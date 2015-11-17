@tool_behat
Feature: Verify that all form fields values can be get and set
  In order to use behat steps definitions
  As a test writer
  I need to verify it all works in real moodle forms

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | email | firstname | lastname |
      | student1 | s1@example.com | Student | 1 |
      | student2 | s2@example.com | Student | 2 |
      | student3 | s3@example.com | Student | 3 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | admin | C1 | editingteacher |
    And the following "groups" exist:
      | name | description | course | idnumber |
      | Group 1 | G1 description | C1 | G1 |
      | Group 2 | G1 description | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
      | student2 | G2 |
      | student3 | G2 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | firstpagetitle | wikimode | visible |
      | wiki | C1 | wiki1 | Test this one | Test this one | Test this one | collaborative | 0 |
    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Reset"
    # Select (multi-select) - Checking "the select box should contain".
    And I expand all fieldsets
    And the "Unenrol users" select box should contain "No roles"
    And the "Unenrol users" select box should contain "Student"
    And the "Unenrol users" select box should contain "Non-editing teacher"
    And the "Unenrol users" select box should contain "Teacher"
    And the "Unenrol users" select box should contain "Manager"
    And the "Unenrol users" select box should contain "No roles, Student, Non-editing teacher, Teacher, Manager"
    And the "Unenrol users" select box should contain "Manager, Teacher, Non-editing teacher, Student, No roles"
    And the "Unenrol users" select box should not contain "President"
    And the "Unenrol users" select box should not contain "Baker"
    And the "Unenrol users" select box should not contain "President, Baker"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I follow "Test this one"
    And I press "Create page"
    # Text (textarea & editor) & Select (multi-select) - Checking "I set the following fields to these values".
    When I set the following fields to these values:
      | HTML format | Student page contents |
    And I press "Save"
    Then I should see "Student page contents" in the "region-main" "region"
    And I follow "Edit"
    # Select (multi-select) - Checking "I set the field".
    And I follow "Edit settings"
    And I expand all fieldsets
    # Checkbox - Checking "I set the field".
    And I set the field "Display description on course page" to "1"
    # Checkbox - Checking "I set the following fields to these values:".
    And I set the following fields to these values:
      | Force format | 1 |
    # Checkbox - Checking "the field matches value" and "the field does not match value".
    And the field "Display description on course page" matches value "1"
    And the field "Display description on course page" does not match value ""
    And I press "Save and return to course"
    And I should see "Test this one"
    And I follow "Test this one"
    And I follow "Edit settings"
    # Checkbox - Checking "the field matches value" and "the following fields match these values".
    And the following fields match these values:
      | Display description on course page | 1 |
      | Default format | HTML |
      | Wiki name | Test this one |
    And the field "Force format" matches value "1"
    # Select (simple) - Checking "I set the following fields to these values:".
    And I set the following fields to these values:
      | Default format | NWiki |
      | Display description on course page | |
    # Checkbox - Checking "I set the field" to uncheck.
    And I set the field "Force format" to ""
    # Select (simple) - Checking "I set the field".
    And I set the field "Group mode" to "Separate groups"
    And I press "Save and display"
    And I follow "Edit settings"
    And the following fields match these values:
      | Default format | NWiki |
      | Group mode | Separate groups |
      | Display description on course page | |
      | Force format | |
    # All fields - Checking "the following fields do not match these values".
    And the following fields do not match these values:
      | Wiki name | Test this one baby |
      | Default format | HTML |
      | Force format | 1 |
    And I press "Cancel"
    And I follow "Course 1"
    # Radio - Checking "I set the field" and "the field matches value".
    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Test choice name |
      | Description | Test choice description |
      | Allow choice to be updated | Yes |
      | Option 1 | one |
      | Option 2 | two |
      | Option 3 | three |
    And I follow "Test choice name"
    And I set the field "one" to "1"
    And I press "Save my choice"
    And the field "one" matches value "1"
    And the field "two" matches value ""
    # Check if field xpath set/match works.
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Edit settings" node in "Course administration"
    And I set the field with xpath "//input[@id='id_idnumber']" to "Course id number"
    And the field with xpath "//input[@name='idnumber']" matches value "Course id number"
    And the field with xpath "//input[@name='idnumber']" does not match value ""
    And I press "Save and display"
    And I navigate to "Edit settings" node in "Course administration"
    And the field "Course ID number" matches value "Course id number"


  Scenario: with JS disabled all form fields getters and setters works as expected

  @javascript
  Scenario: with JS enabled all form fields getters and setters works as expected
    Then I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    # Select (multi-select & AJAX) - Checking "I set the field" and "select box should contain".
    And I set the field "groups" to "Group 2"
    And the "members" select box should contain "Student 2"
    And the "members" select box should contain "Student 3"
    And the "members" select box should not contain "Student 1"
    And I set the field "groups" to "Group 1"
    And the "members" select box should contain "Student 1"
    And the "members" select box should contain "Student 2"
    And the "members" select box should not contain "Student 3"
    # Checkbox (AJAX) - Checking "I set the field" and "I set the following fields to these values".
    And I follow "Course 1"
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Test lesson |
      | Description | Test lesson description |
      | available[enabled] | 1 |
    And I set the field "deadline[enabled]" to "1"
    # Checkbox (AJAX) - Checking "the field matches value" before saving.
    And the field "available[enabled]" matches value "1"
    And the "available[day]" "field" should be enabled
    And the field "deadline[enabled]" matches value "1"
    And I press "Save and display"
    And I follow "Edit settings"
    And the field "available[enabled]" matches value "1"
    And the "available[day]" "field" should be enabled
    And the field "deadline[enabled]" matches value "1"
    And I press "Cancel"
