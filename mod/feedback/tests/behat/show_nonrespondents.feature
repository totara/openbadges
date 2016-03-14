@mod @mod_feedback
Feature: Show users who have not responded to the feedback survey
  In order to harass students about completing the feedback
  As a teacher
  I need to see which students haven't responded

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "groups" exist:
      | course | name | idnumber |
      | C1     | G1   | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI1   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
    And the following "grouping groups" exist:
      | grouping | group  |
      | GXI1     | GI1    |
    And I log in as "admin"
    And I navigate to "Manage activities" node in "Site administration > Plugins > Activity modules"
    And I click on "Show" "link" in the "Feedback" "table_row"
    And I log out

  @javascript
  Scenario: See users who have not responded
    # Set up a feedback.
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Feedback" to section "1" and I fill the form with:
      | Name                | Frogs                                             |
      | Description         | x                                                 |
      | Record user names   | User's name will be logged and shown with answers |
      | Access restrictions | Grouping: GX1                                     |
    And I follow "Frogs"
    And I follow "Edit questions"
    And I set the field "Add question" to "Short text answer"
    And I set the following fields to these values:
      | Question | Y/N? |
    And I press "Save question"
    And I log out

    # Go in as student 1 and do the feedback.
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Frogs"
    And I follow "Answer the questions"
    And I set the field "Y/N?" to "Y"
    And I press "Submit your answers"
    And I log out

    # Go in as teacher and check the users who haven't completed it.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Frogs"
    And I follow "Show non-respondents"

    # Should only show student 2; not student 1 (they did it) or 3 (not in grouping).
    Then I should see "Student 2"
    And I should not see "Student 1"
    And I should not see "Student 3"
