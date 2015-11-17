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
 * Behat grade related steps definitions.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode;

class behat_grade extends behat_base {

    /**
     * Enters a grade via the gradebook for a specific grade item and user when viewing the 'Grader report' with editing mode turned on.
     *
     * @Given /^I give the grade "(?P<grade_number>(?:[^"]|\\")*)" to the user "(?P<username_string>(?:[^"]|\\")*)" for the grade item "(?P<grade_activity_string>(?:[^"]|\\")*)"$/
     * @param int $grade
     * @param string $userfullname the user's fullname as returned by fullname()
     * @param string $itemname
     * @return Given
     */
    public function i_give_the_grade($grade, $userfullname, $itemname) {
        $gradelabel = $userfullname . ' ' . $itemname;
        $fieldstr = get_string('useractivitygrade', 'gradereport_grader', $gradelabel);

        return new Given('I set the field "' . $this->escape($fieldstr) . '" to "' . $grade . '"');
    }

    /**
     * Enters a quick feedback via the gradebook for a specific grade item and user when viewing
     * the 'Grader report' with editing mode turned on.
     *
     * @Given /^I give the feedback "(?P<grade_number>(?:[^"]|\\")*)" to the user "(?P<username_string>(?:[^"]|\\")*)" for the grade item "(?P<grade_activity_string>(?:[^"]|\\")*)"$/
     * @param string $feedback
     * @param string $userfullname the user's fullname as returned by fullname()
     * @param string $itemname
     * @return Given
     */
    public function i_give_the_feedback($feedback, $userfullname, $itemname) {
        $gradelabel = $userfullname . ' ' . $itemname;
        $fieldstr = get_string('useractivityfeedback', 'gradereport_grader', $gradelabel);

        return new Given('I set the field "' . $this->escape($fieldstr) . '" to "' . $this->escape($feedback) . '"');
    }

    /**
     * Changes the settings of a grade item or category or the course.
     *
     * Teacher must be either on the grade setup page or on the Grader report page with editing mode turned on.
     *
     * @Given /^I set the following settings for grade item "(?P<grade_item_string>(?:[^"]|\\")*)":$/
     * @param string $gradeitem
     * @param TableNode $data
     * @return Given[]
     */
    public function i_set_the_following_settings_for_grade_item($gradeitem, TableNode $data) {

        $steps = array();
        $gradeitem = $this->getSession()->getSelectorsHandler()->xpathLiteral($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradeitem)]//*[contains(@class,'moodle-actionmenu')]//a[contains(@class,'toggle-display')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $steps[] = new Given('I click on "' . $this->escape($xpath) . '" "xpath_element"');
            }
        }

        $savechanges = get_string('savechanges', 'grades');
        $edit = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('edit') . '  ');
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";
        $steps[] = new Given('I click on "' . $this->escape($linkxpath) . '" "xpath_element"');
        $steps[] = new Given('I set the following fields to these values:', $data);
        $steps[] = new Given('I press "' . $this->escape($savechanges) . '"');
        return $steps;
    }

    /**
     * Sets a calculated manual grade item. Needs a table with item name - idnumber relation.
     * The step requires you to be in the 'Gradebook setup' page.
     *
     * @Given /^I set "(?P<calculation_string>(?:[^"]|\\")*)" calculation for grade item "(?P<grade_item_string>(?:[^"]|\\")*)" with idnumbers:$/
     * @param string $calculation The calculation.
     * @param string $gradeitem The grade item name.
     * @param TableNode $TableNode The grade item name - idnumbers relation.
     * @return Given[]
     */
    public function i_set_calculation_for_grade_item_with_idnumbers($calculation, $gradeitem, TableNode $data) {

        $steps = array();
        $gradeitem = $this->getSession()->getSelectorsHandler()->xpathLiteral($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradeitem)]//*[contains(@class,'moodle-actionmenu')]//a[contains(@class,'toggle-display')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $steps[] = new Given('I click on "' . $this->escape($xpath) . '" "xpath_element"');
            }
        }

        // Going to edit calculation.
        $savechanges = get_string('savechanges', 'grades');
        $edit = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('editcalculation', 'grades'));
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";
        $steps[] = new Given('I click on "' . $this->escape($linkxpath) . '" "xpath_element"');

        // After adding id numbers we should wait until the page is reloaded.
        if ($this->running_javascript()) {
            $steps[] = new Given('I wait until the page is ready');
        }

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath ="//input[@class='idnumber'][" .
                "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                " or " .
                "parent::li[@class='categoryitem' or @class='courseitem']/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
            "]";
            $steps[] = new Given('I set the field with xpath "' . $inputxpath . '" to "' . $idnumber . '"');
        }

        $steps[] = new Given('I press "' . get_string('addidnumbers', 'grades') . '"');

        // After adding id numbers we should wait until the page is reloaded.
        if ($this->running_javascript()) {
            $steps[] = new Given('I wait until the page is ready');
        }

        $steps[] = new Given('I set the field "' . get_string('calculation', 'grades') . '" to "' . $calculation . '"');
        $steps[] = new Given('I press "' . $savechanges . '"');

        return $steps;
    }

    /**
     * Sets a calculated manual grade category total. Needs a table with item name - idnumber relation.
     * The step requires you to be in the 'Gradebook setup' page.
     *
     * @Given /^I set "(?P<calculation_string>(?:[^"]|\\")*)" calculation for grade category "(?P<grade_item_string>(?:[^"]|\\")*)" with idnumbers:$/
     * @param string $calculation The calculation.
     * @param string $gradeitem The grade item name.
     * @param TableNode $data The grade item name - idnumbers relation.
     * @return Given[]
     */
    public function i_set_calculation_for_grade_category_with_idnumbers($calculation, $gradeitem, TableNode $data) {

        $steps = array();
        $gradecategorytotal = $this->getSession()->getSelectorsHandler()->xpathLiteral($gradeitem . ' total');
        $gradeitem = $this->getSession()->getSelectorsHandler()->xpathLiteral($gradeitem);

        if ($this->running_javascript()) {
            $xpath = "//tr[contains(.,$gradecategorytotal)]//*[contains(@class,'moodle-actionmenu')]" .
                "//a[contains(@class,'toggle-display')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $steps[] = new Given('I click on "' . $this->escape($xpath) . '" "xpath_element"');
            }
        }

        // Going to edit calculation.
        $savechanges = get_string('savechanges', 'grades');
        $edit = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('editcalculation', 'grades'));
        $linkxpath = "//a[./img[starts-with(@title,$edit) and contains(@title,$gradeitem)]]";
        $steps[] = new Given('I click on "' . $this->escape($linkxpath) . '" "xpath_element"');

        // After adding id numbers we should wait until the page is reloaded.
        $steps[] = new Given('I wait until the page is ready');

        // Mapping names to idnumbers.
        $datahash = $data->getRowsHash();
        foreach ($datahash as $gradeitem => $idnumber) {
            // This xpath looks for course, categories and items with the provided name.
            // Grrr, we can't equal in categoryitem and courseitem because there is a line jump...
            $inputxpath = "//input[@class='idnumber'][" .
                "parent::li[@class='item'][text()='" . $gradeitem . "']" .
                " | " .
                "parent::li[@class='categoryitem' | @class='courseitem']" .
                "/parent::ul/parent::li[starts-with(text(),'" . $gradeitem . "')]" .
            "]";
            $steps[] = new Given('I set the field with xpath "' . $inputxpath . '" to "' . $idnumber . '"');
        }

        $steps[] = new Given('I press "' . get_string('addidnumbers', 'grades') . '"');

        // After adding id numbers we should wait until the page is reloaded.
        $steps[] = new Given('I wait until the page is ready');

        $steps[] = new Given('I set the field "' . get_string('calculation', 'grades') . '" to "' . $calculation . '"');
        $steps[] = new Given('I press "' . $savechanges . '"');

        return $steps;
    }

    /**
     * Resets the weights for the grade category
     *
     * Teacher must be on the grade setup page.
     *
     * @Given /^I reset weights for grade category "(?P<grade_item_string>(?:[^"]|\\")*)"$/
     * @param $gradeitem
     * @return array
     */
    public function i_reset_weights_for_grade_category($gradeitem) {

        $steps = array();

        if ($this->running_javascript()) {
            $gradeitemliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($gradeitem);
            $xpath = "//tr[contains(.,$gradeitemliteral)]//*[contains(@class,'moodle-actionmenu')]//a[contains(@class,'toggle-display')]";
            if ($this->getSession()->getPage()->findAll('xpath', $xpath)) {
                $steps[] = new Given('I click on "' . $this->escape($xpath) . '" "xpath_element"');
            }
        }

        $linktext = get_string('resetweights', 'grades', (object)array('itemname' => $gradeitem));
        $steps[] = new Given('I click on "' . $this->escape($linktext) . '" "link"');
        return $steps;
    }

    /**
     * Step allowing to test before-the-fix behaviour of the gradebook
     *
     * @Given /^gradebook calculations for the course "(?P<coursename_string>(?:[^"]|\\")*)" are frozen at version "(?P<version_string>(?:[^"]|\\")*)"$/
     * @param string $coursename
     * @param string $version
     * @return Given
     */
    public function gradebook_calculations_for_the_course_are_frozen_at_version($coursename, $version) {
        global $DB;
        $courseid = $DB->get_field('course', 'id', array('shortname' => $coursename), MUST_EXIST);
        set_config('gradebook_calculations_freeze_' . $courseid, $version);
    }
}
