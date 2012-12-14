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
$string['defaultissuerurl_desc'] = 'Origin of the issuer. This setting should be in form &lt;protocol>://&lt;host>:&lt;port>';
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
$string['hiddenbadge'] = 'Unfortunately, badge owner has not made this information available.';
$string['issuedbadge'] = 'Issued badge information';
$string['visible'] = 'Visible';
$string['hidden'] = 'Hidden';
$string['expired'] = 'Expired';
$string['viewbadge'] = 'View issued badge';

// Badge status.
$string['currentstatus'] = 'Current status: ';
$string['badgestatus_0'] = 'Inactive';
$string['badgestatus_1'] = 'Active';
$string['badgestatus_2'] = 'Inactive and locked';
$string['badgestatus_3'] = 'Active and locked';
$string['badgestatus_4'] = 'Archived';

// Badge criteria.
$string['criteria_0'] = 'Overall criteria aggregation';
$string['criteria_1'] = 'Activity completion';
$string['criteria_2'] = 'Manual badge issue';
$string['criteria_3'] = 'Social pariticipation';
$string['criteria_4'] = 'Course completion';
$string['criteria_5'] = 'Courseset completion';
$string['criteria_6'] = 'Profile completion';
$string['criteriasummary'] = 'Criteria summary';
$string['completionnotenabled'] = 'Course completion is not enabled for this course, so it cannot be included in badge criteria. <br/> You can enable course completion in the course settings.';
$string['coursecompletion'] = 'Learners must complete this course. ';
$string['lockedbadge'] = 'Currently, this badge is either active or locked, so it cannot be modified. If you would like to change this badge\'s details or criteria, please set its status to inactive.';
$string['mingrade'] = 'Minimum grade required';
$string['noparamstoadd'] = 'There are no additional parameters available to add to this badge requirement.';
$string['nothingtoadd'] = 'There are no available criteria to add.';

// Badge criteria description.
$string['criteria_descr_0'] = '<p>To earn this badge, learners have to complete <b>{$a}</b> of the following requirements:</p>';
$string['criteria_descr_1'] = '<p><b>{$a}</b> of the following activities have to be completed:</p>';
$string['criteria_descr_4'] = 'Learners have to complete this course ';
$string['criteria_descr_2'] = '<p>This badge has to be awarded by the users with <b>{$a}</b> of the following roles:</p>';
$string['criteria_descr_5'] = '<p><b>{$a}</b> of the following courses have to be completed:</p>';
$string['criteria_descr_grade'] = ' with minimum grade of <i>{$a}</i> ';
$string['criteria_descr_bydate'] = ' by <i>{$a}</i> ';

// Badge details.
$string['badgedetails'] = 'Badge details';
$string['issuerdetails'] = 'Issuer details';
$string['issuancedetails'] = 'Issuance details';
$string['expiredate'] = 'This badge expires on {$a}.';
$string['expireperiod'] = 'This badge expires {$a} day(s) after being issued.';

// Badge editing/creating strings.
$string['actions'] = 'Actions';
$string['activate'] = 'Activate';
$string['activitiescriteria'] = 'Activity completion criteria';
$string['addcriteria'] = 'Add new criteria';
$string['additionalparameters'] = 'Additional parameters';
$string['after'] = 'after the date of issue.';
$string['aggregationmethod'] = 'Aggregation method';
$string['all'] = 'All';
$string['any'] = 'Any';
$string['awards'] = 'Awards';
$string['award'] = 'Award badge';
$string['attachment'] = 'Attachment';
$string['bawards'] = 'Awards ({$a})';
$string['badgeimage'] = 'Image';
$string['badgeurl'] = 'Issued badge link';
$string['badgestoearn'] = 'Number of badges available: {$a}';
$string['bcriteria'] = 'Criteria';
$string['bdetails'] = 'Edit details';
$string['bmessage'] = 'Message';
$string['bydate'] = ' complete by';
$string['configuremessage'] = 'Configure badge message';
$string['copyof'] = 'Copy of ';
$string['boverview'] = 'Overview';
$string['clear'] = 'Clear all criteria';
$string['clearbadge'] = 'Remove all badge criteria';
$string['clearconfirm'] = '<p>Are you sure you would like to remove all existing criteria for this badge?</p>
<p><b>Warning!</b></p><p>If some users have already made progress towards earning this badge, their progress will be removed as well.</p>';
$string['contact'] = 'Contact';
$string['create'] = 'New badge';
$string['createbutton'] = 'Create badge';
$string['currentimage'] = 'Current image';
$string['dateawarded'] = 'Date issued';
$string['dateearned'] = 'Earned on {$a}';
$string['day'] = 'Day(s)';
$string['deactivate'] = 'Deactivate';
$string['delbadge'] = 'Delete badge';
$string['delcritconfirm'] = 'Are you sure that you want to delete this criterion?';
$string['delparamconfirm'] = 'Are you sure that you want to delete this parameter?';
$string['delconfirm'] = 'Are you sure that you want to delete badge \'{$a}\'?';
$string['description'] = 'Description';
$string['expirydate'] = 'Expiry date';
$string['fixed'] = 'Fixed date';
$string['issuername'] = 'Issuer name';
$string['issuerurl'] = 'Issuer URL';
$string['makevisible'] = 'Make visible';
$string['message'] = 'Message body';
$string['month'] = 'Month(s)';
$string['never'] = 'Never (default)';
$string['newbadge'] = 'Add a new badge';
$string['newimage'] = 'New image';
$string['noawards'] = 'This badge has not been earned yet.';
$string['nobadges'] = 'There are no badges available.';
$string['nocriteria'] = 'Criteria for this badge have not been set up yet.';
$string['numawards'] = 'This badge has been earned by {$a} user(s).';
$string['noexpiry'] = 'This badge does not have an expiry date.';
$string['overallcriteriaaggregation'] = 'Overall badge criteria aggregation';
$string['perpage'] = 'Records per page';
$string['proceed'] = 'Proceed';
$string['relative'] = 'Relative date';
$string['reviewbadge'] = 'Review badge';
$string['reviewconfirm'] = '<p>When you activate a badge, it means that now it can be earned by users.</p>
<p>Would you like to check if any of the users have already completed all the requirements for \'{$a}\' badge? </p>
<p><b>Warning!</b></p><p>Depending on how many criteria are assigned to this badge and a number of users on your site, selecting to run this check may make your site slower while all badge requirements are calculated. </p>';
$string['subject'] = 'Message subject';
$string['selecting'] = 'With selected badges...';
$string['status'] = 'Badge status';
$string['visible'] = 'Visible';
$string['year'] = 'Year(s)';

// Default badge message.
$string['messagebody'] = 'Based on the following achievement(s):

%criterialist%

you have been awarded a badge %badgename%!

More infromation about this badge can be found at %badgelink%.

If there is no badge attached to this email, you can manage and download it from {$a} page.';

$string['messagesubject'] = 'Congratulations! You just earned a badge!';

// Help text for elements.
$string['actions_help'] = '';
$string['attachment_help'] = 'If this \'Attachment\' is checked, an issued badge will be attached to the recepient\'s email for download';
$string['contact_help'] = 'An email address associated with the badge issuer.';
$string['criteria_0_help'] = "Overall help";
$string['criteria_1_help'] = "Help";
$string['criteria_2_help'] = "Role help";
$string['criteria_3_help'] = "Social help";
$string['criteria_4_help'] = "Course help";
$string['criteria_5_help'] = "Courseset Help";
$string['criteria_6_help'] = "Profile help";
$string['expirydate_help'] = 'Visible';
$string['badgeimage_help'] = 'This is an image that will be used when this badge is issued.

To add a new image, browse and select an image (in JPG or PNG format) then click "Save changes". The image will be cropped to a square and resized to match badge image requirements. ';

$string['issuerurl_help'] = 'Origin of the issuer.

This setting should be in form &lt;protocol>://&lt;host>:&lt;port>.';

$string['issuername_help'] = 'Name of the issuing agent or authority.';
$string['notification'] = 'Notify badge creator';
$string['notification_help'] = 'If the \'Notify badge creator\' box is checked, the badge creator will also be sent a notification about the badge issue.';
$string['status_help'] = 'Badge status explanation'; //@TODO
$string['variablesubstitution'] = 'Variable substitution in messages.';
$string['variablesubstitution_help'] = 'In a badge message, certain variables can be inserted into the subject and/or body of a message so that they will be replaced with real values when the message is sent. The variables should be inserted into the the text exactly as they are shown below. The following variables can be used:

%badgename%
:   This will be replaced by the badge\'s full name.

%username%
:   This will be replaced by the recipient\'s full name.

%badgelink%
:   This will be replaced by the public URL with information about the issued badge.

%criterialist%
:   This will be replaced by the list of completed criteria for issued badge.';
$string['visible_help'] = 'This setting indicates whether badge is visible to its potential earners.

If this checkbox is checked and the badge is active, users will be able to see the badge in the list of site or course badges.';

// Error messages.
$string['error:clone'] = 'Cannot clone the badge.';
$string['error:save'] = 'Cannot save the badge.';
$string['error:missingcourse'] = 'It looks like this course does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:missingmodule'] = 'It looks like this activity does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:missingrole'] = 'It looks like this role does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:noactivities'] = 'No activities with completion criteria enabled.';
$string['error:nosuchcourse'] = '!!!Course error. Please revise your criteria!!!';
$string['error:nosuchmod'] = '!!!Activity error. Please revise your criteria!!!';
$string['error:nosuchrole'] = '!!!Role error. Please revise your criteria!!!';
$string['error:nosuchbadge'] = 'Badge with id {$a} does not exist.';
$string['error:invalidcriteriatype'] = 'Invalid criteria type.';
$string['error:invalidbadgeurl'] = 'Invalid badge issuer URL format. If it is possible, try using {$a}';
$string['error:invalidexpireperiod'] = 'Expiry period cannot be negative or equal 0.';
$string['error:invalidexpiredate'] = 'Expiry date has to be in the future.';
