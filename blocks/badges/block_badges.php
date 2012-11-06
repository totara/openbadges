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
 * Block for displaying earned local badges to users
 *
 * @package    block_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/badgeslib.php");

/**
 * Displays recent badges
 */
class block_badges extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_badges');
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return false;
    }

    function instance_allow_config() {
        return true;
    }

    function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_badges');
        } else {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new stdClass();
        }

        if (empty($this->config->numberofbadges)) {
            $limit = 10;
        } else if ($this->config->numberofbadges == 0) {
            $limit = null;
        } else {
            $limit = $this->config->numberofbadges;
        }

        // Create empty content
        $this->content = new stdClass();
        $this->content->text = '';

        if ($badges = get_user_badges($USER->id, $limit)) {
            foreach ($badges as $badge) {
                $this->content->text = '';
            }
        } else {
            $this->content->text = get_string('nothingtodisplay', 'block_badges');
        }

        return $this->content;
    }
}