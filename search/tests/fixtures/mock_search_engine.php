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

namespace mock_search;

/**
 * Search engine for testing purposes.
 *
 * @package   core_search
 * @category  phpunit
 * @copyright David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class engine extends \core_search\engine {

    public function is_installed() {
        return true;
    }

    public function is_server_ready() {
        return true;
    }

    public function add_document($doc) {
        // No need to implement.
    }

    public function execute_query($data, $usercontexts) {
        // No need to implement.
    }

    public function delete($areaid = null) {
        return null;
    }

    public function get_course($courseid) {
        return parent::get_course($courseid);
    }

    public function get_search_area($areaid) {
        return parent::get_search_area($areaid);
    }
}
