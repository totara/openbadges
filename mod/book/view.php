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
 * Book view page
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id        = optional_param('id', 0, PARAM_INT);        // Course Module ID
$bid       = optional_param('b', 0, PARAM_INT);         // Book id
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);    // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if ($id) {
    $cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);
} else {
    $book = $DB->get_record('book', array('id'=>$bid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('book', $book->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $id = $cm->id;
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/book:read', $context);

$allowedit  = has_capability('mod/book:edit', $context);
$viewhidden = has_capability('mod/book:viewhiddenchapters', $context);

if ($allowedit) {
    if ($edit != -1 and confirm_sesskey()) {
        $USER->editing = $edit;
    } else {
        if (isset($USER->editing)) {
            $edit = $USER->editing;
        } else {
            $edit = 0;
        }
    }
} else {
    $edit = 0;
}

// read chapters
$chapters = book_preload_chapters($book);

if ($allowedit and !$chapters) {
    redirect('edit.php?cmid='.$cm->id); // No chapters - add new one.
}
// Check chapterid and read chapter data
if ($chapterid == '0') { // Go to first chapter if no given.
    // Trigger course module viewed event.
    book_view($book, null, false, $course, $cm, $context);

    foreach ($chapters as $ch) {
        if ($edit) {
            $chapterid = $ch->id;
            break;
        }
        if (!$ch->hidden) {
            $chapterid = $ch->id;
            break;
        }
    }
}

$courseurl = new moodle_url('/course/view.php', array('id' => $course->id));

// No content in the book.
if (!$chapterid) {
    $PAGE->set_url('/mod/book/view.php', array('id' => $id));
    notice(get_string('nocontent', 'mod_book'), $courseurl->out(false));
}
// Chapter doesnt exist or it is hidden for students
if ((!$chapter = $DB->get_record('book_chapters', array('id' => $chapterid, 'bookid' => $book->id))) or ($chapter->hidden and !$viewhidden)) {
    print_error('errorchapter', 'mod_book', $courseurl);
}

$PAGE->set_url('/mod/book/view.php', array('id'=>$id, 'chapterid'=>$chapterid));


// Unset all page parameters.
unset($id);
unset($bid);
unset($chapterid);

// Read standard strings.
$strbooks = get_string('modulenameplural', 'mod_book');
$strbook  = get_string('modulename', 'mod_book');
$strtoc   = get_string('toc', 'mod_book');

// prepare header
$pagetitle = $book->name . ": " . $chapter->title;
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

book_add_fake_block($chapters, $chapter, $book, $cm, $edit);

// prepare chapter navigation icons
$previd = null;
$prevtitle = null;
$nextid = null;
$nexttitle = null;
$last = null;
foreach ($chapters as $ch) {
    if (!$edit and $ch->hidden) {
        continue;
    }
    if ($last == $chapter->id) {
        $nextid = $ch->id;
        $nexttitle = book_get_chapter_title($ch->id, $chapters, $book, $context);
        break;
    }
    if ($ch->id != $chapter->id) {
        $previd = $ch->id;
        $prevtitle = book_get_chapter_title($ch->id, $chapters, $book, $context);
    }
    $last = $ch->id;
}

$islastchapter = false;
if ($book->navstyle) {
    $navprevicon = right_to_left() ? 'nav_next' : 'nav_prev';
    $navnexticon = right_to_left() ? 'nav_prev' : 'nav_next';
    $navprevdisicon = right_to_left() ? 'nav_next_dis' : 'nav_prev_dis';

    $chnavigation = '';
    if ($previd) {
        $navprev = get_string('navprev', 'book');
        if ($book->navstyle == 1) {
            $chnavigation .= '<a title="' . $navprev . '" class="bookprev" href="view.php?id=' .
                $cm->id . '&amp;chapterid=' . $previd .  '">' .
                '<img src="' . $OUTPUT->pix_url($navprevicon, 'mod_book') . '" class="icon" alt="' . $navprev . '"/></a>';
        } else {
            $chnavigation .= '<a title="' . $navprev . '" class="bookprev" href="view.php?id=' .
                $cm->id . '&amp;chapterid=' . $previd . '">' .
                '<span class="chaptername"><span class="arrow">' . $OUTPUT->larrow() . '&nbsp;</span></span>' .
                $navprev . ':&nbsp;<span class="chaptername">' . $prevtitle . '</span></a>';
        }
    } else {
        if ($book->navstyle == 1) {
            $chnavigation .= '<img src="' . $OUTPUT->pix_url($navprevdisicon, 'mod_book') . '" class="icon" alt="" />';
        }
    }
    if ($nextid) {
        $navnext = get_string('navnext', 'book');
        if ($book->navstyle == 1) {
            $chnavigation .= '<a title="' . $navnext . '" class="booknext" href="view.php?id=' .
                $cm->id . '&amp;chapterid='.$nextid.'">' .
                '<img src="' . $OUTPUT->pix_url($navnexticon, 'mod_book').'" class="icon" alt="' . $navnext . '" /></a>';
        } else {
            $chnavigation .= ' <a title="' . $navnext . '" class="booknext" href="view.php?id=' .
                $cm->id . '&amp;chapterid='.$nextid.'">' .
                $navnext . ':<span class="chaptername">&nbsp;' . $nexttitle.
                '<span class="arrow">&nbsp;' . $OUTPUT->rarrow() . '</span></span></a>';
        }
    } else {
        $navexit = get_string('navexit', 'book');
        $sec = $DB->get_field('course_sections', 'section', array('id' => $cm->section));
        $returnurl = course_get_url($course, $sec);
        if ($book->navstyle == 1) {
            $chnavigation .= '<a title="' . $navexit . '" class="bookexit"  href="'.$returnurl.'">' .
                '<img src="' . $OUTPUT->pix_url('nav_exit', 'mod_book') . '" class="icon" alt="' . $navexit . '" /></a>';
        } else {
            $chnavigation .= ' <a title="' . $navexit . '" class="bookexit"  href="'.$returnurl.'">' .
                '<span class="chaptername">' . $navexit . '&nbsp;' . $OUTPUT->uarrow() . '</span></a>';
        }

        $islastchapter = true;
    }
}

book_view($book, $chapter, $islastchapter, $course, $cm, $context);

// =====================================================
// Book display HTML code
// =====================================================

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($book->name));

$navclasses = book_get_nav_classes();

if ($book->navstyle) {
    // Upper navigation.
    echo '<div class="navtop clearfix ' . $navclasses[$book->navstyle] . '">' . $chnavigation . '</div>';
}

// The chapter itself.
$hidden = $chapter->hidden ? ' dimmed_text' : null;
echo $OUTPUT->box_start('generalbox book_content' . $hidden);

if (!$book->customtitles) {
    if (!$chapter->subchapter) {
        $currtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
        echo $OUTPUT->heading($currtitle, 3);
    } else {
        $currtitle = book_get_chapter_title($chapters[$chapter->id]->parent, $chapters, $book, $context);
        $currsubtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
        echo $OUTPUT->heading($currtitle, 3);
        echo $OUTPUT->heading($currsubtitle, 4);
    }
}
$chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $chapter->id);
echo format_text($chaptertext, $chapter->contentformat, array('noclean'=>true, 'overflowdiv'=>true, 'context'=>$context));

echo $OUTPUT->box_end();

if ($book->navstyle) {
    // Lower navigation.
    echo '<div class="navbottom clearfix ' . $navclasses[$book->navstyle] . '">' . $chnavigation . '</div>';
}

echo $OUTPUT->footer();
