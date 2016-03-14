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
 * Course selector field.
 *
 * Allows auto-complete ajax searching for courses and can restrict by enrolment, permissions, viewhidden...
 *
 * @package   core_form
 * @copyright 2015 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/autocomplete.php');

/**
 * Form field type for choosing a course.
 *
 * Allows auto-complete ajax searching for courses and can restrict by enrolment, permissions, viewhidden...
 *
 * @package   core_form
 * @copyright 2015 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_course extends MoodleQuickForm_autocomplete {

    /**
     * @var array $exclude Exclude a list of courses from the list (e.g. the current course).
     */
    protected $exclude = array();

    /**
     * @var boolean $allowmultiple Allow selecting more than one course.
     */
    protected $multiple = false;

    /**
     * @var array $requiredcapabilities Array of extra capabilities to check at the course context.
     */
    protected $requiredcapabilities = array();

    /**
     * Constructor
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     *                       Valid options are:
     *                       'multiple' - boolean multi select
     *                       'exclude' - array or int, list of course ids to never show
     *                       'requiredcapabilities' - array of capabilities. Uses ANY to combine them.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array()) {
        if (isset($options['multiple'])) {
            $this->multiple = $options['multiple'];
        }
        if (isset($options['exclude'])) {
            $this->exclude = $options['exclude'];
            if (!is_array($this->exclude)) {
                $this->exclude = array($this->exclude);
            }
        }
        if (isset($options['requiredcapabilities'])) {
            $this->requiredcapabilities = $options['requiredcapabilities'];
        }

        $validattributes = array(
            'ajax' => 'core/form-course-selector',
            'data-requiredcapabilities' => implode(',', $this->requiredcapabilities),
            'data-exclude' => implode(',', $this->exclude)
        );
        if ($this->multiple) {
            $validattributes['multiple'] = 'multiple';
        }

        parent::__construct($elementname, $elementlabel, array(), $validattributes);
    }

    /**
     * Set the value of this element. If values can be added or are unknown, we will
     * make sure they exist in the options array.
     * @param string|array $value The value to set.
     * @return boolean
     */
    public function setValue($value) {
        global $DB;
        $values = (array) $value;
        $coursestofetch = array();

        foreach ($values as $onevalue) {
            if ((!$this->optionExists($onevalue)) &&
                    ($onevalue !== '_qf__force_multiselect_submission')) {
                array_push($coursestofetch, $onevalue);
            }
        }

        if (empty($coursestofetch)) {
            return $this->setSelected(array());
        }

        // There is no API function to load a list of course from a list of ids.
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        $fields = array('c.id', 'c.category', 'c.sortorder',
                        'c.shortname', 'c.fullname', 'c.idnumber',
                        'c.startdate', 'c.visible', 'c.cacherev');
        list($whereclause, $params) = $DB->get_in_or_equal($coursestofetch, SQL_PARAMS_NAMED, 'id');

        $sql = "SELECT ". join(',', $fields). ", $ctxselect
                FROM {course} c
                JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                WHERE c.id ". $whereclause." ORDER BY c.sortorder";
        $list = $DB->get_records_sql($sql, array('contextcourse' => CONTEXT_COURSE) + $params);

        $coursestoselect = array();
        foreach ($list as $course) {
            context_helper::preload_from_record($course);
            // Make sure we can see the course.
            if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                continue;
            }
            $label = get_course_display_name_for_list($course);
            $this->addOption($label, $course->id);
            array_push($coursestoselect, $course->id);
        }
        return $this->setSelected($coursestoselect);
    }
}
