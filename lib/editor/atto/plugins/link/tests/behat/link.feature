@editor @editor_atto @atto @atto_link @_file_upload
Feature: Add links to Atto
  To write rich text - I need to add links.

  @javascript
  Scenario: Insert a links
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "Super cool"
    And I select the text in the "Description" Atto editor
    And I click on "Link" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I click on "Update profile" "button"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "Super cool</a>"

