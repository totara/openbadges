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
 * @package   mod_survey
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Graph size
 * @global int $SURVEY_GHEIGHT
 */
global $SURVEY_GHEIGHT;
$SURVEY_GHEIGHT = 500;
/**
 * Graph size
 * @global int $SURVEY_GWIDTH
 */
global $SURVEY_GWIDTH;
$SURVEY_GWIDTH  = 900;
/**
 * Question Type
 * @global array $SURVEY_QTYPE
 */
global $SURVEY_QTYPE;
$SURVEY_QTYPE = array (
        "-3" => "Virtual Actual and Preferred",
        "-2" => "Virtual Preferred",
        "-1" => "Virtual Actual",
         "0" => "Text",
         "1" => "Actual",
         "2" => "Preferred",
         "3" => "Actual and Preferred",
        );


define("SURVEY_COLLES_ACTUAL",           "1");
define("SURVEY_COLLES_PREFERRED",        "2");
define("SURVEY_COLLES_PREFERRED_ACTUAL", "3");
define("SURVEY_ATTLS",                   "4");
define("SURVEY_CIQ",                     "5");


// STANDARD FUNCTIONS ////////////////////////////////////////////////////////
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $survey
 * @return int|bool
 */
function survey_add_instance($survey) {
    global $DB;

    if (!$template = $DB->get_record("survey", array("id"=>$survey->template))) {
        return 0;
    }

    $survey->questions    = $template->questions;
    $survey->timecreated  = time();
    $survey->timemodified = $survey->timecreated;

    return $DB->insert_record("survey", $survey);

}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $survey
 * @return bool
 */
function survey_update_instance($survey) {
    global $DB;

    if (!$template = $DB->get_record("survey", array("id"=>$survey->template))) {
        return 0;
    }

    $survey->id           = $survey->instance;
    $survey->questions    = $template->questions;
    $survey->timemodified = time();

    return $DB->update_record("survey", $survey);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function survey_delete_instance($id) {
    global $DB;

    if (! $survey = $DB->get_record("survey", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("survey_analysis", array("survey"=>$survey->id))) {
        $result = false;
    }

    if (! $DB->delete_records("survey_answers", array("survey"=>$survey->id))) {
        $result = false;
    }

    if (! $DB->delete_records("survey", array("id"=>$survey->id))) {
        $result = false;
    }

    return $result;
}

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $survey
 * @return $result
 */
function survey_user_outline($course, $user, $mod, $survey) {
    global $DB;

    if ($answers = $DB->get_records("survey_answers", array('survey'=>$survey->id, 'userid'=>$user->id))) {
        $lastanswer = array_pop($answers);

        $result = new stdClass();
        $result->info = get_string("done", "survey");
        $result->time = $lastanswer->time;
        return $result;
    }
    return NULL;
}

/**
 * @global stdObject
 * @global object
 * @uses SURVEY_CIQ
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $survey
 */
function survey_user_complete($course, $user, $mod, $survey) {
    global $CFG, $DB, $OUTPUT;

    if (survey_already_done($survey->id, $user->id)) {
        if ($survey->template == SURVEY_CIQ) { // print out answers for critical incidents
            $table = new html_table();
            $table->align = array("left", "left");

            $questions = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));
            $questionorder = explode(",", $survey->questions);

            foreach ($questionorder as $key=>$val) {
                $question = $questions[$val];
                $questiontext = get_string($question->shorttext, "survey");

                if ($answer = survey_get_user_answer($survey->id, $question->id, $user->id)) {
                    $answertext = "$answer->answer1";
                } else {
                    $answertext = "No answer";
                }
                $table->data[] = array("<b>$questiontext</b>", s($answertext));
            }
            echo html_writer::table($table);

        } else {

            survey_print_graph("id=$mod->id&amp;sid=$user->id&amp;type=student.png");
        }

    } else {
        print_string("notdone", "survey");
    }
}

/**
 * @global stdClass
 * @global object
 * @param object $course
 * @param mixed $viewfullnames
 * @param int $timestamp
 * @return bool
 */
function survey_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $DB, $OUTPUT;

    $modinfo = get_fast_modinfo($course);
    $ids = array();
    foreach ($modinfo->cms as $cm) {
        if ($cm->modname != 'survey') {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $ids[$cm->instance] = $cm->instance;
    }

    if (!$ids) {
        return false;
    }

    $slist = implode(',', $ids); // there should not be hundreds of glossaries in one course, right?

    $allusernames = user_picture::fields('u');
    $rs = $DB->get_recordset_sql("SELECT sa.userid, sa.survey, MAX(sa.time) AS time,
                                         $allusernames
                                    FROM {survey_answers} sa
                                    JOIN {user} u ON u.id = sa.userid
                                   WHERE sa.survey IN ($slist) AND sa.time > ?
                                GROUP BY sa.userid, sa.survey, $allusernames
                                ORDER BY time ASC", array($timestart));
    if (!$rs->valid()) {
        $rs->close(); // Not going to iterate (but exit), close rs
        return false;
    }

    $surveys = array();

    foreach ($rs as $survey) {
        $cm = $modinfo->instances['survey'][$survey->survey];
        $survey->name = $cm->name;
        $survey->cmid = $cm->id;
        $surveys[] = $survey;
    }
    $rs->close();

    if (!$surveys) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newsurveyresponses', 'survey').':', 3);
    foreach ($surveys as $survey) {
        $url = $CFG->wwwroot.'/mod/survey/view.php?id='.$survey->cmid;
        print_recent_activity_note($survey->time, $survey, $survey->name, $url, false, $viewfullnames);
    }

    return true;
}

// SQL FUNCTIONS ////////////////////////////////////////////////////////

/**
 * @global object
 * @param sting $log
 * @return array
 */
function survey_log_info($log) {
    global $DB;
    return $DB->get_record_sql("SELECT s.name, u.firstname, u.lastname, u.picture
                                  FROM {survey} s, {user} u
                                 WHERE s.id = ?  AND u.id = ?", array($log->info, $log->userid));
}

/**
 * @global object
 * @param int $surveyid
 * @param int $groupid
 * @param int $groupingid
 * @return array
 */
function survey_get_responses($surveyid, $groupid, $groupingid) {
    global $DB;

    $params = array('surveyid'=>$surveyid, 'groupid'=>$groupid, 'groupingid'=>$groupingid);

    if ($groupid) {
        $groupsjoin = "JOIN {groups_members} gm ON u.id = gm.userid AND gm.groupid = :groupid ";

    } else if ($groupingid) {
        $groupsjoin = "JOIN {groups_members} gm ON u.id = gm.userid
                       JOIN {groupings_groups} gg ON gm.groupid = gg.groupid AND gg.groupingid = :groupingid ";
    } else {
        $groupsjoin = "";
    }

    $userfields = user_picture::fields('u');
    return $DB->get_records_sql("SELECT $userfields, MAX(a.time) as time
                                   FROM {survey_answers} a
                                   JOIN {user} u ON a.userid = u.id
                            $groupsjoin
                                  WHERE a.survey = :surveyid
                               GROUP BY $userfields
                               ORDER BY time ASC", $params);
}

/**
 * @global object
 * @param int $survey
 * @param int $user
 * @return array
 */
function survey_get_analysis($survey, $user) {
    global $DB;

    return $DB->get_record_sql("SELECT notes
                                  FROM {survey_analysis}
                                 WHERE survey=? AND userid=?", array($survey, $user));
}

/**
 * @global object
 * @param int $survey
 * @param int $user
 * @param string $notes
 */
function survey_update_analysis($survey, $user, $notes) {
    global $DB;

    return $DB->execute("UPDATE {survey_analysis}
                            SET notes=?
                          WHERE survey=?
                            AND userid=?", array($notes, $survey, $user));
}

/**
 * @global object
 * @param int $surveyid
 * @param int $groupid
 * @param string $sort
 * @return array
 */
function survey_get_user_answers($surveyid, $questionid, $groupid, $sort="sa.answer1,sa.answer2 ASC") {
    global $DB;

    $params = array('surveyid'=>$surveyid, 'questionid'=>$questionid);

    if ($groupid) {
        $groupfrom = ', {groups_members} gm';
        $groupsql  = 'AND gm.groupid = :groupid AND u.id = gm.userid';
        $params['groupid'] = $groupid;
    } else {
        $groupfrom = '';
        $groupsql  = '';
    }

    $userfields = user_picture::fields('u');
    return $DB->get_records_sql("SELECT sa.*, $userfields
                                   FROM {survey_answers} sa,  {user} u $groupfrom
                                  WHERE sa.survey = :surveyid
                                        AND sa.question = :questionid
                                        AND u.id = sa.userid $groupsql
                               ORDER BY $sort", $params);
}

/**
 * @global object
 * @param int $surveyid
 * @param int $questionid
 * @param int $userid
 * @return array
 */
function survey_get_user_answer($surveyid, $questionid, $userid) {
    global $DB;

    return $DB->get_record_sql("SELECT sa.*
                                  FROM {survey_answers} sa
                                 WHERE sa.survey = ?
                                       AND sa.question = ?
                                       AND sa.userid = ?", array($surveyid, $questionid, $userid));
}

// MODULE FUNCTIONS ////////////////////////////////////////////////////////
/**
 * @global object
 * @param int $survey
 * @param int $user
 * @param string $notes
 * @return bool|int
 */
function survey_add_analysis($survey, $user, $notes) {
    global $DB;

    $record = new stdClass();
    $record->survey = $survey;
    $record->userid = $user;
    $record->notes = $notes;

    return $DB->insert_record("survey_analysis", $record, false);
}
/**
 * @global object
 * @param int $survey
 * @param int $user
 * @return bool
 */
function survey_already_done($survey, $user) {
    global $DB;

    return $DB->record_exists("survey_answers", array("survey"=>$survey, "userid"=>$user));
}
/**
 * @param int $surveyid
 * @param int $groupid
 * @param int $groupingid
 * @return int
 */
function survey_count_responses($surveyid, $groupid, $groupingid) {
    if ($responses = survey_get_responses($surveyid, $groupid, $groupingid)) {
        return count($responses);
    } else {
        return 0;
    }
}

/**
 * @param int $cmid
 * @param array $results
 * @param int $courseid
 */
function survey_print_all_responses($cmid, $results, $courseid) {
    global $OUTPUT;
    $table = new html_table();
    $table->head  = array ("", get_string("name"),  get_string("time"));
    $table->align = array ("", "left", "left");
    $table->size = array (35, "", "" );

    foreach ($results as $a) {
        $table->data[] = array($OUTPUT->user_picture($a, array('courseid'=>$courseid)),
               html_writer::link("report.php?action=student&student=$a->id&id=$cmid", fullname($a)),
               userdate($a->time));
    }

    echo html_writer::table($table);
}

/**
 * @global object
 * @param int $templateid
 * @return string
 */
function survey_get_template_name($templateid) {
    global $DB;

    if ($templateid) {
        if ($ss = $DB->get_record("surveys", array("id"=>$templateid))) {
            return $ss->name;
        }
    } else {
        return "";
    }
}


/**
 * @param string $name
 * @param array $numwords
 * @return string
 */
function survey_shorten_name ($name, $numwords) {
    $words = explode(" ", $name);
    $output = '';
    for ($i=0; $i < $numwords; $i++) {
        $output .= $words[$i]." ";
    }
    return $output;
}

/**
 * @todo Check this function
 *
 * @global object
 * @global object
 * @global int
 * @global void This is never defined
 * @global object This is defined twice?
 * @param object $question
 */
function survey_print_multi($question) {
    global $USER, $DB, $qnum, $checklist, $DB, $OUTPUT; //TODO: this is sloppy globals abuse

    $stripreferthat = get_string("ipreferthat", "survey");
    $strifoundthat = get_string("ifoundthat", "survey");
    $strdefault    = get_string('notyetanswered', 'survey');
    $strresponses  = get_string('responses', 'survey');

    echo $OUTPUT->heading($question->text, 3);
    echo "\n<table width=\"90%\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\" class=\"surveytable\">";

    $options = explode( ",", $question->options);
    $numoptions = count($options);

    // COLLES Actual (which is having questions of type 1) and COLLES Preferred (type 2)
    // expect just one answer per question. COLLES Actual and Preferred (type 3) expects
    // two answers per question. ATTLS (having a single question of type 1) expects one
    // answer per question. CIQ is not using multiquestions (i.e. a question with subquestions).
    // Note that the type of subquestions does not really matter, it's the type of the
    // question itself that determines everything.
    $oneanswer = ($question->type == 1 || $question->type == 2) ? true : false;

    // COLLES Preferred (having questions of type 2) will use the radio elements with the name
    // like qP1, qP2 etc. COLLES Actual and ATTLS have radios like q1, q2 etc.
    if ($question->type == 2) {
        $P = "P";
    } else {
        $P = "";
    }

    echo "<tr class=\"smalltext\"><th scope=\"row\">$strresponses</th>";
    echo "<th scope=\"col\" class=\"hresponse\">". get_string('notyetanswered', 'survey'). "</th>";
    while (list ($key, $val) = each ($options)) {
        echo "<th scope=\"col\" class=\"hresponse\">$val</th>\n";
    }
    echo "</tr>\n";

    echo "<tr><th scope=\"col\" colspan=\"7\">$question->intro</th></tr>\n";

    $subquestions = survey_get_subquestions($question);

    foreach ($subquestions as $q) {
        $qnum++;
        if ($oneanswer) {
            $rowclass = survey_question_rowclass($qnum);
        } else {
            $rowclass = survey_question_rowclass(round($qnum / 2));
        }
        if ($q->text) {
            $q->text = get_string($q->text, "survey");
        }

        echo "<tr class=\"$rowclass rblock\">";
        if ($oneanswer) {
            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            echo $q->text ."</th>\n";

            $default = get_accesshide($strdefault);
            echo "<td class=\"whitecell\"><label for=\"q$P$q->id\"><input type=\"radio\" name=\"q$P$q->id\" id=\"q$P" . $q->id . "_D\" value=\"0\" checked=\"checked\" />$default</label></td>";

            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "q$P" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"q$P$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }
            $checklist["q$P$q->id"] = 0;

        } else {
            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            $qnum++;
            echo "<span class=\"preferthat\">$stripreferthat</span> &nbsp; ";
            echo "<span class=\"option\">$q->text</span></th>\n";

            $default = get_accesshide($strdefault);
            echo '<td class="whitecell"><label for="qP'.$q->id.'"><input type="radio" name="qP'.$q->id.'" id="qP'.$q->id.'" value="0" checked="checked" />'.$default.'</label></td>';


            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "qP" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"qP$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }
            echo "</tr>";

            echo "<tr class=\"$rowclass rblock\">";
            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            echo "<span class=\"foundthat\">$strifoundthat</span> &nbsp; ";
            echo "<span class=\"option\">$q->text</span></th>\n";

            $default = get_accesshide($strdefault);
            echo '<td class="whitecell"><label for="q'. $q->id .'"><input type="radio" name="q'.$q->id. '" id="q'. $q->id .'" value="0" checked="checked" />'.$default.'</label></td>';

            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "q" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"q$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }

            $checklist["qP$q->id"] = 0;
            $checklist["q$q->id"] = 0;
        }
        echo "</tr>\n";
    }
    echo "</table>";
}


/**
 * @global object
 * @global int
 * @param object $question
 */
function survey_print_single($question) {
    global $DB, $qnum, $OUTPUT;

    $rowclass = survey_question_rowclass(0);

    $qnum++;

    echo "<br />\n";
    echo "<table width=\"90%\" cellpadding=\"4\" cellspacing=\"0\">\n";
    echo "<tr class=\"$rowclass\">";
    echo "<th scope=\"row\" class=\"optioncell\"><label for=\"q$question->id\"><b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
    echo "<span class=\"questioncell\">$question->text</span></label></th>\n";
    echo "<td class=\"questioncell smalltext\">\n";


    if ($question->type == 0) {           // Plain text field
        echo "<textarea rows=\"3\" cols=\"30\" name=\"q$question->id\" id=\"q$question->id\">$question->options</textarea>";

    } else if ($question->type > 0) {     // Choose one of a number
        $strchoose = get_string("choose");
        echo "<select name=\"q$question->id\" id=\"q$question->id\">";
        echo "<option value=\"0\" selected=\"selected\">$strchoose...</option>";
        $options = explode( ",", $question->options);
        foreach ($options as $key => $val) {
            $key++;
            echo "<option value=\"$key\">$val</option>";
        }
        echo "</select>";

    } else if ($question->type < 0) {     // Choose several of a number
        $options = explode( ",", $question->options);
        echo $OUTPUT->notification("This question type not supported yet");
    }

    echo "</td></tr></table>";

}

/**
 *
 * @param int $qnum
 * @return string
 */
function survey_question_rowclass($qnum) {

    if ($qnum) {
        return $qnum % 2 ? 'r0' : 'r1';
    } else {
        return 'r0';
    }
}

/**
 * @global object
 * @global int
 * @global int
 * @param string $url
 */
function survey_print_graph($url) {
    global $CFG, $SURVEY_GHEIGHT, $SURVEY_GWIDTH;

    echo "<img class='resultgraph' height=\"$SURVEY_GHEIGHT\" width=\"$SURVEY_GWIDTH\"".
         " src=\"$CFG->wwwroot/mod/survey/graph.php?$url\" alt=\"".get_string("surveygraph", "survey")."\" />";
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function survey_get_view_actions() {
    return array('download','view all','view form','view graph','view report');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function survey_get_post_actions() {
    return array('submit');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the survey.
 *
 * @param object $mform form passed by reference
 */
function survey_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'surveyheader', get_string('modulenameplural', 'survey'));
    $mform->addElement('checkbox', 'reset_survey_answers', get_string('deleteallanswers','survey'));
    $mform->addElement('checkbox', 'reset_survey_analysis', get_string('deleteanalysis','survey'));
    $mform->disabledIf('reset_survey_analysis', 'reset_survey_answers', 'checked');
}

/**
 * Course reset form defaults.
 * @return array
 */
function survey_reset_course_form_defaults($course) {
    return array('reset_survey_answers'=>1, 'reset_survey_analysis'=>1);
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * survey responses for course $data->courseid.
 *
 * @global object
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function survey_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'survey');
    $status = array();

    $surveyssql = "SELECT ch.id
                     FROM {survey} ch
                    WHERE ch.course=?";
    $params = array($data->courseid);

    if (!empty($data->reset_survey_answers)) {
        $DB->delete_records_select('survey_answers', "survey IN ($surveyssql)", $params);
        $DB->delete_records_select('survey_analysis', "survey IN ($surveyssql)", $params);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallanswers', 'survey'), 'error'=>false);
    }

    if (!empty($data->reset_survey_analysis)) {
        $DB->delete_records_select('survey_analysis', "survey IN ($surveyssql)", $params);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallanswers', 'survey'), 'error'=>false);
    }

    // no date shifting
    return $status;
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function survey_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function survey_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param navigation_node $settings
 * @param navigation_node $surveynode
 */
function survey_extend_settings_navigation($settings, $surveynode) {
    global $PAGE;

    if (has_capability('mod/survey:readresponses', $PAGE->cm->context)) {
        $responsesnode = $surveynode->add(get_string("responsereports", "survey"));

        $url = new moodle_url('/mod/survey/report.php', array('id' => $PAGE->cm->id, 'action'=>'summary'));
        $responsesnode->add(get_string("summary", "survey"), $url);

        $url = new moodle_url('/mod/survey/report.php', array('id' => $PAGE->cm->id, 'action'=>'scales'));
        $responsesnode->add(get_string("scales", "survey"), $url);

        $url = new moodle_url('/mod/survey/report.php', array('id' => $PAGE->cm->id, 'action'=>'questions'));
        $responsesnode->add(get_string("question", "survey"), $url);

        $url = new moodle_url('/mod/survey/report.php', array('id' => $PAGE->cm->id, 'action'=>'students'));
        $responsesnode->add(get_string('participants'), $url);

        if (has_capability('mod/survey:download', $PAGE->cm->context)) {
            $url = new moodle_url('/mod/survey/report.php', array('id' => $PAGE->cm->id, 'action'=>'download'));
            $surveynode->add(get_string('downloadresults', 'survey'), $url);
        }
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function survey_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-survey-*'=>get_string('page-mod-survey-x', 'survey'));
    return $module_pagetype;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $survey     survey object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @param  string $viewed       which page viewed
 * @since Moodle 3.0
 */
function survey_view($survey, $course, $cm, $context, $viewed) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $survey->id,
        'courseid' => $course->id,
        'other' => array('viewed' => $viewed)
    );

    $event = \mod_survey\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('survey', $survey);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Helper function for ordering a set of questions by the given ids.
 *
 * @param  array $questions     array of questions objects
 * @param  array $questionorder array of questions ids indicating the correct order
 * @return array                list of questions ordered
 * @since Moodle 3.0
 */
function survey_order_questions($questions, $questionorder) {

    $finalquestions = array();
    foreach ($questionorder as $qid) {
        $finalquestions[] = $questions[$qid];
    }
    return $finalquestions;
}

/**
 * Translate the question texts and options.
 *
 * @param  stdClass $question question object
 * @return stdClass question object with all the text fields translated
 * @since Moodle 3.0
 */
function survey_translate_question($question) {

    if ($question->text) {
        $question->text = get_string($question->text, "survey");
    }

    if ($question->shorttext) {
        $question->shorttext = get_string($question->shorttext, "survey");
    }

    if ($question->intro) {
        $question->intro = get_string($question->intro, "survey");
    }

    if ($question->options) {
        $question->options = get_string($question->options, "survey");
    }
    return $question;
}

/**
 * Returns the questions for a survey (ordered).
 *
 * @param  stdClass $survey survey object
 * @return array list of questions ordered
 * @since Moodle 3.0
 * @throws  moodle_exception
 */
function survey_get_questions($survey) {
    global $DB;

    $questionids = explode(',', $survey->questions);
    if (! $questions = $DB->get_records_list("survey_questions", "id", $questionids)) {
        throw new moodle_exception('cannotfindquestion', 'survey');
    }

    return survey_order_questions($questions, $questionids);
}

/**
 * Returns subquestions for a given question (ordered).
 *
 * @param  stdClass $question questin object
 * @return array list of subquestions ordered
 * @since Moodle 3.0
 */
function survey_get_subquestions($question) {
    global $DB;

    $questionids = explode(',', $question->multi);
    $questions = $DB->get_records_list("survey_questions", "id", $questionids);

    return survey_order_questions($questions, $questionids);
}

/**
 * Save the answer for the given survey
 *
 * @param  stdClass $survey   a survey object
 * @param  array $answersrawdata the answers to be saved
 * @param  stdClass $course   a course object (required for trigger the submitted event)
 * @param  stdClass $context  a context object (required for trigger the submitted event)
 * @since Moodle 3.0
 */
function survey_save_answers($survey, $answersrawdata, $course, $context) {
    global $DB, $USER;

    $answers = array();

    // Sort through the data and arrange it.
    // This is necessary because some of the questions may have two answers, eg Question 1 -> 1 and P1.
    foreach ($answersrawdata as $key => $val) {
        if ($key != "userid" && $key != "id") {
            if (substr($key, 0, 1) == "q") {
                $key = clean_param(substr($key, 1), PARAM_ALPHANUM);   // Keep everything but the 'q', number or P number.
            }
            if (substr($key, 0, 1) == "P") {
                $realkey = (int) substr($key, 1);
                $answers[$realkey][1] = $val;
            } else {
                $answers[$key][0] = $val;
            }
        }
    }

    // Now store the data.
    $timenow = time();
    $answerstoinsert = array();
    foreach ($answers as $key => $val) {
        if ($key != 'sesskey') {
            $newdata = new stdClass();
            $newdata->time = $timenow;
            $newdata->userid = $USER->id;
            $newdata->survey = $survey->id;
            $newdata->question = $key;
            if (!empty($val[0])) {
                $newdata->answer1 = $val[0];
            } else {
                $newdata->answer1 = "";
            }
            if (!empty($val[1])) {
                $newdata->answer2 = $val[1];
            } else {
                $newdata->answer2 = "";
            }

            $answerstoinsert[] = $newdata;
        }
    }

    if (!empty($answerstoinsert)) {
        $DB->insert_records("survey_answers", $answerstoinsert);
    }

    $params = array(
        'context' => $context,
        'courseid' => $course->id,
        'other' => array('surveyid' => $survey->id)
    );
    $event = \mod_survey\event\response_submitted::create($params);
    $event->trigger();
}
