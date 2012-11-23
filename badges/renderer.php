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
    public function print_badges_list($badges, $size = 'meduim') {
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

    // Prints a badge overview infomation.
    public function print_badge_overview($badge, $context) {
        $display = "";

        // Current badge status.
        $status = get_string('currentstatus', 'badges') . $badge->get_status_name();
        $display .= $this->output->heading_with_help($status, 'status', 'badges');

        // Badge details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('badgedetails','badges'), array('class' => 'bold'));

        $detailstable = new html_table();
        $detailstable->attributes = array('class' => 'clearfix', 'id' => 'badgedetails');
        $detailstable->data[] = new html_table_row(array(get_string('name') . ":", $badge->name));
        $detailstable->data[] = new html_table_row(array(get_string('description', 'badges') . ":", $badge->description));
        $detailstable->data[] = new html_table_row(array(get_string('visible', 'badges') . ":",
                $badge->visible ? get_string('yes') : get_string('no')));
        $detailstable->data[] = new html_table_row(array(get_string('badgeimage', 'badges') . ":",
                print_badge_image($badge, $context, 'large')));
        $display .= html_writer::table($detailstable);
        $display .= html_writer::end_tag('fieldset');

        // Issuer details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuerdetails','badges'), array('class' => 'bold'));

        $issuertable = new html_table();
        $issuertable->attributes = array('class' => 'clearfix', 'id' => 'badgeissuer');
        $issuertable->data[] = new html_table_row(array(get_string('issuername', 'badges') . ":", $badge->issuername));
        $issuertable->data[] = new html_table_row(array(get_string('issuerurl', 'badges') . ":", $badge->issuerurl));
        $issuertable->data[] = new html_table_row(array(get_string('contact', 'badges') . ":", $badge->issuercontact));
        $display .= html_writer::table($issuertable);
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
                $display .= get_string('numawards', 'badges', count($badge->get_awards()));
            } else {
                $display .= get_string('noawards', 'badges');
            }
            $display .= html_writer::end_tag('fieldset');
        }
// @TODO: table styles.
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

    // Prints action icons for the badge.
    public function print_badge_table_actions($badge, $context) {
        $table = new html_table();
        $table->attributes = array('class' => 'badge-table-actions', 'id' => 'badge-table-actions');

        $actions = array();
        $actions[] = $this->output->pix_icon('t/lock', get_string('activate', 'badges'));
        $actions[] = $this->output->pix_icon('t/enrolusers', get_string('award', 'badges'));

        $actions[] = $this->output->pix_icon('t/hide', get_string('hide'));
        $actions[] = $this->output->pix_icon('t/editstring', get_string('edit'));
        $actions[] = $this->output->pix_icon('t/copy', get_string('copy'));
        $actions[] = $this->output->pix_icon('t/delete', get_string('delete'));

        $table->data[] = $actions;
        return html_writer::table($table);
    }

    // Outputs table badges with actions available.
    public function render_badge_collection(badge_collection $badges) {
        global $PAGE;
        $totalcount = count($badges);
        list($extrasql, $params) = $ufiltering->get_sql_filter();
        $table = new flexible_table('badge-table');
        $table->define_columns(array('select', 'image', 'name', 'criteria', 'awards', 'status', 'actions'));
        $headers = array(
                    get_string('select'),
                    get_string('badgeimage', 'badges'),
                    get_string('name'),
                    get_string('bcriteria', 'badges'),
                    get_string('awards', 'badges'),
                    get_string('status', 'badges'),
                    get_string('actions', 'badges')
                );

        $table->define_headers($headers);
        $table->define_baseurl($PAGE->url);
        $table->column_nosort = array('select', 'image', 'criteria', 'actions');
        $table->sortable(true, 'timemodified', SORT_DESC);
        $table->pageable(true);
        $table->initialbars($totalcount > $perpage);
        $table->pagesize($perpage, $totalcount);
        $table->set_attribute('class', 'badge-table boxaligncenter boxwidthwide');
        $table->column_class(1, 'badge-table-image');
        $table->setup();

        if ($totalcount == 0) {
            $table->print_nothing_to_display();
        } else {
            foreach ($badges as $b) {
                $row = array(
                        html_writer::checkbox('badgeid', $b->id, false),
                        print_badge_image($b, $this->page->context),
                        html_writer::link(new moodle_url('/badges/overview.php', array('id' => $b->id)), $b->name),
                        "",
                        html_writer::link(new moodle_url('/badges/awards.php', array('id' => $b->id)), count($b->get_awards())),
                        $b->statstring,
                        self::print_badge_table_actions($b, $this->page->context)
                        );
                $table->add_data($row);
            }
        }

        $table->finish_output();
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

    // Renders a table with users who have earned the badge.
    // Based on stamps collection plugin.
    protected function render_badge_recipients(badge_recipients $recipients) {
        if (empty($recipients->userids)) {
            return $this->output->heading(get_string('somestring', 'badges'), 3);
        }

        $htmlpagingbar = $this->render(new paging_bar($recipients->totalcount, $recipients->page, $recipients->perpage, $this->page->url, 'page'));
        $table = new html_table();
        $table->attributes['class'] = 'generaltable generalbox boxaligncenter boxwidthwide';

        $sortbyfirstname = $this->helper_sortable_heading(get_string('firstname'),
                'firstname', $recipients->sort, $recipients->dir);
        $sortbylastname = $this->helper_sortable_heading(get_string('lastname'),
                'lastname', $recipients->sort, $recipients->dir);
        if ($this->helper_fullname_format() == 'lf') {
            $sortbyname = $sortbylastname . ' / ' . $sortbyfirstname;
        } else {
            $sortbyname = $sortbyfirstname . ' / ' . $sortbylastname;
        }

        $sortbydate = $this->helper_sortable_heading(get_string('dateawarded', 'badges'),
                'dateissued', $recipients->sort, $recipients->dir);

        $table->head = array($sortbyname, $sortbydate, '');

        $table->colclasses = array('fullname', 'dateissued', 'badgeurl');

        foreach ($recipients->userids as $holder) {
            $fullname = fullname($holder);
            $fullname = html_writer::link(
                            new moodle_url('/user/profile.php', array('id' => $holder->userid)),
                            $fullname
                        );
            $awarded  = userdate($holder->dateissued);
            $badgeurl = html_writer::link(
                            new moodle_url('/badges/badge.php', array('hash' => $holder->uniquehash)),
                            get_string('viewbadge', 'badges')
                        );

            $row = array($fullname, $awarded, $badgeurl);
            $table->data[] = $row;
        }

        $htmltable       = html_writer::table($table);
        $htmlpreferences = $this->helper_preferences_form($recipients->perpage);

        return $htmlpagingbar . $htmltable . $htmlpagingbar . $htmlpreferences;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Helper methods
    // Reused from stamps collection plugin
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a form to set the view preferences
     *
     * @param int $perpage current value of users per page setting
     * @return string HTML
     */
    protected function helper_preferences_form($perpage) {
        global $CFG;
        require_once($CFG->libdir . '/formslib.php');

        $mform = new MoodleQuickForm('preferences',
                                    'post',
                                    $this->page->url,
                                    '',
                                    array('class' => 'preferences boxaligncenter boxwidthwide'));

        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->addElement('hidden', 'updatepref', 1);

        $mform->addElement('header', 'qgprefs', get_string('preferences'));

        $mform->addElement('text', 'perpage', get_string('perpage', 'badges'), array('size' => 2));
        $mform->setDefault('perpage', $perpage);

        $mform->addElement('submit', 'savepreferences', get_string('savepreferences'));

        ob_start();
        $mform->display();
        $out = ob_get_clean();

        return $out;
    }
    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid=null, $sortby=null, $sorthow=null) {

        $out = html_writer::tag('span', $text, array('class'=>'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid or $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/up', get_string('sortbyx', 'core', s($text)), null, array('class' => 'sort asc')));
            }
            if ($sortby !== $sortid or $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/down', get_string('sortbyxreverse', 'core', s($text)), null, array('class' => 'sort desc')));
            }
        }
        return $out;
    }

    /**
     * Tries to guess the fullname format set at the site
     *
     * @return string fl|lf
     */
    protected function helper_fullname_format() {
        $fake = new stdClass();
        $fake->lastname = 'LLLL';
        $fake->firstname = 'FFFF';
        $fullname = get_string('fullnamedisplay', '', $fake);
        if (strpos($fullname, 'LLLL') < strpos($fullname, 'FFFF')) {
            return 'lf';
        } else {
            return 'fl';
        }
    }
}

/**
 * Badge recipients rendering class
 */
class badge_recipients implements renderable {
    /** @var string how are the data sorted */
    public $sort = 'lastname';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of badge recipients to display per page */
    public $perpage = 30;

    /** @var int the total number or badge recipients to display */
    public $totalcount = null;

    /** @var array internal list of  badge recipients ids */
    public $userids = array();
    /**
     * Initializes the list of users to display
     *
     * @param array $holders List of badge holders
     */
    public function __construct($holders) {
        $this->userids = $holders;
    }
}

/**
 * Collection of all badges for view.php page
 */
class badge_collection implements renderable {

    /** @var string how are the data sorted */
    public $sortedby = 'name';

    /** @var string how are the data sorted */
    public $sortedhow = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of stamo holders to display per page */
    public $perpage = 30;

    /** @var int the total number or stamp holders to display */
    public $totalcount = null;

    /** @var int list of badges */
    public $badges = array();

    /**
     * Initializes the list of badges to display
     *
     * @param array $badges Badges to render
     * @param int $courseid Course id
     */
    public function __construct(array $badges = array()) {
        $this->badges = $badges;
    }
}

/**
 * List of badges used at the index.php page
 */
class badge_management extends badge_collection implements renderable {
}