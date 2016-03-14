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
 * Strings for component 'repository_boxnet', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   repository_boxnet
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['apikey'] = 'API key';
$string['boxnet:view'] = 'View Box repository';
$string['cannotcreatereference'] = 'Cannot create a reference, not enough permissions to share the file on Box.';
$string['clientid'] = 'Client ID';
$string['clientsecret'] = 'Client secret';
$string['configplugin'] = 'Box configuration';
$string['filesourceinfo'] = 'Box ({$a->fullname}): {$a->filename}';
$string['information'] = 'Get a client ID and secret from the <a href="https://app.box.com/developers/services">Box developer page</a> for your Moodle site.';
$string['invalidpassword'] = 'Invalid password';
$string['migrationadvised'] = 'It appears that you were using Box with the API version 1, have you run the <a href="{$a}">migration tool</a> to convert the old references?';
$string['migrationinfo'] = '<p>As part of the migration to the new API provided by Box, your file references have to be migrated. Unfortunately the reference system is not compatible with the API v2, so we are going to download them and convert them to real files.</p>
<p>Please also be aware that the migration can <strong>take a very long time</strong>, depending on how many references are used, and how large their files are.</p>
<p>You can run the migration tool by clicking the button below, or alternatively by executing the CLI script: repository/boxnet/cli/migrationv1.php.</p>
<p>Find out more <a href="{$a->docsurl}">here</a>.</p>';
$string['migrationtool'] = 'Box APIv1 migration tool';
$string['nullfilelist'] = 'There are no files in this repository';
$string['password'] = 'Password';
$string['pluginname_help'] = 'Repository on Box';
$string['pluginname'] = 'Box';
$string['runthemigrationnow'] = 'Run the migration tool now';
$string['saved'] = 'Box data saved';
$string['shareurl'] = 'Share URL';
$string['username'] = 'Username for Box';
$string['warninghttps'] = 'Box requires your website to be using HTTPS in order for the repository to work.';
