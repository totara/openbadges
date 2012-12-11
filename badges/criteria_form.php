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
    function definition() {
        $mform = $this->_form;
        $badge = $this->_customdata['badge'];

        if ($badge->has_criteria()) {
            ksort($badge->criteria);

            foreach ($badge->criteria as $crit) {
                $crit->config_form_criteria($mform, $badge);
            }

            $this->add_action_buttons(false, get_string('update'));
        } else {
            $mform->addElement('html', '<div>'. get_string('nocriteria', 'badges') . '</div>');
        }

        // Add hidden fields.
        $mform->addElement('hidden', 'id', $badge->id);
        $mform->setType('id', PARAM_INT);

        // Freeze all elements if badge is active.
        if ($badge->is_active() || $badge->is_locked()) {
            $mform->hardFreeze();
        }
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}