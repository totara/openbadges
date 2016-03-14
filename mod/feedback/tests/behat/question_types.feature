@mod @mod_feedback
Feature: Test creating different types of feedback questions
  In order to create feedbacks
  As a teacher
  I need to be able to add different question types

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And I log in as "admin"
    And I navigate to "Manage activities" node in "Site administration > Plugins > Activity modules"
    And I click on "Show" "link" in the "Feedback" "table_row"
    And I log out
    And the following "activities" exist:
      | activity   | name                | course | idnumber    |
      | feedback   | Learning experience | C1     | feedback0   |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Edit questions"
    And I add a "Information" question to the feedback with:
      | Question         | this is an information question |
      | Label            | info                            |
      | Information-Type | Course                          |
    And I add a "Label" question to the feedback with:
      | Contents | label text |
    And I add a "Longer text answer" question to the feedback with:
      | Question         | this is a longer text answer |
      | Label            | longertext                   |
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option a\noption b\noption c  |
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 2        |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - multiple answers |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | option d\noption e\noption f       |
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 3        |
      | Label                          | multichoice3                       |
      | Multiple choice type           | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values         | option g\noption h\noption i                           |
    And I add a "Multiple choice (rated)" question to the feedback with:
      | Question               | this is a multiple choice rated |
      | Label                  | multichoice4                    |
      | Multiple choice type   | Multiple choice - single answer |
      | Multiple choice values | 0/option k\n1/option l\n5/option m |
    And I add a "Numeric answer" question to the feedback with:
      | Question               | this is a numeric answer |
      | Label                  | numeric                  |
      | Range to               | 100                      |
    And I add a "Short text answer" question to the feedback with:
      | Question               | this is a short text answer |
      | Label                  | shorttext                   |
      | Maximum characters accepted | 200                    |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a longer text answer | my long answer |
      | option b                     | 1              |
      | option d                     | 1              |
      | option f                     | 1              |
      | this is a multiple choice 3  | option h       |
      | option l                     | 1              |
      | this is a numeric answer (0 - 100) | 35       |
      | this is a short text answer  | hello          |
    And I press "Submit your answers"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a longer text answer | lots of feedbacks |
      | option a                     | 1              |
      | option d                     | 1              |
      | option e                     | 1              |
      | this is a multiple choice 3  | option i       |
      | option m                     | 1              |
      | this is a numeric answer (0 - 100) | 71       |
      | this is a short text answer  | no way         |
    And I press "Submit your answers"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Analysis"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 8"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Analysis"
    And I should see "C1" in the "(info)" "table"
    And I should see "my long answer" in the "(longertext)" "table"
    And I should see "lots of feedbacks" in the "(longertext)" "table"
    #And I should see "1 (50.00 %)" in the "option a:" "table_row"  // TODO: MDL-46891
    #And I should see "1 (50.00 %)" in the "option b:" "table_row"  // TODO: MDL-46891
    And I should see "2 (100.00 %)" in the "option d:" "table_row"
    And I should see "1 (50.00 %)" in the "option e:" "table_row"
    And I should see "1 (50.00 %)" in the "option f:" "table_row"
    And I should see "0" in the "option g:" "table_row"
    And I should not see "%" in the "option g:" "table_row"
    And I should see "1 (50.00 %)" in the "option h:" "table_row"
    And I should see "1 (50.00 %)" in the "option i:" "table_row"
    And I should see "0" in the "option k (0):" "table_row"
    And I should not see "%" in the "option k (0):" "table_row"
    And I should see "1 (50.00 %)" in the "option l (1):" "table_row"
    And I should see "1 (50.00 %)" in the "option m (5):" "table_row"
    And I should see "Average: 3.00" in the "(multichoice4)" "table"
    And I should see "35.00" in the "(numeric)" "table"
    And I should see "71.00" in the "(numeric)" "table"
    And I should see "Average: 53.00" in the "(numeric)" "table"
    And I should see "no way" in the "(shorttext)" "table"
    And I should see "hello" in the "(shorttext)" "table"
    And I log out

  Scenario: Create different types of questions in feedback with javascript disabled

  @javascript
  Scenario: Create different types of questions in feedback with javascript enabled
