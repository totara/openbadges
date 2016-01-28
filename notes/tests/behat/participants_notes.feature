@core @core_notes
Feature: Add notes to course participants
  In order to share information with other staff
  As a teacher
  I need to add notes from the course particpants list

  Scenario: An teacher can add multiple notes
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Participants"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Student 1')]//input[@type='checkbox']" to "1"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Student 2')]//input[@type='checkbox']" to "1"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Student 3')]//input[@type='checkbox']" to "1"
    And I set the field "With selected users..." to "Add a new note"
    And I press "OK"
    # Add a note to student 1, but leave student 2 empty and student 3 with space.
    When I set the field with xpath "//tr[contains(normalize-space(.), 'Student 1')]//textarea" to "Student 1 needs to pick up his game"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Student 2')]//textarea" to ""
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Student 3')]//textarea" to "  "
    And I press "Save changes"
    And I follow "Student 1"
    And I follow "Notes"
    # Student 1 has note from Teacher
    Then I should see "Teacher" in the "region-main" "region"
    And I should see "Student 1 needs to pick up his game"
    And I follow "Participants"
    And I follow "Student 2"
    And I follow "Notes"
    And I follow "Course 1"
    And I follow "Participants"
    And I follow "Notes"
    Then I should see "Student 1"
    And I should see "Student 1 needs to pick up his game"
    # Verify Student 2 does not have a note added.
    And I should not see "Student 2"
    And I should not see "Student 3"
