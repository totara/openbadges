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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @author     Brian Barnes <brian.barnes@totaralms.com>
 *
 * @package    theme_base
 * @copyright  2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . "/badges/renderer.php");
class theme_base_core_badges_renderer extends core_badges_renderer {
    /**
     * Renders a label value pair
     *
     * @param string $label The label of the value
     * @param string $value The value to be rendered
     */
    protected function render_label_value($label, $value) {
        $output = html_writer::start_tag('div', array('class' => 'row-fluid'));
        $output .= html_writer::tag('div', $label, array('class' => 'span2'));
        $output .= html_writer::tag('div', $value, array('class' => 'span10'));
        $output .= html_writer::end_tag('div');
        return $output;
    }
}
