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
 * Wiki external functions and service definitions.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

$functions = array(

    'mod_wiki_get_wikis_by_courses' => array(
        'classname'     => 'mod_wiki_external',
        'methodname'    => 'get_wikis_by_courses',
        'description'   => 'Returns a list of wiki instances in a provided set of courses, if ' .
                           'no courses are provided then all the wiki instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/wiki:viewpage',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_wiki_view_wiki' => array(
        'classname'     => 'mod_wiki_external',
        'methodname'    => 'view_wiki',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/wiki:viewpage',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_wiki_view_page' => array(
        'classname'     => 'mod_wiki_external',
        'methodname'    => 'view_page',
        'description'   => 'Trigger the page viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/wiki:viewpage',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_wiki_get_subwikis' => array(
        'classname'     => 'mod_wiki_external',
        'methodname'    => 'get_subwikis',
        'description'   => 'Returns the list of subwikis the user can see in a specific wiki.',
        'type'          => 'read',
        'capabilities'  => 'mod/wiki:viewpage',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    )
);
