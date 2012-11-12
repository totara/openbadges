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
 * Language file for 'badges' component
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

// Admin settings.
$string['adminonly'] = 'This page is restricted to Site Administrators only';
$string['coursebadges'] = 'Badges';
$string['badgesettings'] = 'General settings';
$string['managebadges'] = 'Manage badges';
$string['mybadges'] = 'My badges';
$string['sitebadges'] = 'Site badges';

// Global settings.
$string['defaultissuername'] = 'Default badge issuer name';
$string['defaultissuername_desc'] = 'Name of the issuing agent or authority.';
$string['defaultissuerurl'] = 'Default badge issuer URL';
$string['defaultissuerurl_desc'] = 'Origin of the issuer. This setting should be in form [protocol]://[host]:[port]';
$string['defaultissuercontact'] = 'Default badge issuer contact details';
$string['defaultissuercontact_desc'] = 'An email address associated with the badge issuer.';
$string['defaultbadgesalt'] = 'Default salt for constructing hash of the recepient\'s email address';
$string['defaultbadgesalt_desc'] = 'Using hash allows backpack services to confirm the badge earner without having to expose their email address. This setting can consist of numbers and letters only.';
$string['allowexternalbackpack'] = 'Enable external backpacks connections';
$string['allowexternalbackpack_desc'] = 'Allow users to set up connections and display badges from their external backpack providers.';
$string['allowcoursebadges'] = 'Enable course badges';
$string['allowcoursebadges_desc'] = 'Allow badges to be created and awarded in course context.';

// MyBadges page strings.
$string['localbadges'] = 'Local Badges';
$string['localbadges_help'] = 'Badges earned within this web site.';
$string['externalbadges'] = 'External Backpack';
$string['externalbadges_help'] = 'Connection setting and badges displayed from an external backpack provider';
$string['visible'] = 'Visible';
$string['hidden'] = 'Hidden';
$string['expired'] = 'Expired';

// Badge details.
$string['badgedetails'] = 'Badge details';
$string['issuerdetails'] = 'Issuer details';
$string['issuancedetails'] = 'Issuance details';
$string['expiredate'] = 'This badge expires on {$a}.';
$string['expireperiod'] = 'This badge expires {$a} day(s) after being earned.';
$string[''] = '';
$string[''] = '';

// Badge editing/creating strings.
$string['awards'] = 'Awards';
$string['bawards'] = 'Awards ({$a})';
$string['bcriteria'] = 'Criteria';
$string['bdetails'] = 'Edit details';
$string['bmessage'] = 'Message';
$string['boverview'] = 'Overview';
$string['create'] = 'New badge';
$string['newbadge'] = 'Add a new badge';
$string['noawards'] = 'This badge has not been earned yet.';
$string['nocriteria'] = 'Criteria for this badge have not been set up yet.';
$string['numawards'] = 'This badge has been earned by {$a} user(s).';
$string['noexpiry'] = 'This badge does not have an expiry date.';

// Error messages.
$string['error:setter'] = 'Field {$a->field} was not found in class {$s->class} ';
$string['error:save'] = 'Cannot save the badge.';
$string['error:nosuchbadge'] = 'Badge with id {$a} does not exist.';
$string['error:invalidcriteriatype'] = 'Invalid criteria type.';
