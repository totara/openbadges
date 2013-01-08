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

    // Outputs badges list.
    public function print_badges_list($badges, $userid, $profile = false, $external = false) {
        global $USER, $CFG;
        foreach ($badges as $b) {
            if (!$external) {
                $context = ($b->context == BADGE_TYPE_SITE) ? context_system::instance() : context_course::instance($b->courseid);
                $bname = $b->name;
                $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $b->id, '/', 'f1', false);
            } else {
                $bname = $b->assertion->badge->name;
                $imageurl = $b->imageUrl;
            }
            $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'badge-image'));
            $name = html_writer::tag('span', $bname, array('class' => 'badge-name'));
            $checkbox = $status = "";

            if (($userid == $USER->id) && !$profile) {
                $checkbox = html_writer::checkbox('badges[]', $b->uniquehash, false, '', array('class' => 'badge-select'));
                if ($b->public) {
                    $status = $this->output->pix_icon('t/hide', get_string('visible', 'badges')) . " ";
                } else {
                    $status = $this->output->pix_icon('t/show', get_string('hidden', 'badges')) . " ";
                }
            }

            if (!$profile) {
                $url = new moodle_url('badge.php', array('hash' => $b->uniquehash));
            } else {
                if (!$external) {
                    $url = new moodle_url($CFG->wwwroot . '/badges/badge.php', array('hash' => $b->uniquehash));
                } else {
                    $url = new moodle_url($CFG->wwwroot . '/badges/external.php', array('badge' => serialize($b)));
                }
            }
            $items[] = $checkbox .
                        html_writer::link(
                            $url,
                            $image . $status. $name,
                            array('class' => 'badge', 'title' => $bname)
                        );
        }

        return html_writer::alist($items, array('class' => 'badges'));
    }

    // Recipients selection form.
    public function recipients_selection_form(user_selector_base $existinguc, user_selector_base $potentialuc) {
        $output = '';
        $formattributes = array();
        $formattributes['id'] = 'recipientform';
        $formattributes['action'] = '';
        $formattributes['method'] = 'post';
        $output .= html_writer::start_tag('form', $formattributes);
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        $existingcell = new html_table_cell();
        $existingcell->text = $existinguc->display(true);
        $existingcell->attributes['class'] = 'existing';
        $actioncell = new html_table_cell();
        $actioncell->text  = html_writer::start_tag('div', array());
        $actioncell->text .= html_writer::empty_tag('input', array(
                    'type' => 'submit',
                    'name' => 'award',
                    'value' => $this->output->larrow() . ' ' . get_string('award', 'badges'),
                    'class' => 'actionbutton')
                );
        $actioncell->text .= html_writer::end_tag('div', array());
        $actioncell->attributes['class'] = 'actions';
        $potentialcell = new html_table_cell();
        $potentialcell->text = $potentialuc->display(true);
        $potentialcell->attributes['class'] = 'potential';

        $table = new html_table();
        $table->attributes['class'] = 'recipienttable boxaligncenter';
        $table->data = array(new html_table_row(array($existingcell, $actioncell, $potentialcell)));
        $output .= html_writer::table($table);

        $output .= html_writer::end_tag('form');
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
        $display .= html_writer::tag('legend', get_string('badgedetails', 'badges'), array('class' => 'bold'));

        $detailstable = new html_table();
        $detailstable->attributes = array('class' => 'clearfix', 'id' => 'badgedetails');
        $detailstable->data[] = array(get_string('name') . ":", $badge->name);
        $detailstable->data[] = array(get_string('description', 'badges') . ":", $badge->description);
        $detailstable->data[] = array(get_string('visible', 'badges') . ":",
                $badge->visible ? get_string('yes') : get_string('no'));
        $detailstable->data[] = array(get_string('badgeimage', 'badges') . ":",
                print_badge_image($badge, $context, 'large'));
        $display .= html_writer::table($detailstable);
        $display .= html_writer::end_tag('fieldset');

        // Issuer details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuerdetails', 'badges'), array('class' => 'bold'));

        $issuertable = new html_table();
        $issuertable->attributes = array('class' => 'clearfix', 'id' => 'badgeissuer');
        $issuertable->data[] = array(get_string('issuername', 'badges') . ":", $badge->issuername);
        $issuertable->data[] = array(get_string('issuerurl', 'badges') . ":",
                html_writer::tag('a', $badge->issuerurl, array('href' => $badge->issuerurl)));
        $issuertable->data[] = array(get_string('contact', 'badges') . ":",
                html_writer::tag('a', $badge->issuercontact, array('href' => 'mailto:' . $badge->issuercontact)));
        $display .= html_writer::table($issuertable);
        $display .= html_writer::end_tag('fieldset');

        // Issuance details if any.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuancedetails', 'badges'), array('class' => 'bold'));
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
        $display .= html_writer::tag('legend', get_string('bcriteria', 'badges'), array('class' => 'bold'));
        if ($badge->has_criteria()) {
            $display .= self::print_badge_criteria($badge);
        } else {
            $display .= get_string('nocriteria', 'badges');
        }
        $display .= html_writer::end_tag('fieldset');

        // Awards details if any.
        if (has_capability('moodle/badges:viewawarded', $context)) {
            $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
            $display .= html_writer::tag('legend', get_string('awards', 'badges'), array('class' => 'bold'));
            if ($badge->has_awards()) {
                $display .= get_string('numawards', 'badges', count($badge->get_awards()));
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
            if ($badge->is_active()) {
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
        $actions = "";

        if (has_capability('moodle/badges:configuredetails', $context)) {
            // Activate/deactivate badge.
            if ($badge->status == BADGE_STATUS_INACTIVE || $badge->status == BADGE_STATUS_INACTIVE_LOCKED) {
                $url = new moodle_url(qualified_me());
                $url->param('activate', $badge->id);
                $actions .= $this->output->action_icon($url, new pix_icon('t/stop', get_string('activate', 'badges'))) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('lock', $badge->id);
                $actions .= $this->output->action_icon($url, new pix_icon('t/go', get_string('deactivate', 'badges'))) . " ";
            }

            // Show/hide badge.
            if (!empty($badge->visible)) {
                $url = new moodle_url(qualified_me());
                $url->param('hide', $badge->id);
                $actions .= $this->output->action_icon($url, new pix_icon('t/hide', get_string('hide'))) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('show', $badge->id);
                $actions .= $this->output->action_icon($url, new pix_icon('t/show', get_string('show'))) . " ";
            }
        }

        // Award badge manually.
        if ($badge->has_manual_award_criteria() &&
                has_capability('moodle/badges:awardbadge', $context) &&
                $badge->is_active()) {
            $url = new moodle_url('/badges/award.php', array('id' => $badge->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/enrolusers', get_string('award', 'badges'))) . " ";
        }

        // Edit badge.
        if (has_capability('moodle/badges:configuredetails', $context)) {
            $url = new moodle_url('/badges/edit.php', array('id' => $badge->id, 'action' => 'details'));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Duplicate badge.
        if (has_capability('moodle/badges:createbadge', $context)) {
            $url = new moodle_url('/badges/action.php', array('copy' => '1', 'id' => $badge->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/copy', get_string('copy'))) . " ";
        }

        // Delete badge.
        if (has_capability('moodle/badges:deletebadge', $context)) {
            $url = new moodle_url(qualified_me());
            $url->param('delete', $badge->id);
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }

        return $actions;
    }

    // Outputs issued badge with actions available.
    protected function render_issued_badge(issued_badge $ibadge) {
        global $USER, $CFG;
        $issued = $ibadge->issued;
        $badge = new badge($ibadge->badgeid);

        if ($ibadge->visible
            || ($USER->id == $ibadge->recipient)
            || has_capability('moodle/badges:viewawarded', context_system::instance())) {
            $table = new html_table();

            $imagetable = new html_table();
            $imagetable->attributes = array('class' => 'clearfix badgeissuedimage');
            $imagetable->data[] = array(html_writer::empty_tag('img', array('src' => $issued['badge']['image'])));
            if ($USER->id == $ibadge->recipient) {
                $imagetable->data[] = array($this->output->single_button(
                            new moodle_url('/badges/badge.php', array('hash' => $ibadge->hash, 'bake' => true)),
                            get_string('download'),
                            'POST'));
                if ($CFG->badges_allowexternalbackpack) {
                    $assertion = new moodle_url('/badges/assertion.php', array('b' => $ibadge->hash));
                    $attributes = array(
                            'type' => 'button',
                            'value' => get_string('addtobackpack', 'badges'),
                            'onclick' => 'OpenBadges.issue(["' . $assertion->out(false) . '"], function(errors, successes) { })');
                    $tobackpack = html_writer::tag('input', '', $attributes);
                    $imagetable->data[] = array($tobackpack);
                }
            }
            $datatable = new html_table();
            $datatable->attributes = array('class' => 'badgeissuedinfo');
            $datatable->colclasses = array('bfield', 'bvalue');
            $datatable->data[] = array($this->output->heading(get_string('issuerdetails', 'badges'), 3), '');
            $datatable->data[] = array(get_string('issuername', 'badges'), $badge->issuername);
            $datatable->data[] = array(get_string('issuerurl', 'badges'),
                    html_writer::tag('a', $badge->issuerurl, array('href' => $badge->issuerurl)));
            $datatable->data[] = array(get_string('contact', 'badges'),
                    html_writer::tag('a', $badge->issuercontact, array('href' => 'mailto:' . $badge->issuercontact)));
            $datatable->data[] = array($this->output->heading(get_string('badgedetails', 'badges'), 3), '');
            $datatable->data[] = array(get_string('name'), $badge->name);
            $datatable->data[] = array(get_string('description', 'badges'), $badge->description);
            $datatable->data[] = array(get_string('bcriteria', 'badges'), self::print_badge_criteria($badge));
            $datatable->data[] = array($this->output->heading(get_string('issuancedetails', 'badges'), 3), '');
            $datatable->data[] = array(get_string('dateawarded', 'badges'), $issued['issued_on']);
            if (isset($issued['expires'])) {
                $datatable->data[] = array(get_string('expirydate', 'badges'), $issued['expires']);
            }
            // $datatable->data[] = array(get_string('evidence', 'badges'), 'TODO'); // @TODO: print completed criteria.
            $table->attributes = array('class' => 'generalbox boxaligncenter issuedbadgebox');
            $table->data[] = array(html_writer::table($imagetable), html_writer::table($datatable));
            $htmlbadge = html_writer::table($table);

            return $htmlbadge;
        } else {
            return get_string('hiddenbadge', 'badges');
        }
    }

    // Outputs external badge.
    protected function render_external_badge(external_badge $ibadge) {
        $issued = $ibadge->issued;
        $assertion = $issued->assertion;
        $issuer = $assertion->badge->issuer;
        $table = new html_table();

        $imagetable = new html_table();
        $imagetable->attributes = array('class' => 'clearfix badgeissuedimage');
        $imagetable->data[] = array(html_writer::empty_tag('img', array('src' => $issued->imageUrl, 'width' => '100px')));

        $datatable = new html_table();
        $datatable->attributes = array('class' => 'badgeissuedinfo');
        $datatable->colclasses = array('bfield', 'bvalue');
        $datatable->data[] = array($this->output->heading(get_string('issuerdetails', 'badges'), 3), '');
        $datatable->data[] = array(get_string('issuername', 'badges'), $issuer->name);
        $datatable->data[] = array(get_string('issuerurl', 'badges'),
                html_writer::tag('a', $issuer->origin, array('href' => $issuer->origin)));
        if (isset($issuer->contact)) {
            $datatable->data[] = array(get_string('contact', 'badges'),
                html_writer::tag('a', $issuer->contact, array('href' => 'mailto:' . $issuer->contact)));
        }
        $datatable->data[] = array($this->output->heading(get_string('badgedetails', 'badges'), 3), '');
        $datatable->data[] = array(get_string('name'), $assertion->badge->name);
        $datatable->data[] = array(get_string('description', 'badges'), $assertion->badge->description);
        $datatable->data[] = array(get_string('bcriteria', 'badges'),
                html_writer::tag('a', $assertion->badge->criteria, array('href' => $assertion->badge->criteria)));
        $datatable->data[] = array($this->output->heading(get_string('issuancedetails', 'badges'), 3), '');
        if (isset($assertion->issued_on)) {
            $datatable->data[] = array(get_string('dateawarded', 'badges'), $assertion->issued_on);
        }
        if (isset($assertion->badge->expire)) {
            $datatable->data[] = array(get_string('expirydate', 'badges'), $assertion->badge->expire);
        }
        if (isset($assertion->evidence)) {
            $datatable->data[] = array(get_string('evidence', 'badges'),
                html_writer::tag('a', $assertion->evidence, array('href' => $assertion->evidence)));
        }
        $table->attributes = array('class' => 'generalbox boxaligncenter issuedbadgebox');
        $table->data[] = array(html_writer::table($imagetable), html_writer::table($datatable));
        $htmlbadge = html_writer::table($table);

        return $htmlbadge;
    }

    // Outputs table of user badges.
    protected function render_badge_user_collection(badge_user_collection $badges) {
        global $CFG, $USER;
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);

        $searchform = $this->helper_search_form($badges->search);
        // Local badges.
        $localhtml = html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $localhtml .= html_writer::tag('legend', $this->output->heading_with_help(get_string('localbadges', 'badges'), 'localbadges', 'badges'));
        $localhtml .= html_writer::tag('div', get_string('badgesearned', 'badges', $badges->totalcount));

        $htmllist = $this->print_badges_list($badges->badges, $USER->id);

        $htmlactions  = $this->bulk_user_action_form();
        $attributes = array(
                'id'     => 'bulkaction',
                'action' => $this->page->url,
                'method' => 'post',
                'class'  => 'boxaligncenter'
        );
        $htmlform = html_writer::tag('form', $htmllist . $htmlactions, $attributes);
        $localhtml .= $searchform . $htmlpagingbar . $htmlform . $htmlpagingbar;
        $localhtml .= html_writer::end_tag('fieldset');

        // External badges.
        $backpack = $badges->backpack;
        $externalhtml = "";
        if ($CFG->badges_allowexternalbackpack) {
            $externalhtml .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
            $externalhtml .= html_writer::tag('legend', $this->output->heading_with_help(get_string('externalbadges', 'badges'), 'externalbadges', 'badges'));
            if (!is_null($backpack)) {
                if ($backpack->totalbadges > 0) {
                    $externalhtml .= get_string('backpackbadges', 'badges', $backpack);
                } else {
                    $externalhtml .= get_string('nobackpackbadges', 'badges', $backpack);
                }
                $label = get_string('editsettings', 'badges');
                $externalhtml .= $this->output->single_button(
                        new moodle_url('mybackpack.php', array('clear' => true)),
                        get_string('clearsettings', 'badges'),
                        'POST',
                        array('class' => 'backpackform'));
            } else {
                $externalhtml .= get_string('nobackpack', 'badges');
                $label = get_string('setup', 'badges');
            }
            $externalhtml .= $this->output->single_button('mybackpack.php', $label, 'POST', array('class' => 'backpackform'));
            $externalhtml .= html_writer::end_tag('fieldset');
        }

        return $localhtml . $externalhtml;
    }

    // Outputs table of available badges.
    protected function render_badge_collection(badge_collection $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection boxaligncenter boxwidthwide';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $badges->sort, $badges->dir);
        $sortbyawarded = $this->helper_sortable_heading(get_string('awards', 'badges'),
                'dateissued', $badges->sort, $badges->dir);
        $table->head = array(
                    get_string('badgeimage', 'badges'),
                    $sortbyname,
                    get_string('description', 'badges'),
                    get_string('bcriteria', 'badges'),
                    $sortbyawarded
                );
        $table->colclasses = array('badgeimage', 'name', 'description', 'criteria', 'awards');

        foreach ($badges->badges as $badge) {
            $badgeimage = print_badge_image($badge, $this->page->context, 'large');
            $name = $badge->name;
            $description = $badge->description;
            $criteria = self::print_badge_criteria($badge);
            if ($badge->dateissued) {
                $icon = new pix_icon('i/tick_green_big',
                            get_string('dateearned', 'badges', userdate($badge->dateissued)));
                $badgeurl = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
                $awarded = $this->output->action_icon($badgeurl, $icon);
            } else {
                $awarded = "";
            }
            $row = array($badgeimage, $name, $description, $criteria, $awarded);
            $table->data[] = $row;
        }

        $htmltable       = html_writer::table($table);
        $htmlpreferences = $this->helper_preferences_form($badges->perpage);

        return $htmlpagingbar . $htmltable . $htmlpagingbar . $htmlpreferences;
    }

    // Outputs table of badges with actions available.
    protected function render_badge_management(badge_management $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $badges->sort, $badges->dir);
        $sortbystatus = $this->helper_sortable_heading(get_string('status', 'badges'),
                'status', $badges->sort, $badges->dir);
        $table->head = array(
                get_string('select'),
                get_string('badgeimage', 'badges'),
                $sortbyname,
                $sortbystatus,
                get_string('bcriteria', 'badges'),
                get_string('awards', 'badges'),
                get_string('actions')
            );
        $table->colclasses = array('select', 'badgeimage', 'name', 'status', 'criteria', 'awards', 'actions');

        foreach ($badges->badges as $b) {
            $select = html_writer::checkbox('badges[]', $b->id, false, null, array('class' => 'badgecheckbox'));
            $badgeimage = print_badge_image($b, $this->page->context);

            $name = html_writer::link(new moodle_url('/badges/overview.php', array('id' => $b->id)), $b->name);
            $status = $b->statstring;
            $criteria = self::print_badge_criteria($b);

            if (has_capability('moodle/badges:viewawarded', $this->page->context)) {
                $awards = html_writer::link(new moodle_url('/badges/recipients.php', array('id' => $b->id)), $b->awards);
            } else {
                $awards = $b->awards;
            }

            $actions = self::print_badge_table_actions($b, $this->page->context);

            $row = array($select, $badgeimage, $name, $status, $criteria, $awards, $actions);
            $table->data[] = $row;
        }

        $htmltable       = html_writer::table($table);
        $htmlactions     = $this->bulk_action_form();
        $htmlpreferences = $this->helper_preferences_form($badges->perpage);

        $attributes = array(
                'id'     => 'bulkaction',
                'action' => 'bulk_action.php',
                'method' => 'post',
                'class'  => 'mform boxaligncenter boxwidthwide'
                );
        $htmlform = html_writer::tag('form', $htmlactions . $htmltable, $attributes);

        return $htmlpagingbar . $htmlform . $htmlpagingbar . $htmlpreferences;
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
                        new moodle_url('/badges/criteria.php', array('id' => $badgeid)),
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
                        new moodle_url('/badges/recipients.php', array('id' => $badgeid)),
                        get_string('bawards', 'badges', $awarded)
                    );
        }

        $tabs[] = $row;

        print_tabs($tabs, $current);
    }

    // Prints badge criteria.
    public function print_badge_criteria(badge $badge) {
        $output = "";
        $agg = $badge->get_aggregation_methods();
        if (empty($badge->criteria)) {
            return get_string('nocriteria', 'badges');
        } else if (count($badge->criteria) == 2) {
            $output .= get_string('criteria_descr', 'badges');
        } else {
            $output .= get_string('criteria_descr_' . BADGE_CRITERIA_TYPE_OVERALL, 'badges', strtoupper($agg[$badge->get_aggregation_method()]));
        }
        $items = array();
        unset($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]);
        foreach ($badge->criteria as $type => $c) {
            if (count($c->params) == 1) {
                $items[] .= get_string('criteria_descr_single_' . $type , 'badges', strtoupper($agg[$badge->get_aggregation_method($type)])) . $c->get_details();
            } else {
                $items[] .= get_string('criteria_descr_' . $type , 'badges', strtoupper($agg[$badge->get_aggregation_method($type)])) . $c->get_details();
            }
        }
        $output .= html_writer::alist($items, array(), 'ul');
        return $output;
    }

    // Prints criteria actions for badge editing.
    public function print_criteria_actions(badge $badge) {
        $table = new html_table();
        $table->attributes = array('class' => 'clearfix', 'id' => 'badgeactions');

        $actions = array();
        if (!$badge->is_active() && !$badge->is_locked()) {
            // Clear all criteria button.
            if ($badge->has_criteria()) {
                $actions[] = get_string('clear', 'badges');
                $actions[] = $this->output->single_button(new moodle_url('/badges/action.php', array('id' => $badge->id, 'clear' => 1)), get_string('clear'));
            }
            $table->data[] = $actions;
            $actions = array();

            // Add criteria button.
            $accepted = $badge->get_accepted_criteria();
            $potential = array_diff($accepted, array_keys($badge->criteria));

            if (!empty($potential)) {
                foreach ($potential as $p) {
                    if ($p != 0) {
                        $select[$p] = get_string('criteria_' . $p, 'badges');
                    }
                }
                $actions[] = get_string('addcriteria', 'badges');
                $actions[] = $this->output->single_select(
                        new moodle_url('/badges/criteria_action.php', array('badgeid' => $badge->id, 'add' => true)),
                        'type',
                        $select,
                        false,
                        array('value' => get_string('addcriteria', 'badges')));
            } else {
                $actions[] = $this->output->box(get_string('nothingtoadd', 'badges'), 'clearfix');
            }
        }

        $table->data[] = $actions;
        return html_writer::table($table);
    }

    // Renders a table with users who have earned the badge.
    // Based on stamps collection plugin.
    protected function render_badge_recipients(badge_recipients $recipients) {
        $paging = new paging_bar($recipients->totalcount, $recipients->page, $recipients->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
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
    protected function helper_sortable_heading($text, $sortid = null, $sortby = null, $sorthow = null) {
        $out = html_writer::tag('span', $text, array('class' => 'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid || $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/up', get_string('sortbyx', 'core', s($text)), null, array('class' => 'sort asc')));
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
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
    /**
     * Renders a form for bulk actions with badges
     *
     * @return string HTML
     */
    protected function bulk_action_form() {
        $actions = array();
        if (has_capability('moodle/badges:configuredetails', $this->page->context)) {
            $actions['hide'] = get_string('hide');
        }
        if (has_capability('moodle/badges:configuredetails', $this->page->context)) {
            $actions['show'] = get_string('makevisible', 'badges');
        }
        if (has_capability('moodle/badges:deletebadge', $this->page->context)) {
            $actions['delete'] = get_string('delete');
        }

        $output = html_writer::tag('fieldset',
                html_writer::label(get_string('selecting', 'badges'), 'menuaction') .
                html_writer::select($actions, 'action', null, array('' => 'choosedots')) .
                html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go'))) .
                html_writer::empty_tag('input', array('type' => 'hidden', 'value' => sesskey(), 'name' => 'sesskey')) .
                html_writer::empty_tag('input', array('type' => 'hidden', 'value' => qualified_me(), 'name' => 'returnto')),
                array('class' => 'boxaligncenter boxwidthwide'));

        return $output;
    }
    /**
     * Renders a form for bulk actions with badges
     *
     * @return string HTML
     */
    protected function bulk_user_action_form() {
        $actions = array();

        if (has_capability('moodle/badges:manageownbadges', $this->page->context)) {
            $actions['hide'] = get_string('hide');
            $actions['show'] = get_string('makepublic', 'badges');
            $actions['download'] = get_string('download');
        }

        $output = html_writer::tag('fieldset',
                html_writer::label(get_string('selecting', 'badges'), 'menuaction') .
                html_writer::select($actions, 'action', null, array('' => 'choosedots')) .
                html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go'))) .
                html_writer::empty_tag('input', array('type' => 'hidden', 'value' => sesskey(), 'name' => 'sesskey')) .
                html_writer::empty_tag('input', array('type' => 'hidden', 'value' => qualified_me(), 'name' => 'returnto')),
                array('class' => 'boxaligncenter'));

        return $output;
    }
    /**
     * Renders a search form
     *
     * @param string $search Search string
     * @return string HTML
     */
    protected function helper_search_form($search) {
        global $CFG;
        require_once($CFG->libdir . '/formslib.php');

        $mform = new MoodleQuickForm('searchform',
                'post',
                $this->page->url,
                '',
                array('class' => 'boxaligncenter'));

        $mform->addElement('hidden', 'sesskey', sesskey());

        $el[] = $mform->createElement('text', 'search', get_string('search'), array('size' => 20));
        $mform->setDefault('search', $search);
        $el[] = $mform->createElement('submit', 'submitsearch', get_string('search'));
        $el[] = $mform->createElement('submit', 'clearsearch', get_string('clear'));
        $mform->addGroup($el, 'searchgroup', get_string('searchname', 'badges'), " ", false);

        ob_start();
        $mform->display();
        $out = ob_get_clean();

        return $out;
    }
}

/**
 * An issued badges for badge.php page
 */
class issued_badge implements renderable {
    /** @var issued badge */
    public $issued;

    /** @var badge recipient */
    public $recipient = 0;

    /** @var badge visibility to others */
    public $visible = 0;

    /** @var badge class */
    public $badgeid = 0;

    /** @var issued badge unique hash */
    public $hash = "";

    /**
     * Initializes the badge to display
     *
     * @param string $hash Issued badge hash
     */
    public function __construct($hash) {
        global $DB;
        $this->issued = get_issued_badge_info($hash);
        $this->hash = $hash;

        $rec = $DB->get_record_select('badge_issued', $DB->sql_compare_text('uniquehash') . ' = ? ', array($hash), 'userid, visible, badgeid');
        if ($rec) {
            $this->recipient = $rec->userid;
            $this->visible = $rec->visible;
            $this->badgeid = $rec->badgeid;
        }
    }
}

/**
 * An external badges for external.php page
 */
class external_badge implements renderable {
    /** @var issued badge */
    public $issued;

    /**
     * Initializes the badge to display
     *
     * @param string $json External badge information.
     */
    public function __construct($json) {
        $this->issued = $json;
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
    public $sort = 'name';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of badges to display per page */
    public $perpage = 30;

    /** @var int the total number of badges to display */
    public $totalcount = null;

    /** @var array list of badges */
    public $badges = array();

    /**
     * Initializes the list of badges to display
     *
     * @param array $badges Badges to render
     */
    public function __construct($badges) {
        $this->badges = $badges;
    }
}

/**
 * Collection of badges used at the index.php page
 */
class badge_management extends badge_collection implements renderable {
}

/**
 * Collection of user badges used at the mybadges.php page
 */
class badge_user_collection extends badge_collection implements renderable {
    /** @var array backpack settings */
    public $backpack;

    /** @var string search */
    public $search = '';

    /**
     * Initializes user badge collection.
     *
     * @param array $badges Badges to render
     * @param int $userid Badges owner
     */
    public function __construct($badges, $userid) {
        parent::__construct($badges);
        $this->backpack = get_backpack_settings($userid);
    }
}