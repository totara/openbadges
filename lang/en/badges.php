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
$string['mybackpack'] = 'My backpack settings';
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
$string['backpackbadges'] = 'You have {$a->totalbadges} badge(s) displayed from your backpack at <a href="{$a->backpackurl}">{$a->backpackurl}</a>. <br/>';
$string['backpackdetails'] = 'Backpack settings';
$string['badgesearned'] = 'Number of badges earned: {$a}';
$string['clearsettings'] = 'Clear settings';
$string['editsettings'] = 'Edit settings';
$string['evidence'] = 'Evidence';
$string['expired'] = 'Expired';
$string['externalbadges'] = 'External Badges';
$string['externalbadges_help'] = 'Connection setting and badges displayed from an external backpack provider';
$string['hidden'] = 'Hidden';
$string['hiddenbadge'] = 'Unfortunately, badge owner has not made this information available.';
$string['issuedbadge'] = 'Issued badge information';
$string['localbadges'] = 'Local Badges';
$string['localbadges_help'] = 'Badges earned within this web site.';
$string['makepublic'] = 'Make public';
$string['nobackpack'] = 'There is no backpack service connected to this account. <br/>';
$string['nobackpackbadges'] = 'There are no badges displayed from your backpack at <a href="{$a->backpackurl}">{$a->backpackurl}</a>. <br/>';
$string['searchname'] = 'Search by name';
$string['selectgroup'] = 'Select badge group';
$string['visible'] = 'Visible';
$string['setup'] = 'Set up connection';
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
$string['default'] = 'Default user profile fields';
$string['lockedbadge'] = 'Currently, this badge is either active or locked, so most of it\'s properties cannot be modified. If you would like to change this badge\'s details or criteria, please set its status to inactive.';
$string['mingrade'] = 'Minimum grade required';
$string['noparamstoadd'] = 'There are no additional parameters available to add to this badge requirement.';
$string['nothingtoadd'] = 'There are no available criteria to add.';

// Badge criteria description.
$string['criteria_descr'] = '<p>To earn this badge, learners have to complete the following requirement: ';
$string['criteria_descr_0'] = '<p>To earn this badge, learners have to complete <b>{$a}</b> of the following requirements:</p>';
$string['criteria_descr_1'] = '<p><b>{$a}</b> of the following activities have to be completed:</p>';
$string['criteria_descr_single_1'] = '<p>The following activity has to be completed:</p>';
$string['criteria_descr_4'] = 'Learners must complete the course ';
$string['criteria_descr_single_4'] = 'Learners must complete the course ';
$string['criteria_descr_2'] = '<p>This badge has to be awarded by the users with <b>{$a}</b> of the following roles:</p>';
$string['criteria_descr_single_2'] = '<p>This badge has to be awarded by a user with the following role:</p>';
$string['criteria_descr_5'] = '<p><b>{$a}</b> of the following courses have to be completed:</p>';
$string['criteria_descr_single_5'] = '<p>The following course has to be completed:</p>';
$string['criteria_descr_6'] = '<p><b>{$a}</b> of the following user profile fields have to be completed:</p>';
$string['criteria_descr_single_6'] = '<p>The following user profile field has to be completed:</p>';
$string['criteria_descr_grade'] = ' with minimum grade of <i>{$a}</i> ';
$string['criteria_descr_bydate'] = ' by <i>{$a}</i> ';

// Badge details.
$string['addtobackpack'] = 'Add to backpack';
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
$string['donotaward'] = 'Currently, this badge is not active, so it cannot be awarded to users. If you would like to award this badge, please set its status to active.';
$string['evidence'] = 'Evidence';
$string['existingrecipients'] = 'Existing badge recipients';
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
$string['numawards'] = 'This badge has been issued to {$a} user(s).';
$string['noexpiry'] = 'This badge does not have an expiry date.';
$string['notacceptedrole'] = 'Your current role assignment is not among the roles that can manually issue this badge. <br/>
If you would like to see users who have already earned this badge, you can visit {$a} page. ';
$string['overallcriteriaaggregation'] = 'Overall badge criteria aggregation';
$string['perpage'] = 'Records per page';
$string['potentialrecipients'] = 'Potential badge recipients';
$string['proceed'] = 'Proceed';
$string['recipients'] = 'Badge recipients';
$string['relative'] = 'Relative date';
$string['reviewbadge'] = 'Review badge criteria';
$string['reviewconfirm'] = '<p>This action will perform a check if any of the users have already completed all the requirements for \'{$a}\' badge? </p>
<p>Would you like to proceed?</p>';
$string['subject'] = 'Message subject';
$string['selecting'] = 'With selected badges...';
$string['status'] = 'Badge status';
$string['visible'] = 'Visible';
$string['year'] = 'Year(s)';

// Default badge message.
$string['creatorsubject'] = '\'{$a}\' has been awarded!';
$string['creatorbody'] = '{$a->user} has completed all badge requirements and has been awarded the badge.

View issued badge at {$a->link}';
$string['messagebody'] = '<p>You have been awarded a badge "%badgename%"!</p>

<p>More infromation about this badge can be found at %badgelink%.</p>

<p>If there is no badge attached to this email, you can manage and download it from {$a} page.</p>';

$string['messagesubject'] = 'Congratulations! You just earned a badge!';

// Help text for elements.
$string['actions_help'] = '';
$string['attachment_help'] = 'If this \'Attachment\' is checked, an issued badge will be attached to the recepient\'s email for download';
$string['contact_help'] = 'An email address associated with the badge issuer.';
$string['criteria_0_help'] = 'Overall help';
$string['criteria_1_help'] = 'Activity completion allows to award a badge to users who have completed a set of selected activities.';
$string['criteria_2_help'] = 'This criterion allows to award a badge manually by the users with selected roles.';
$string['criteria_3_help'] = 'Social';
$string['criteria_4_help'] = 'Course completion allows to award a badge to users who have completed the course. This criterion can have additional parameters such as minimum grade and date of course completion.';
$string['criteria_5_help'] = 'Courseset completion allows to award a badge to users who have completed a set of selected courses. Each course can have additional parameters such as minimum grade and date of course completion. ';
$string['criteria_6_help'] = 'Profile completion allows to award a badge when a user completes selected fields in their profile. You can select from default and custom profile fields that are available to users. ';
$string['expirydate_help'] = 'Optionally, badges can expire on a specific date, or such date can also be calculated based on the date when this badge was issued to a user. ';
$string['badgeimage_help'] = 'This is an image that will be used when this badge is issued.

To add a new image, browse and select an image (in JPG or PNG format) then click "Save changes". The image will be cropped to a square and resized to match badge image requirements. ';

$string['issuerurl_help'] = 'Origin of the issuer.

This setting should be in form &lt;protocol>://&lt;host>:&lt;port>.';

$string['issuername_help'] = 'Name of the issuing agent or authority.';
$string['notification'] = 'Notify badge creator';
$string['notification_help'] = 'If the \'Notify badge creator\' box is checked, the badge creator will also be sent a notification about the badge issue.';
$string['status_help'] = 'Badge status determines its behaviour in the system:

* **ACTIVE** – Active badge means that this badge can we earned by users, but it has not been issued yet. While this badge remains active, its criteria cannot be changed.

* **INACTIVE** – Inactive badge means that this badge is not available to users and cannot be earned. Criteria of an inactive badge can be changed.

* **ACTIVE AND LOCKED** – Badges with such status can be earned by users, but they have been issued in the past. Therefore, their criteria are locked and cannot be changed any more.

* **INACTIVE AND LOCKED** – Inactive badges cannot be earned by users and their criteria cannot be updated.

Badges are set to locked automatically once they have been issued to at least one user. Unlike active or inactive, this property cannot be manually changed. If you need to modify details or criteria of a locked badge, you can duplicate this badge and make all the required changes.

*Why do we lock badges?*

We want to make sure that all users complete the same requirements to earn a badge. Currently, it is not possible to revoke badges. If we allowed badges requirements to be modified all the time, we would most likely end up with users having the same badge for meeting completely different requirements.';
$string['variablesubstitution'] = 'Variable substitution in messages.';
$string['variablesubstitution_help'] = 'In a badge message, certain variables can be inserted into the subject and/or body of a message so that they will be replaced with real values when the message is sent. The variables should be inserted into the the text exactly as they are shown below. The following variables can be used:

%badgename%
:   This will be replaced by the badge\'s full name.

%username%
:   This will be replaced by the recipient\'s full name.

%badgelink%
:   This will be replaced by the public URL with information about the issued badge.';
$string['visible_help'] = 'This setting indicates whether badge is visible to its potential earners.

If this checkbox is checked and the badge is active, users will be able to see the badge in the list of site or course badges.

Important! Hiding a badge from users does not prevent them from earning it if the badge is active.';

// Error messages.
$string['error:cannotact'] = 'Cannot activate the badge. ';
$string['error:cannotawardbadge'] = 'Cannot award badge to a user.';
$string['error:clone'] = 'Cannot clone the badge.';
$string['error:save'] = 'Cannot save the badge.';
$string['error:missingcourse'] = 'It looks like this course does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:missingfield'] = 'It looks like this profile field does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:missingmodule'] = 'It looks like this activity does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:missingrole'] = 'It looks like this role does not exist any more. If you want users to be able to earn this badge, please revise this parameter';
$string['error:noactivities'] = 'No activities with completion criteria enabled.';
$string['error:nosuchcourse'] = 'Warning: This course is no longer available.';
$string['error:nosuchfield'] = 'Warning: This user profile field is no longer available.';
$string['error:nosuchmod'] = 'Warning: This activity is no longer available.';
$string['error:nosuchrole'] = 'Warning: This role is no longer available.';
$string['error:nosuchbadge'] = 'Badge with id {$a} does not exist.';
$string['error:nosuchuser'] = 'User with such email does not have an account with the current backpack provider';
$string['error:invalidcriteriatype'] = 'Invalid criteria type.';
$string['error:invalidbadgeurl'] = 'Invalid badge issuer URL format. If it is possible, try using {$a}';
$string['error:invalidexpireperiod'] = 'Expiry period cannot be negative or equal 0.';
$string['error:invalidexpiredate'] = 'Expiry date has to be in the future.';
$string['error:nopermissiontoview'] = 'You have no permissions to view badge recipients';
