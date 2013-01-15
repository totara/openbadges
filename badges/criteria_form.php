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
 * Form classes for editing badges criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Form to edit badge criteria.
 *
 */
class edit_criteria_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $criteria = $this->_customdata['criteria'];
        $add = $this->_customdata['add'];
        $edit = $this->_customdata['edit'];
        $addcourse = $this->_customdata['addcourse'];

        // Get course selector first if it's a new courseset criteria.
        if (($criteria->id == 0 || $addcourse) && $criteria->criteriatype == BADGE_CRITERIA_TYPE_COURSESET) {
            $criteria->get_courses($mform);
        } else {
            list($none, $message) = $criteria->get_options($mform);

            if ($none) {
                $mform->addElement('html', html_writer::tag('div', $message));
                $mform->addElement('submit', 'back', get_string('back'));
            } else {
                $buttonarray = array();
                if ($criteria->criteriatype == BADGE_CRITERIA_TYPE_COURSESET) {
                    $buttonarray[] =& $mform->createElement('submit', 'addcourse', get_string('addcourse', 'badges'));
                }
                $str = $edit ? get_string('updatec', 'badges') : get_string('addc', 'badges');
                $buttonarray[] =& $mform->createElement('submit', 'submitbutton', $str);
                $buttonarray[] =& $mform->createElement('submit', 'back', get_string('cancel'));

                $mform->closeHeaderBefore('buttonar');
                $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            }
        }
    }
}