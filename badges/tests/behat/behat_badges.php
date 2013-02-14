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
 * DESCRIPTION
 *
 * @package    core
 * @subpackage badges
 * @category   test
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Element\NodeElement as NodeElement;

/**
 * Badges-related steps definitions.
 *
 */
class behat_badges extends behat_base {
    /**
     * Attaches image file to a new badge form page.
     *
     * @Given /^I add a badge image to "(?P<field_string>(?:[^"]|\\")*)" field$/
     * @param string $formfield
     */
    public function i_add_badge_image_to($formfield) {
        global $CFG;

        $formfield = $this->fixStepArgument($formfield);
        $filefield = $this->getSession()->getPage()->findField($formfield);
        $filefield->attachFile($CFG->dirroot . '/badges/tests/behat/badge.png');
    }
}