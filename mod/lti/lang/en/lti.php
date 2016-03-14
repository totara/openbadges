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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file contains en_utf8 translation of the Basic LTI module
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['accept'] = 'Accept';
$string['accepted'] = 'Accepted';
$string['accept_grades'] = 'Accept grades from the tool';
$string['accept_grades_admin'] = 'Accept grades from the tool';
$string['accept_grades_admin_help'] = 'Specify whether the tool provider can add, update, read, and delete grades associated with instances of this tool type.

Some tool providers support reporting grades back to Moodle based on actions taken within the tool, creating a more integrated
experience.';
$string['accept_grades_help'] = 'Specify whether the tool provider can add, update, read, and delete grades associated only with this external tool instance.

Some tool providers support reporting grades back to Moodle based on actions taken within the tool, creating a more integrated experience.

Note that this setting may be overridden in the tool configuration.';
$string['action'] = 'Action';
$string['active'] = 'Active';
$string['activity'] = 'Activity';
$string['addnewapp'] = 'Enable external application';
$string['addserver'] = 'Add new trusted server';
$string['addtype'] = 'Add external tool configuration';
$string['allow'] = 'Allow';
$string['allowinstructorcustom'] = 'Allow teachers to add custom parameters';
$string['allowsetting'] = 'Allow tool to store 8K of settings in Moodle';
$string['always'] = 'Always';
$string['automatic'] = 'Automatic, based on launch URL';
$string['baseurl'] = 'Base URL/tool registration name';
$string['basiclti'] = 'LTI';
$string['basiclti_base_string'] = 'LTI OAuth base string';
$string['basiclti_endpoint'] = 'LTI launch endpoint';
$string['basiclti_in_new_window'] = 'Your activity has opened in a new window';
$string['basiclti_parameters'] = 'LTI launch parameters';
$string['basicltiactivities'] = 'LTI activities';
$string['basicltifieldset'] = 'Custom example fieldset';
$string['basicltiintro'] = 'Activity description';
$string['basicltiname'] = 'Activity name';
$string['basicltisettings'] = 'Basic Learning Tool Interoperability (LTI) settings';
$string['cancel'] = 'Cancel';
$string['cancelled'] = 'Cancelled';
$string['cannot_delete'] = 'You may not delete this tool configuration.';
$string['cannot_edit'] = 'You may not edit this tool configuration.';
$string['capabilities'] = 'Capabilities';
$string['capabilities_help'] = 'Select those capabilities which you wish to offer to the tool provider.  More than one capability can be selected.';
$string['click_to_continue'] = '<a href="{$a->link}" target="_top">Click to continue</a>';
$string['comment'] = 'Comment';
$string['configpassword'] = 'Default remote tool password';
$string['configpreferheight'] = 'Default preferred height';
$string['configpreferwidget'] = 'Set widget as default launch';
$string['configpreferwidth'] = 'Default preferred width';
$string['configresourceurl'] = 'Default resource URL';
$string['configtoolurl'] = 'Default remote tool URL';
$string['configtypes'] = 'Enable LTI applications';
$string['configured'] = 'Configured';
$string['course_tool_types'] = 'Course tool types';
$string['courseid'] = 'Course ID number';
$string['coursemisconf'] = 'Course is misconfigured';
$string['createdon'] = 'Created on';
$string['curllibrarymissing'] = 'PHP Curl library must be installed to use LTI';
$string['custom'] = 'Custom parameters';
$string['custom_config'] = 'Using custom tool configuration.';
$string['custom_help'] = 'Custom parameters are settings used by the tool provider. For example, a custom parameter may be used to display
a specific resource from the provider.  Each parameter should be entered on a separate line using a format of "name=value"; for example, "chapter=3".

It is safe to leave this field unchanged unless directed by the tool provider.';
$string['custominstr'] = 'Custom parameters';
$string['debuglaunch'] = 'Debug option';
$string['debuglaunchoff'] = 'Normal launch';
$string['debuglaunchon'] = 'Debug launch';
$string['default'] = 'Default';
$string['default_launch_container'] = 'Default launch container';
$string['default_launch_container_help'] = 'The launch container affects the display of the tool when launched from the course. Some launch containers provide more screen
real estate to the tool, and others provide a more integrated feel with the Moodle environment.

* **Default** - Use the launch container specified by the tool configuration.
* **Embed** - The tool is displayed within the existing Moodle window, in a manner similar to most other Activity types.
* **Embed, without blocks** - The tool is displayed within the existing Moodle window, with just the navigation controls
        at the top of the page.
* **New window** - The tool opens in a new window, occupying all the available space.
        Depending on the browser, it will open in a new tab or a popup window.
        It is possible that browsers will prevent the new window from opening.';
$string['delegate'] = 'Delegate to teacher';
$string['delete'] = 'Delete';
$string['delete_confirmation'] = 'Are you sure you want to delete this external tool configuration?';
$string['deletetype'] = 'Delete external tool configuration';
$string['display_description'] = 'Display activity description when launched';
$string['display_description_help'] = 'If selected, the activity description (specified above) will display above the tool provider\'s content.

The description may be used to provide additional instructions for launchers of the tool, but it is not required.

The description is never displayed when the tool\'s launch container is in a new window.';
$string['display_name'] = 'Display activity name when launched';
$string['display_name_help'] = 'If selected, the activity name (specified above) will display above the tool provider\'s content.

It is possible that the tool provider may also display the title. This option can prevent the activity title from
being displayed twice.

The title is never displayed when the tool\'s launch container is in a new window.';
$string['domain_mismatch'] = 'Launch URL\'s domain does not match tool configuration.';
$string['donot'] = 'Do not send';
$string['donotaccept'] = 'Do not accept';
$string['donotallow'] = 'Do not allow';
$string['duplicateregurl'] = 'This registration URL is already in use';
$string['edittype'] = 'Edit external tool configuration';
$string['embed'] = 'Embed';
$string['embed_no_blocks'] = 'Embed, without blocks';
$string['enableemailnotification'] = 'Send notification emails';
$string['enableemailnotification_help'] = 'If enabled, students will receive email notification when their tool submissions are graded.';
$string['errormisconfig'] = 'Misconfigured tool. Please ask your Moodle administrator to fix the configuration of the tool.';
$string['existing_window'] = 'Existing window';
$string['extensions'] = 'LTI extension services';
$string['external_tool_type'] = 'External tool type';
$string['external_tool_type_help'] = 'The main purpose of a tool configuration is to set up a secure communication channel between Moodle and the tool provider.
It also provides an opportunity for configuration defaults and setting up additional services provided by the tool.

* **Automatic, based on Launch URL** - This setting should be used in almost all cases. Moodle will select the most appropriate tool configuration
       based on the Launch URL. Tools configured by both an administrator or within this course will be used.
       When the Launch URL is specified, Moodle will provide feedback on whether it recognizes it or not. If Moodle does not recognize the Launch URL,
       you may need to enter the tool configuration details manually.
* **A specific tool type** - By selecting a specific tool type, you can force Moodle to use that tool configuration when communicating with the
       external tool provider. If the Launch URL does not appear to belong to the tool provider, a warning will appear. In some cases, it is not necessary
       to enter a Launch URL when providing a specific tool type (if not launching to a particular resource within the tool provider).
* **Custom configuration** - To setup custom tool configuration on just this instance, show Advanced options, and enter the consumer key and
       shared secret yourself. If you do not have a consumer key and shared secret, you may be able to request them from the tool provider.
       Not all tools require a consumer key and shared secret, in which case the fields may be left blank.

### Tool type editing

Three icons are available after the External tool type dropdown list:

* **Add** - Create a course level tool configuration. All External Tool instances in this course may use the tool configuration.
* **Edit** - Select a course level tool type from the dropdown, then click this icon. The details of the tool configuration may be edited.
* **Delete** - Remove the selected course level tool type.';
$string['external_tool_types'] = 'External tool types';
$string['failedtoconnect'] = 'Moodle was unable to communicate with the "{$a}" system';
$string['filter_basiclti_configlink'] = 'Configure your preferred sites and their passwords';
$string['filter_basiclti_password'] = 'Password is mandatory';
$string['filterconfig'] = 'LTI administration';
$string['filtername'] = 'LTI';
$string['fixexistingconf'] = 'Use an existing configuration for the misconfigured instance';
$string['fixnew'] = 'New configuration';
$string['fixnewconf'] = 'Define a new configuration for the misconfigured instance';
$string['fixold'] = 'Use existing';
$string['forced_help'] = 'This setting has been forced in a course or site level tool configuration. You may not change it from this interface.';
$string['force_ssl'] = 'Force SSL';
$string['force_ssl_help'] = 'Selecting this option forces all launches to this tool provider to use SSL.

In addition, all web service requests from the tool provider will use SSL.

If using this option, confirm that this Moodle site and the tool provider support SSL.';
$string['generaltool'] = 'General tool';
$string['global_tool_types'] = 'Global tool types';
$string['grading'] = 'Grade routing';
$string['icon_url'] = 'Icon URL';
$string['icon_url_help'] = 'The icon URL allows the icon that shows up in the course listing for this activity to be modified. Instead of using the default
LTI icon, an icon which conveys the type of activity may be specified.';
$string['id'] = 'id';
$string['invalidid'] = 'LTI ID was incorrect';
$string['launch_in_moodle'] = 'Launch tool in moodle';
$string['launch_in_popup'] = 'Launch tool in a pop-up';
$string['launch_url'] = 'Launch URL';
$string['launch_url_help'] = 'The Launch URL indicates the web address of the External Tool, and may contain additional information, such as the resource to show.
If you are unsure what to enter for the Launch URL, please check with the tool provider for more information.

If you have selected a specific tool type, you may not need to enter a Launch URL. If the tool link is used to just launch
into the tool provider\'s system, and not go to a specific resource, this will likely be the case.';
$string['launchinpopup'] = 'Launch container';
$string['launchinpopup_help'] = 'The launch container affects the display of the tool when launched from the course. Some launch containers provide more screen
real estate to the tool, and others provide a more integrated feel with the Moodle environment.

* **Default** - Use the launch container specified by the tool configuration.
* **Embed** - The tool is displayed within the existing Moodle window, in a manner similar to most other Activity types.
* **Embed, without blocks** - The tool is displayed within the existing Moodle window, with just the navigation controls
        at the top of the page.
* **New window** - The tool opens in a new window, occupying all the available space.
        Depending on the browser, it will open in a new tab or a popup window.
        It is possible that browsers will prevent the new window from opening.';
$string['launchoptions'] = 'Launch options';
$string['lti'] = 'LTI';
$string['lti:addinstance'] = 'Add new external tool activities';
$string['lti:addcoursetool'] = 'Add course-specific tool configurations';
$string['lti:grade'] = 'View grades returned by the external tool';
$string['lti:manage'] = 'Be an Instructor when the tool is launched';
$string['lti:requesttooladd'] = 'Request a tool is configured site-wide';
$string['lti:view'] = 'Launch external tool activities';
$string['ltisettings'] = 'LTI settings';
$string['lti_administration'] = 'LTI administration';
$string['lti_errormsg'] = 'The tool returned the following error message: "{$a}"';
$string['lti_launch_error'] = 'An error occurred when launching the external tool:';
$string['lti_launch_error_tool_request'] = '<p>
To submit a request for an administrator to complete the tool configuration, click <a href="{$a->admin_request_url}" target="_top">here</a>.
</p>';
$string['lti_launch_error_unsigned_help'] = '<p>This error may be a result of a missing consumer key and shared secret for the tool provider.</p>
<p>If you have a consumer key and shared secret, you may enter it when editing the external tool instance (make sure advanced options are visible).</p>
<p>Alternatively, you may <a href="{$a->course_tool_editor}">create a course level tool provider configuration</a>.</p>';
$string['lti_tool_request_added'] = 'Tool configuration request successfully submitted. You may need to contact an administrator to complete the tool configuration.';
$string['lti_tool_request_existing'] = 'A tool configuration for the tool domain has already been submitted.';
$string['ltiunknownserviceapicall'] = 'LTI unknown service API call.';
$string['main_admin'] = 'General help';
$string['main_admin_help'] = 'External tools allow Moodle users to seamlessly interact with learning resources hosted remotely. Through a special
launch protocol, the remote tool will have access to some general information about the launching user. For example,
the institution name, course id, user id, and other information such as the user\'s name or e-mail address.

Tool types listed on this page are separated into three categories:

* **Active** - These tool providers have been approved and configured by an administrator. They can be used from within any
        course on this Moodle instance. If a consumer key and shared secret are entered, a trust relationship is established
        between this Moodle instance and the remote tool, providing a secure communication channel.
* **Pending** - These tool providers came in through a package import, but have not been configured by an administrator.
        Teachers may still use tools from these providers if they have a consumer key and shared secret, or if none is required.
* **Rejected** - These tools providers are flagged as ones which an administrator has no intention of making available to the entire
        Moodle instance. Teachers may still use tools from these providers if they have a consumer key and shared secret, or if none is required.';
$string['manage_tools'] = 'Manage external tool types';
$string['manage_tool_proxies'] = 'Manage external tool registrations';
$string['miscellaneous'] = 'Miscellaneous';
$string['misconfiguredtools'] = 'Misconfigured tool instances were detected';
$string['missingparameterserror'] = 'The page is misconfigured: "{$a}"';
$string['module_class_type'] = 'Moodle module type';
$string['modulename'] = 'External tool';
$string['modulename_help'] = 'The external tool activity module enables students to interact with learning resources and activities on other web sites. For example, an external tool could provide access to a new activity type or learning materials from a publisher.

To create an external tool activity, a tool provider which supports LTI (Learning Tools Interoperability) is required. A teacher can create an external tool activity or make use of a tool configured by the site administrator.

External tool activities differ from URL resources in a few ways:

* External tools are context aware i.e. they have access to information about the user who launched the tool, such as institution, course and name
* External tools support reading, updating, and deleting grades associated with the activity instance
* External tool configurations create a trust relationship between your site and the tool provider, allowing secure communication between them';
$string['modulename_link'] = 'mod/lti/view';
$string['modulenameplural'] = 'External tools';
$string['modulenamepluralformatted'] = 'LTI Instances';
$string['name'] = 'Name';
$string['never'] = 'Never';
$string['new_window'] = 'New window';
$string['no_lti_configured'] = 'There are no active external tools configured.';
$string['no_lti_pending'] = 'There are no pending external tools.';
$string['no_lti_rejected'] = 'There are no rejected external tools.';
$string['no_tp_accepted'] = 'There are no accepted external tool registrations.';
$string['no_tp_cancelled'] = 'There are no cancelled external tool registrations.';
$string['no_tp_configured'] = 'There are no unregistered external tool registrations configured.';
$string['no_tp_pending'] = 'There are no pending external tool registrations.';
$string['no_tp_rejected'] = 'There are no rejected external tool registrations.';
$string['noattempts'] = 'No attempts have been made on this tool instance';
$string['noltis'] = 'There are no external tool instances';
$string['noprofileservice'] = 'Profile service not found';
$string['noservers'] = 'No servers found';
$string['notypes'] = 'There are currently no LTI tools set up in Moodle. Click the Install link above to add some.';
$string['noviewusers'] = 'No users were found with permissions to use this tool';
$string['optionalsettings'] = 'Optional settings';
$string['organization'] = 'Organization details';
$string['organizationdescr'] = 'Organization description';
$string['organizationid'] = 'Organization ID';
$string['organizationid_help'] = 'A unique identifier for this Moodle instance. Typically, the DNS name of the organization is used.

If this field is left blank, the host name of this Moodle site will be used as the default value.';
$string['organizationurl'] = 'Organization URL';
$string['organizationurl_help'] = 'The base URL of this Moodle instance.

If this field is left blank, a default value will be used based on the site configuration.';
$string['pagesize'] = 'Submissions shown per page';
$string['parameter'] = 'Tool parameters';
$string['parameter_help'] = 'Tool parameters are settings requested to be passed by the tool provider in the accepted tool proxy.';
$string['password'] = 'Shared secret';
$string['password_admin'] = 'Shared secret';
$string['password_admin_help'] = 'The shared secret can be thought of as a password used to authenticate access to the tool. It should be provided
along with the consumer key from the tool provider.

Tools which do not require secure communication from Moodle and do not provide additional services (such as grade reporting)
may not require a shared secret.';
$string['password_help'] = 'For pre-configured tools, it is not necessary to enter a shared secret here, as the shared secret will be
provided as part of the configuration process.

This field should be entered if creating a link to a tool provider which is not already configured.
If the tool provider is to be used more than once in this course, adding a course tool configuration is a good idea.

The shared secret can be thought of as a password used to authenticate access to the tool. It should be provided
along with the consumer key from the tool provider.

Tools which do not require secure communication from Moodle and do not provide additional services (such as grade reporting)
may not require a shared secret.';
$string['pending'] = 'Pending';
$string['pluginadministration'] = 'LTI administration';
$string['pluginname'] = 'LTI';
$string['preferheight'] = 'Preferred height';
$string['preferwidget'] = 'Prefer widget launch';
$string['preferwidth'] = 'Preferred width';
$string['press_to_submit'] = 'Press to launch this activity';
$string['privacy'] = 'Privacy';
$string['quickgrade'] = 'Allow quick grading';
$string['quickgrade_help'] = 'If enabled, multiple tools can be graded on one page. Add grades and comments then click the "Save all my feedback" button to save all changes for that page.';
$string['redirect'] = 'You will be redirected in few seconds. If you are not, press the button.';
$string['register'] = 'Register';
$string['register_warning'] = 'The registration page seems to be taking a while to open.  If it does not appear, check that you entered the correct URL in the configuration settings.';
$string['registertype'] = 'Configure a new external tool registration';
$string['registration_options'] = 'Registration options';
$string['registrationname'] = 'Tool provider name';
$string['registrationname_help'] = 'Enter the name of the tool provider being registered.';
$string['registrationurl'] = 'Registration URL';
$string['registrationurl_help'] = 'The registration URL should be available from the tool provider as the location to which registration requests should be sent.';
$string['reject'] = 'Reject';
$string['rejected'] = 'Rejected';
$string['resource'] = 'Resource';
$string['resourcekey'] = 'Consumer key';
$string['resourcekey_admin'] = 'Consumer key';
$string['resourcekey_admin_help'] = 'The consumer key can be thought of as a username used to authenticate access to the tool.
It can be used by the tool provider to uniquely identify the Moodle site from which users launch into the tool.

The consumer key must be provided by the tool provider. The method of obtaining a consumer key varies between
tool providers. It may be an automated process, or it may require a dialogue with the tool provider.

Tools which do not require secure communication from Moodle and do not provide additional services (such as grade reporting)
may not require a resource key.';
$string['resourcekey_help'] = 'For pre-configured tools, it is not necessary to enter a resource key here, as the consumer key will be
provided as part of the configuration process.

This field should be entered if creating a link to a tool provider which is not already configured.
If the tool provider is to be used more than once in this course, adding a course tool configuration is a good idea.

The consumer key can be thought of as a username used to authenticate access to the tool.
It can be used by the tool provider to uniquely identify the Moodle site from which users launch into the tool.

The consumer key must be provided by the tool provider. The method of obtaining a consumer key varies between
tool providers. It may be an automated process, or it may require a dialogue with the tool provider.

Tools which do not require secure communication from Moodle and do not provide additional services (such as grade reporting)
may not require a resource key.';
$string['resourceurl'] = 'Resource URL';
$string['return_to_course'] = 'Click <a href="{$a->link}" target="_top">here</a> to return to the course.';
$string['saveallfeedback'] = 'Save all my feedback';
$string['search:activity'] = 'LTI activities';
$string['secure_icon_url'] = 'Secure icon URL';
$string['secure_icon_url_help'] = 'Similar to the icon URL, but used if the user accessing Moodle securely through SSL. The main purpose for this field is to prevent
the browser from warning the user if the underlying page was accessed over SSL, but requesting to show an unsecure image.';
$string['secure_launch_url'] = 'Secure launch URL';
$string['secure_launch_url_help'] = 'Similar to Launch URL, but used instead of the launch url if high security is required. Moodle will use the
secure launch URL instead of the launch URL if the Moodle site is accessed through SSL, or if the tool configuration
is set to always launch through SSL.

The Launch URL may also be set to an https address to force launching through SSL, and this field may be left blank.';
$string['send'] = 'Send';
$string['services'] = 'Services';
$string['services_help'] = 'Select those services which you wish to offer to the tool provider.  More than one service can be selected.';
$string['setupoptions'] = 'Set-up options';
$string['share_email'] = 'Share launcher\'s email with the tool';
$string['share_email_admin'] = 'Share launcher\'s email with tool';
$string['share_email_admin_help'] = 'Specify whether the e-mail address of the user launching the tool will be shared with the tool provider.
The tool provider may need launcher\'s e-mail addresses to distinguish users with the same name in the UI, or send e-mails
to users based on actions within the tool.';
$string['share_email_help'] = 'Specify whether the e-mail address of the user launching the tool will be shared with the tool provider.

The tool provider may need launcher\'s email addresses to distinguish users with the same name, or send emails to users based on actions within the tool.

Note that this setting may be overridden in the tool configuration.';
$string['share_name'] = 'Share launcher\'s name with the tool';
$string['share_name_admin'] = 'Share launcher\'s name with tool';
$string['share_name_admin_help'] = 'Specify whether the full name of the user launching the tool should be shared with the tool provider.
The tool provider may need launchers\' names to show meaningful information within the tool.';
$string['share_name_help'] = 'Specify whether the full name of the user launching the tool should be shared with the tool provider.

The tool provider may need launchers\' names to show meaningful information within the tool.

Note that this setting may be overridden in the tool configuration.';
$string['share_roster'] = 'Allow the tool to access this course\'s roster';
$string['share_roster_admin'] = 'Tool may access course roster';
$string['share_roster_admin_help'] = 'Specify whether the tool can access the list of users enrolled in courses from which this tool type is launched.';
$string['share_roster_help'] = 'Specify whether the tool can access the list of users enrolled in this course.

Note that this setting may be overridden in the tool configuration.';
$string['show_in_course'] = 'Show tool type when creating tool instances';
$string['show_in_course_help'] = 'If selected, this tool configuration will appear in the "External tool type" dropdown when teachers
configure external tools within courses.

In most cases, this option does not need to be selected. Teachers can use this tool configuration
based on the Launch URL matching the Tool base URL, which is the preferred method.

The only case in which this option should be selected is if the tool configuration is just intended for single sign on.
For example, if all launches to the tool provider just take the user to a landing page instead of to a specific resource.';
$string['size'] = 'Size parameters';
$string['submission'] = 'Submission';
$string['submissions'] = 'Submissions';
$string['submissionsfor'] = 'Submissions for {$a}';
$string['subplugintype_ltiresource'] = 'LTI service resource';
$string['subplugintype_ltiresource_plural'] = 'LTI service resources';
$string['subplugintype_ltiservice'] = 'LTI service';
$string['subplugintype_ltiservice_plural'] = 'LTI services';
$string['subplugintype_ltisource'] = 'LTI source';
$string['subplugintype_ltisource_plural'] = 'LTI sources';
$string['toggle_debug_data'] = 'Toggle debug data';
$string['tool_config_not_found'] = 'Tool configuration not found for this URL.';
$string['tool_settings'] = 'Tool settings';
$string['toolproxy'] = 'External tool registrations';
$string['toolproxy_help'] = 'External tool registrations allow Moodle site administrators to configure external tools from a tool proxy obtained from a tool provider supporting LTI 2.0. A registration URL provided by the tool provider is all that is required to initiate the process. The capabilities and services offered to the tool provider are selected when configuring a new registration.

Tool registrations listed on this page are separated into four categories:

* **Configured** - These tool registrations have been set up but the registration process has not yet been started.
* **Pending** - The registration process for these tool registrations has been started but has not completed. Open and save the settings to move it
back to the \'Configured\' category.
* **Accepted** - These tool registrations have been approved; the resources specified in the tool proxy will appear on the external tool types page
with an initial status of \'Pending\'.
* **Rejected** - These tool registrations are ones which were rejected during the registration process. Open and save the settings to move it
back to the \'Configured\' category so the registration process can be restarted.';
$string['toolproxyregistration'] = 'External tool registration';
$string['toolregistration'] = 'External tool registration';
$string['toolsetup'] = 'External tool configuration';
$string['toolurl'] = 'Tool base URL';
$string['toolurl_help'] = 'The tool base URL is used to match tool launch URLs to the correct tool configuration. Prefixing the URL with http(s) is optional.

Additionally, the base URL is used as the launch URL if a launch URL is not specified in the external tool instance.

For example, a base URL of *tool.com* would match the following:

* tool.com
* tool.com/quizzes
* tool.com/quizzes/quiz.php?id=10
* www.tool.com/quizzes

A base URL of *www.tool.com/quizzes* would match the following:

* www.tool.com/quizzes
* tool.com/quizzes
* tool.com/quizzes/take.php?id=10

A base URL of *quiz.tool.com* would match the following:

* quiz.tool.com
* quiz.tool.com/take.php?id=10

If two different tool configurations are for the same domain, the most specific match will be used.';
$string['typename'] = 'Tool name';
$string['typename_help'] = 'The tool name is used to identify the tool provider within Moodle. The name entered will be visible
to teachers when adding external tools within courses.';
$string['types'] = 'Types';
$string['update'] = 'Update';
$string['using_tool_configuration'] = 'Using tool configuration: ';
$string['validurl'] = 'A valid URL must start with http(s)://';
$string['viewsubmissions'] = 'View submissions and grading screen';
