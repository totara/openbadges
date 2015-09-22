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
 * Atto equation plugin upgrade script.
 *
 * @package    atto_equation
 * @copyright  2015 Sam Chaffee <sam@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Run all Atto equation upgrade steps between the current DB version and the current version on disk.
 * @param int $oldversion The old version of atto equation in the DB.
 * @return bool
 */
function xmldb_atto_equation_upgrade($oldversion) {
    require_once(__DIR__ . '/upgradelib.php');

    if ($oldversion < 2015083100) {
        atto_equation_update_librarygroup4_setting();

        // Atto equation savepoint reached.
        upgrade_plugin_savepoint(true, 2015083100, 'atto', 'equation');
    }

    return true;
}