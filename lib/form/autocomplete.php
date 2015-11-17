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
 * autocomplete type form element
 *
 * Contains HTML class for a autocomplete type element
 *
 * @package   core_form
 * @copyright 2015 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once($CFG->libdir . '/form/select.php');

/**
 * Autocomplete as you type form element
 *
 * HTML class for a autocomplete type element
 *
 * @package   core_form
 * @copyright 2015 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_autocomplete extends MoodleQuickForm_select {

    /** @var boolean $tags Should we allow typing new entries to the field? */
    protected $tags = false;
    /** @var string $ajax Name of an AMD module to send/process ajax requests. */
    protected $ajax = '';
    /** @var string $placeholder Placeholder text for an empty list. */
    protected $placeholder = '';
    /** @var bool $casesensitive Whether the search has to be case-sensitive. */
    protected $casesensitive = false;
    /** @var bool $showsuggestions Show suggestions by default - but this can be turned off. */
    protected $showsuggestions = true;

    /**
     * constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $options Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array. Special options
     *                          "tags", "placeholder", "ajax", "multiple", "casesensitive" are supported.
     */
    function MoodleQuickForm_autocomplete($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        // Even if the constructor gets called twice we do not really want 2x options (crazy forms!).
        $this->_options = array();
        if ($attributes === null) {
            $attributes = array();
        }
        if (isset($attributes['tags'])) {
            $this->tags = $attributes['tags'];
            unset($attributes['tags']);
        }
        if (isset($attributes['showsuggestions'])) {
            $this->showsuggestions = $attributes['showsuggestions'];
            unset($attributes['showsuggestions']);
        }
        $this->placeholder = get_string('search');
        if (isset($attributes['placeholder'])) {
            $this->placeholder = $attributes['placeholder'];
            unset($attributes['placeholder']);
        }
        if (isset($attributes['ajax'])) {
            $this->ajax = $attributes['ajax'];
            unset($attributes['ajax']);
        }
        if (isset($attributes['casesensitive'])) {
            $this->casesensitive = $attributes['casesensitive'] ? true : false;
            unset($attributes['casesensitive']);
        }
        parent::HTML_QuickForm_select($elementName, $elementLabel, $options, $attributes);

        $this->_type = 'autocomplete';
    }

    /**
     * Returns HTML for select form element.
     *
     * @return string
     */
    function toHtml(){
        global $PAGE;

        // Enhance the select with javascript.
        $this->_generateId();
        $id = $this->getAttribute('id');
        $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', $params = array('#' . $id, $this->tags, $this->ajax,
            $this->placeholder, $this->casesensitive, $this->showsuggestions));

        return parent::toHTML();
    }

    /**
     * Search the current list of options to see if there are any options with this value.
     * @param  string $value to search
     * @return boolean
     */
    function optionExists($value) {
        foreach ($this->_options as $option) {
            if (isset($option['attr']['value']) && ($option['attr']['value'] == $value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set the value of this element. If values can be added or are unknown, we will
     * make sure they exist in the options array.
     * @param  mixed string|array $value The value to set.
     * @return boolean
     */
    function setValue($value) {
        $values = (array) $value;
        foreach ($values as $onevalue) {
            if (($this->tags || $this->ajax) &&
                    (!$this->optionExists($onevalue)) &&
                    ($onevalue !== '_qf__force_multiselect_submission')) {
                $this->addOption($onevalue, $onevalue);
            }
        }
        return parent::setValue($value);
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param  array   array of submitted values to search
     * @param  bool    whether to return the value as associative array
     * @access public
     * @return mixed
     */
    function exportValue(&$submitValues, $assoc = false) {
        if ($this->ajax || $this->tags) {
            // When this was an ajax request, we do not know the allowed list of values.
            $value = $this->_findValue($submitValues);
            if (null === $value) {
                $value = $this->getValue();
            }
            // Quickforms inserts a duplicate element in the form with
            // this value so that a value is always submitted for this form element.
            // Normally this is cleaned as a side effect of it not being a valid option,
            // but in this case we need to detect and skip it manually.
            if ($value === '_qf__force_multiselect_submission' || $value === null) {
                $value = '';
            }
            return $this->_prepareValue($value, $assoc);
        } else {
            return parent::exportValue($submitValues, $assoc);
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $caller->setType($arg[0], PARAM_TAGLIST);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }
}
