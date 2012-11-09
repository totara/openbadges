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
 * Form classes for editing badges
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
 * Form to edit badge details.
 *
 */
class edit_details_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        $badge = (isset($this->_customdata['badge'])) ? $this->_customdata['badge'] : false;
        $action = $this->_customdata['action'];

        $mform->addElement('header', 'badgedetails', get_string('badgedetails', 'badges'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '70'));
        // Using PARAM_FILE to avoid problems later when downloading badge files.
        $mform->setType('name', PARAM_FILE);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'badges'), 'wrap="virtual" rows="8" cols="70"');
        $mform->setType('description', PARAM_CLEANHTML);
        $mform->addRule('description', null, 'required');

        $str = $action == 'new' ? get_string('badgeimage', 'badges') : get_string('newimage', 'badges');
        $imageoptions = array('maxbytes' => 262144, 'accepted_types' => array('web_image'));
        $mform->addElement('filepicker', 'image', $str, null, $imageoptions);

        if ($action == 'new') {
            $mform->addRule('image', null, 'required');
        } else {
            $currentimage = $mform->createElement('static', 'currentimage', get_string('currentimage', 'badges'));
            $mform->insertElementBefore($currentimage, 'image');
        }
        $mform->addHelpButton('image', 'badgeimage', 'badges');

        $mform->addElement('header', 'issuerdetails', get_string('issuerdetails', 'badges'));

        $mform->addElement('text', 'issuername', get_string('name'), array('size' => '70'));
        $mform->setType('issuername', PARAM_NOTAGS);
        $mform->addRule('issuername', null, 'required');
        if (isset($CFG->badges_defaultissuername)) {
            $mform->setDefault('issuername', $CFG->badges_defaultissuername);
        }
        $mform->addHelpButton('issuername', 'issuername', 'badges');

        $mform->addElement('text', 'issuercontact', get_string('contact', 'badges'), array('size' => '70'));
        if (isset($CFG->badges_defaultissuercontact)) {
            $mform->setDefault('issuercontact', $CFG->badges_defaultissuercontact);
        }
        $mform->setType('issuercontact', PARAM_RAW);
        $mform->addHelpButton('issuercontact', 'contact', 'badges');

        $mform->addElement('header', 'issuancedetails', get_string('issuancedetails', 'badges'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}

/**
 * Form to edit badge details.
 *
 */
class edit_criteria_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['issuercontact']) && !validate_email($data['issuercontact'])) {
            $errors['issuercontact'] = get_string('invalidemail');
        }

        if ($data['expiry'] == 2 && $data['expireperiod'] <= 0) {
            $errors['expirydategr'] = get_string('error:invalidexpireperiod', 'badges');
        }

        if ($data['expiry'] == 1 && $data['expiredate'] <= time()) {
            $errors['expirydategr'] = get_string('error:invalidexpiredate', 'badges');
        }

        // Check for duplicate badge names.
        if ($data['action'] == 'new') {
            $duplicate = $DB->record_exists_select('badge', 'name = :name AND status != :deleted',
                array('name' => $data['name'], 'deleted' => BADGE_STATUS_ARCHIVED));
        } else {
            $duplicate = $DB->record_exists_select('badge', 'name = :name AND id != :badgeid AND status != :deleted',
                array('name' => $data['name'], 'badgeid' => $data['id'], 'deleted' => BADGE_STATUS_ARCHIVED));
        }

        if ($duplicate) {
            $errors['name'] = get_string('error:duplicatename', 'badges');
        }

        return $errors;
    }
}

/**
 * Form to edit badge details.
 *
 */
class edit_message_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
