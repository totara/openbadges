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
 * Renderer for use with the badges output
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/filters/lib.php');

/**
 * Standard HTML output renderer for badges
 */
class core_badges_renderer extends plugin_renderer_base {

    // Outputs list in of badges in grid-like view.
    public function display_badges_list($badges, $size = 'meduim') {
        $items = array();

        foreach ($badges as $badge) {
            $attributes = array();
            $attributes['class'] = 'badge-icon-' . $size;
            $attributes['alt'] = $badge->name;
            $attributes['src'] = ''; // Get file here.
            $items[] = html_writer::empty_tag('img', $attributes);
        }

        $output = html_writer::alist($items, array('id' => 'badges-list'));

        return $output;
    }

    // Prints a table of users who have earned the badge.
    public function print_awarded_table($awards, $search = null) {
        global $PAGE;
        $totalcount = count($awards);
        $perpage = 25;

        // create the user filter form
        $ufiltering = new user_filtering(array('realname' => 0), $PAGE->url);
        $ufiltering->display_add();
        $ufiltering->display_active();

        $table = new flexible_table('badge-recipients-table');
        $table->define_columns(array('userid', 'dateissued', 'uniquehash'));
        $table->define_headers(array(get_string('user'), get_string('dateawarded', 'badges'), get_string('badgeurl', 'badges')));
        $table->define_baseurl($PAGE->url);
        $table->no_sorting('uniquehash');
        $table->sortable(true, 'dateissued', SORT_DESC);
        $table->pageable(true);
        $table->initialbars($totalcount > $perpage);
        $table->pagesize($perpage, $totalcount);
        $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
        $table->setup();

        foreach ($awards as $award) {
            $row = array(
                    fullname($award),
                    userdate($award->dateissued),
                    html_writer::link(
                            new moodle_url('/badges/badge.php', array('hash' => $award->uniquehash)),
                            get_string('viewbadge', 'badges')
                            )
                    );
            $table->add_data($row);
        }

        $table->finish_output();
    }

    // Prints a table of users who have earned the badge.
    public function print_badge_overview($badge, $context) {
        $display = "";

        // Current badge status.
        $status = get_string('currentstatus', 'badges') . $badge->get_status_name();
        $display .= $this->output->heading_with_help($status, 'status', 'badges');

        // Badge details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('badgedetails','badges'), array('class' => 'bold'));
        $display .= "";
        $display .= html_writer::end_tag('fieldset');

        // Issuer details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuerdetails','badges'), array('class' => 'bold'));
        $display .= "";
        $display .= html_writer::end_tag('fieldset');

        // Issuance details if any.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuancedetails','badges'), array('class' => 'bold'));
        if ($badge->can_expire()) {
            if ($badge->expiredate) {
                $display .= get_string('expiredate', 'badges', userdate($badge->expiredate));
            } else if ($badge->expireperiod) {
                $display .= get_string('expireperiod', 'badges', $badge->expireperiod / 60 / 60 / 24);
            }
        } else {
            $display .= get_string('noexpiry', 'badges');
        }
        $display .= html_writer::end_tag('fieldset');

        // Criteria details if any.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('bcriteria','badges'), array('class' => 'bold'));
        if ($badge->has_criteria()) {
            $display .= '';
        } else {
            $display .= get_string('nocriteria', 'badges');
        }
        $display .= html_writer::end_tag('fieldset');

        // Awards details if any.
        if (has_capability('moodle/badges:viewawarded', $context)) {
            $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
            $display .= html_writer::tag('legend', get_string('awards','badges'), array('class' => 'bold'));
            if ($badge->has_awards()) {
                $display .= get_string('numawards', 'badges', count($badge->awards));
            } else {
                $display .= get_string('noawards', 'badges');
            }
            $display .= html_writer::end_tag('fieldset');
        }

        return $display;
    }

    // Prints action buttons available for the badge.
    public function print_badge_overview_actions($badge, $context) {
        $table = new html_table();
        $table->attributes = array('class' => 'clearfix', 'id' => 'badgeactions');

        $actions = array();
        if (has_capability('moodle/badges:deletebadge', $context)) {
            $actions[] = $this->output->single_button(
                    new moodle_url('/badges/action.php', array('id' => $badge->id, 'delete' => 1)),
                    get_string('delete'));
        }

        if (has_capability('moodle/badges:createbadge', $context)) {
            $actions[] = $this->output->single_button(
                    new moodle_url('/badges/action.php', array('id' => $badge->id, 'copy' => 1)),
                    get_string('duplicate'));
        }

        if (has_capability('moodle/badges:configurecriteria', $context)) {
            if ($badge->is_locked()) {
                $actions[] = "";
            } else if ($badge->is_active()) {
                $actions[] = $this->output->single_button(
                        new moodle_url('/badges/action.php', array('id' => $badge->id, 'lock' => 1)),
                        get_string('deactivate', 'badges'));
            } else {
                $actions[] = $this->output->single_button(
                        new moodle_url('/badges/action.php', array('id' => $badge->id, 'activate' => 1)),
                        get_string('activate', 'badges'));
            }
        }

        $table->data[] = $actions;
        return html_writer::table($table);
    }

    // Prints tabs for badge editing.
    public function print_badge_tabs($badgeid, $context, $current = 'overview') {
        global $DB;

        $tabs = $row = array();

        $row[] = new tabobject('overview',
                    new moodle_url('/badges/overview.php', array('id' => $badgeid)),
                    get_string('boverview', 'badges')
                );

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $row[] = new tabobject('details',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'details')),
                        get_string('bdetails', 'badges')
                    );
        }

        if (has_capability('moodle/badges:configurecriteria', $context)) {
            $row[] = new tabobject('criteria',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'criteria')),
                        get_string('bcriteria', 'badges')
                    );
        }

        if (has_capability('moodle/badges:configuremessages', $context)) {
            $row[] = new tabobject('message',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'message')),
                        get_string('bmessage', 'badges')
                    );
        }

        if (has_capability('moodle/badges:viewawarded', $context)) {
            $awarded = $DB->count_records('badge_issued', array('badgeid' => $badgeid));
            $row[] = new tabobject('awards',
                        new moodle_url('/badges/awards.php', array('id' => $badgeid)),
                        get_string('bawards', 'badges', $awarded)
                    );
        }

        $tabs[] = $row;

        print_tabs($tabs, $current);
    }
}