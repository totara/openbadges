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
 * Managing tags, tag areas and tags collections
 *
 * @package    core_tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

define('SHOW_ALL_PAGE_SIZE', 50000);
define('DEFAULT_PAGE_SIZE', 30);

$tagschecked = optional_param_array('tagschecked', array(), PARAM_INT);
$tagid       = optional_param('tagid', null, PARAM_INT);
$isstandard  = optional_param('isstandard', null, PARAM_INT);
$action      = optional_param('action', '', PARAM_ALPHA);
$perpage     = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
$page        = optional_param('page', 0, PARAM_INT);
$notice      = optional_param('notice', '', PARAM_ALPHA);
$tagcollid   = optional_param('tc', 0, PARAM_INT);
$tagareaid   = optional_param('ta', null, PARAM_INT);

$params = array();
if ($perpage != DEFAULT_PAGE_SIZE) {
    $params['perpage'] = $perpage;
}
if ($page > 0) {
    $params['page'] = $page;
}

admin_externalpage_setup('managetags', '', $params, '', array('pagelayout' => 'report'));

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$tagobject = null;
if ($tagid) {
    $tagobject = core_tag_tag::get($tagid, '*', MUST_EXIST);
    $tagcollid = $tagobject->tagcollid;
}
$tagcoll = core_tag_collection::get_by_id($tagcollid);
$tagarea = core_tag_area::get_by_id($tagareaid);
$manageurl = new moodle_url('/tag/manage.php');
if ($tagcoll) {
    // We are inside a tag collection - add it to the page url and the breadcrumb.
    $PAGE->set_url(new moodle_url($PAGE->url, array('tc' => $tagcoll->id)));
    $PAGE->navbar->add(core_tag_collection::display_name($tagcoll),
            new moodle_url($manageurl, array('tc' => $tagcoll->id)));
}

$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');

switch($action) {

    case 'colladd':
    case 'colledit':
        if ($action === 'colladd' || ($action === 'colledit' && $tagcoll && empty($tagcoll->component))) {
            $form = new core_tag_collection_form($manageurl, $tagcoll);
            if ($form->is_cancelled()) {
                redirect($manageurl);
            } else if ($data = $form->get_data()) {
                if ($action === 'colladd') {
                    core_tag_collection::create($data);
                } else {
                    core_tag_collection::update($tagcoll, $data);
                }
                redirect($manageurl);
            } else {
                $title = ($action === 'colladd') ?
                        get_string('addtagcoll', 'tag') :
                        get_string('edittagcoll', 'tag', core_tag_collection::display_name($tagcoll));
                $PAGE->navbar->add($title);
                echo $OUTPUT->header();
                echo $OUTPUT->heading($title, 2);
                $form->display();
                echo $OUTPUT->footer();
                exit;
            }
        }
        break;

    case 'colldelete':
        $confirm = optional_param('confirm', false, PARAM_BOOL);
        if (!$confirm) {
            echo $OUTPUT->header();
            $strconfirm = get_string('suredeletecoll', 'tag', core_tag_collection::display_name($tagcoll));
            $params = array('tc' => $tagcoll->id, 'confirm' => 1, 'sesskey' => sesskey(), 'action' => 'colldelete');
            $formcontinue = new single_button(new moodle_url($manageurl, $params), get_string('yes'));
            $formcancel = new single_button($manageurl, get_string('no'), 'get');
            echo $OUTPUT->confirm($strconfirm, $formcontinue, $formcancel);
            echo $OUTPUT->footer();
            die;
        }
        if ($tagcoll && !$tagcoll->component) {
            require_sesskey();
            core_tag_collection::delete($tagcoll);
            redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
        }
        redirect($manageurl);
        break;

    case 'collmoveup':
        if ($tagcoll) {
            require_sesskey();
            core_tag_collection::change_sortorder($tagcoll, -1);
            redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
        }
        redirect($manageurl);
        break;

    case 'collmovedown':
        if ($tagcoll) {
            require_sesskey();
            core_tag_collection::change_sortorder($tagcoll, 1);
            redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
        }
        redirect($manageurl);
        break;

    case 'areaenable':
    case 'areadisable':
        if ($tagarea) {
            require_sesskey();
            $data = array('enabled' => ($action === 'areaenable') ? 1 : 0);
            core_tag_area::update($tagarea, $data);
            redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
        }
        redirect($manageurl);
        break;

    case 'areasetcoll':
        if ($tagarea) {
            require_sesskey();
            if ($newtagcollid = optional_param('areacollid', null, PARAM_INT)) {
                core_tag_area::update($tagarea, array('tagcollid' => $newtagcollid));
                redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
            }
        }
        redirect($manageurl);
        break;

    case 'areasetshowstandard':
        if ($tagarea) {
            require_sesskey();
            if (($showstandard = optional_param('showstandard', null, PARAM_INT)) !== null) {
                core_tag_area::update($tagarea, array('showstandard' => $showstandard));
                redirect(new moodle_url($manageurl, array('notice' => 'changessaved')));
            }
        }
        redirect($manageurl);
        break;

    case 'delete':
        require_sesskey();
        if (!$tagschecked && $tagid) {
            $tagschecked = array($tagid);
        }
        core_tag_tag::delete_tags($tagschecked);
        redirect(new moodle_url($PAGE->url, $tagschecked ? array('notice' => 'deleted') : null));
        break;

    case 'setflag':
        require_sesskey();
        if ($tagid) {
            $tagobject->flag();
            redirect(new moodle_url($PAGE->url, array('notice' => 'flagged')));
        }
        redirect($PAGE->url);
        break;

    case 'resetflag':
        require_sesskey();
        if ($tagid) {
            $tagobject->reset_flag();
            redirect(new moodle_url($PAGE->url, array('notice' => 'resetflag')));
        }
        redirect($PAGE->url);
        break;

    case 'changetype':
        require_sesskey();
        if ($tagid && $tagobject->update(
                array('isstandard' => $isstandard ? 1 : 0))) {
            redirect(new moodle_url($PAGE->url, array('notice' => 'typechanged')));
        }
        redirect($PAGE->url);
        break;

    case 'addstandardtag':
        require_sesskey();
        $tagobjects = null;
        if ($tagcoll) {
            $otagsadd = optional_param('otagsadd', '', PARAM_RAW);
            $newtags = preg_split('/\s*,\s*/', trim($otagsadd), -1, PREG_SPLIT_NO_EMPTY);
            $tagobjects = core_tag_tag::create_if_missing($tagcoll->id, $newtags, true);
        }
        foreach ($tagobjects as $tagobject) {
            if (!$tagobject->isstandard) {
                $tagobject->update(array('isstandard' => 1));
            }
        }
        redirect(new moodle_url($PAGE->url, $tagobjects ? array('notice' => 'added') : null));
        break;
}

echo $OUTPUT->header();

if ($notice && get_string_manager()->string_exists($notice, 'tag')) {
    echo $OUTPUT->notification(get_string($notice, 'tag'), 'notifysuccess');
}

if (!$tagcoll) {
    // Tag collection is not specified. Display the overview of tag collections and tag areas.
    $tagareastable = new core_tag_areas_table($manageurl);
    $colltable = new core_tag_collections_table($manageurl);

    echo $OUTPUT->heading(get_string('tagcollections', 'core_tag') . $OUTPUT->help_icon('tagcollection', 'tag'), 3);
    echo html_writer::table($colltable);
    $url = new moodle_url($manageurl, array('action' => 'colladd'));
    echo html_writer::div(html_writer::link($url, get_string('addtagcoll', 'tag')), 'mdl-right addtagcoll');

    echo $OUTPUT->heading(get_string('tagareas', 'core_tag'), 3);
    echo html_writer::table($tagareastable);

    echo $OUTPUT->footer();
    exit;
}

// Tag collection is specified. Manage tags in this collection.
echo $OUTPUT->heading(core_tag_collection::display_name($tagcoll));

// Small form to add an standard tag.
print('<form class="tag-addtags-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">');
print('<input type="hidden" name="tc" value="'.$tagcollid.'" />');
print('<input type="hidden" name="action" value="addstandardtag" />');
print('<input type="hidden" name="perpage" value="'.$perpage.'" />');
print('<input type="hidden" name="page" value="'.$page.'" />');
print('<div class="tag-management-form generalbox"><label class="accesshide" for="id_otagsadd">' .
        get_string('addotags', 'tag') .'</label>'.
    '<input name="otagsadd" id="id_otagsadd" type="text" />'.
    '<input type="hidden" name="sesskey" value="'.sesskey().'">'.
    '<input name="addotags" value="'. get_string('addotags', 'tag') .
        '" onclick="skipClientValidation = true;" id="id_addotags" type="submit" />'.
    '</div>');
print('</form>');

$table = new core_tag_manage_table($tagcollid);
echo '<form class="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">';
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'tc', 'value' => $tagcollid));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'delete'));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'perpage', 'value' => $perpage));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'page', 'value' => $page));
echo $table->out($perpage, true);

if ($table->rawdata) {
    echo html_writer::start_tag('p');
    echo html_writer::tag('button', get_string('deleteselected', 'tag'),
            array('id' => 'tag-management-delete', 'type' => 'submit', 'class' => 'tagdeleteselected'));
    echo html_writer::end_tag('p');
}
echo '</form>';

$totalcount = $table->totalcount;
if ($perpage == SHOW_ALL_PAGE_SIZE) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => DEFAULT_PAGE_SIZE, 'page' => 0);
    $url = new moodle_url($PAGE->url, $params);
    echo html_writer::link($url, get_string('showperpage', '', DEFAULT_PAGE_SIZE));
    echo html_writer::end_tag('div');
} else if ($totalcount > 0 and $perpage < $totalcount) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => SHOW_ALL_PAGE_SIZE, 'page' => 0);
    $url = new moodle_url($PAGE->url, $params);
    echo html_writer::link($url, get_string('showall', '', $totalcount));
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
