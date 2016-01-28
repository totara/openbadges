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
 * LDAP authentication plugin upgrade code
 *
 * @package    auth_ldap
 * @copyright  2013 Iñaki Arenaza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_ldap_upgrade($oldversion) {

    // Moodle v2.5.0 release upgrade line
    // Put any upgrade step following this

    // MDL-39323 New setting in 2.5, make sure it's defined.
    if ($oldversion < 2013052100) {
        if (get_config('start_tls', 'auth/ldap') === false) {
            set_config('start_tls', 0, 'auth/ldap');
        }
        upgrade_plugin_savepoint(true, 2013052100, 'auth', 'ldap');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014111001) {
        global $DB;
        // From now on the default LDAP objectClass setting for AD has been changed, from 'user' to '(samaccounttype=805306368)'.
        if (is_enabled_auth('ldap')
                && ($DB->get_field('config_plugins', 'value', array('name' => 'user_type', 'plugin' => 'auth/ldap')) === 'ad')
                && ($DB->get_field('config_plugins', 'value', array('name' => 'objectclass', 'plugin' => 'auth/ldap')) === '')) {
            // Save the backwards-compatible default setting.
            set_config('objectclass', 'user', 'auth/ldap');
        }

        upgrade_plugin_savepoint(true, 2014111001, 'auth', 'ldap');
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
