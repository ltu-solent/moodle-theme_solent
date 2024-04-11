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
 * Assignment output renderer override
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output;

use assign;
use context_module;
use mod_assign\output\assign_submission_status;
use mod_assign\output\renderer as renderer_base;
use moodle_url;

/**
 * Assign renderer overrride
 */
class mod_assign_renderer extends renderer_base {
    /**
     * Utility function to add a row of data to a table with 2 columns where the first column is the table's header.
     * Modified the table param and does not return a value.
     * Solent: This needs to be overridden here because the function is private in the private class.
     *
     * @param \html_table $table The table to append the row of data to
     * @param string $first The first column text
     * @param string $second The second column text
     * @param array $firstattributes The first column attributes (optional)
     * @param array $secondattributes The second column attributes (optional)
     * @return void
     */
    private function add_table_row_tuple(\html_table $table, $first, $second, $firstattributes = [],
            $secondattributes = []) {
        $row = new \html_table_row();
        $cell1 = new \html_table_cell($first);
        $cell1->header = true;
        if (!empty($firstattributes)) {
            $cell1->attributes = $firstattributes;
        }
        $cell2 = new \html_table_cell($second);
        if (!empty($secondattributes)) {
            $cell2->attributes = $secondattributes;
        }
        $row->cells = [$cell1, $cell2];
        $table->data[] = $row;
    }

    /**
     * Render a table containing the current status of the submission.
     *
     * @param assign_submission_status $status
     * @return string
     */
    public function render_assign_submission_status(assign_submission_status $status) {
        $o = '';
        $o .= $this->output->container_start('submissionstatustable');
        $o .= $this->output->heading(get_string('submissionstatusheading', 'assign'), 3);
        $time = time();

        $o .= $this->output->box_start('boxaligncenter submissionsummarytable');

        $t = new \html_table();
        $t->attributes['class'] = 'generaltable table-bordered';

        $warningmsg = '';
        if ($status->teamsubmissionenabled) {
            $cell1content = get_string('submissionteam', 'assign');
            $group = $status->submissiongroup;
            if ($group) {
                $cell2content = format_string($group->name, false, $status->context);
            } else if ($status->preventsubmissionnotingroup) {
                if (count($status->usergroups) == 0) {
                    $notification = new \core\output\notification(get_string('noteam', 'assign'), 'error');
                    $notification->set_show_closebutton(false);
                    $warningmsg = $this->output->notification(get_string('noteam_desc', 'assign'), 'error');
                } else if (count($status->usergroups) > 1) {
                    $notification = new \core\output\notification(get_string('multipleteams', 'assign'), 'error');
                    $notification->set_show_closebutton(false);
                    $warningmsg = $this->output->notification(get_string('multipleteams_desc', 'assign'), 'error');
                }
                $cell2content = $this->output->render($notification);
            } else {
                $cell2content = get_string('defaultteam', 'assign');
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        if ($status->attemptreopenmethod != ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
            $currentattempt = 1;
            if (!$status->teamsubmissionenabled) {
                if ($status->submission) {
                    $currentattempt = $status->submission->attemptnumber + 1;
                }
            } else {
                if ($status->teamsubmission) {
                    $currentattempt = $status->teamsubmission->attemptnumber + 1;
                }
            }

            $cell1content = get_string('attemptnumber', 'assign');
            $maxattempts = $status->maxattempts;
            if ($maxattempts == ASSIGN_UNLIMITED_ATTEMPTS) {
                $cell2content = get_string('currentattempt', 'assign', $currentattempt);
            } else {
                $cell2content = get_string('currentattemptof', 'assign',
                    ['attemptnumber' => $currentattempt, 'maxattempts' => $maxattempts]);
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        $cell1content = get_string('submissionstatus', 'assign');
        $cell2attributes = [];
        if (!$status->teamsubmissionenabled) {
            if ($status->submission && $status->submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $cell2content = get_string('submissionstatus_' . $status->submission->status, 'assign');
                $cell2attributes = ['class' => 'submissionstatus' . $status->submission->status];
            } else {
                if (!$status->submissionsenabled) {
                    $cell2content = get_string('noonlinesubmissions', 'assign');
                } else {
                    $cell2content = get_string('nosubmissionyet', 'assign');
                }
            }
        } else {
            $group = $status->submissiongroup;
            if (!$group && $status->preventsubmissionnotingroup) {
                $cell2content = get_string('nosubmission', 'assign');
            } else if ($status->teamsubmission && $status->teamsubmission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $teamstatus = $status->teamsubmission->status;
                $cell2content = get_string('submissionstatus_' . $teamstatus, 'assign');

                $members = $status->submissiongroupmemberswhoneedtosubmit;
                $userslist = [];
                foreach ($members as $member) {
                    $urlparams = ['id' => $member->id, 'course' => $status->courseid];
                    $url = new \moodle_url('/user/view.php', $urlparams);
                    if ($status->view == assign_submission_status::GRADER_VIEW && $status->blindmarking) {
                        $userslist[] = $member->alias;
                    } else {
                        $fullname = fullname($member, $status->canviewfullnames);
                        $userslist[] = $this->output->action_link($url, $fullname);
                    }
                }
                if (count($userslist) > 0) {
                    $userstr = join(', ', $userslist);
                    $formatteduserstr = get_string('userswhoneedtosubmit', 'assign', $userstr);
                    $cell2content .= $this->output->container($formatteduserstr);
                }

                $cell2attributes = ['class' => 'submissionstatus' . $status->teamsubmission->status];
            } else {
                if (!$status->submissionsenabled) {
                    $cell2content = get_string('noonlinesubmissions', 'assign');
                } else {
                    $cell2content = get_string('nosubmission', 'assign');
                }
            }
        }

        $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);

        // Is locked?
        if ($status->locked) {
            $cell1content = '';
            $cell2content = get_string('submissionslocked', 'assign');
            $cell2attributes = ['class' => 'submissionlocked'];
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);
        }

        // Grading status.
        $cell1content = get_string('gradingstatus', 'assign');
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_GRADING_STATUS_NOT_GRADED) {
            $cell2content = get_string($status->gradingstatus, 'assign');
        } else {
            $gradingstatus = 'markingworkflowstate' . $status->gradingstatus;
            $cell2content = get_string($gradingstatus, 'assign');
        }
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            $cell2attributes = ['class' => 'submissiongraded'];
        } else {
            $cell2attributes = ['class' => 'submissionnotgraded'];
        }
        $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);

        $submission = $status->teamsubmission ? $status->teamsubmission : $status->submission;
        $duedate = $status->duedate;
        if ($duedate > 0) {
            if ($status->view == assign_submission_status::GRADER_VIEW) {
                if ($status->cutoffdate) {
                    // Cut off date.
                    $cell1content = get_string('cutoffdate', 'assign');
                    $cell2content = userdate($status->cutoffdate);
                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }
            }

            if ($status->extensionduedate) {
                // Extension date.
                $cell1content = get_string('extensionduedate', 'assign');
                $cell2content = userdate($status->extensionduedate);
                $this->add_table_row_tuple($t, $cell1content, $cell2content);
                $duedate = $status->extensionduedate;
            }
        }

        // Time remaining.
        // Only add the row if there is a due date, or a countdown.
        if ($status->duedate > 0 || !empty($submission->timestarted)) {
            $cell1content = get_string('timeremaining', 'assign');
            [$cell2content, $cell2attributes] = $this->get_time_remaining($status);
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], ['class' => $cell2attributes]);
        }

        // SU_AMEND_START - Assignment: Cut off date/time remaining in submission status.
        if ($status->view == assign_submission_status::STUDENT_VIEW) {
            $cutoffdate = 0;
            $cutoffdate = $status->cutoffdate;
            if ($cutoffdate) {
                if ($cutoffdate > $status->duedate) {
                    $cell1content = get_string('latesubmissions', 'assign');
                    $cell2content = userdate($status->cutoffdate);
                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }
            }
        }
        // SU_AMEND_END.

        // Add time limit info if there is one.
        $timelimitenabled = get_config('assign', 'enabletimelimit') && $status->timelimit > 0;
        if ($timelimitenabled && $status->timelimit > 0) {
            $cell1content = get_string('timelimit', 'assign');
            $cell2content = format_time($status->timelimit);
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], []);
        }

        // Show graders whether this submission is editable by students.
        if ($status->view == assign_submission_status::GRADER_VIEW) {
            $cell1content = get_string('editingstatus', 'assign');
            if ($status->canedit) {
                $cell2content = get_string('submissioneditable', 'assign');
                $cell2attributes = ['class' => 'submissioneditable'];
            } else {
                $cell2content = get_string('submissionnoteditable', 'assign');
                $cell2attributes = ['class' => 'submissionnoteditable'];
            }
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);
        }

        // Grading criteria preview.
        if (!empty($status->gradingcontrollerpreview)) {
            $cell1content = get_string('gradingmethodpreview', 'assign');
            $cell2content = $status->gradingcontrollerpreview;
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], []);
        }

        // Last modified.
        if ($submission) {
            $cell1content = get_string('timemodified', 'assign');

            if ($submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $cell2content = userdate($submission->timemodified, get_string('strftimedatetimeaccurate', 'langconfig'));
            } else {
                $cell2content = "-";
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);

            if (!$status->teamsubmission || $status->submissiongroup != false || !$status->preventsubmissionnotingroup) {
                foreach ($status->submissionplugins as $plugin) {
                    $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                    if ($plugin->is_enabled() &&
                        $plugin->is_visible() &&
                        $plugin->has_user_summary() &&
                        $pluginshowsummary
                    ) {

                        $cell1content = $plugin->get_name();
                        $displaymode = \assign_submission_plugin_submission::SUMMARY;
                        $pluginsubmission = new \assign_submission_plugin_submission($plugin,
                            $submission,
                            $displaymode,
                            $status->coursemoduleid,
                            $status->returnaction,
                            $status->returnparams);
                        $cell2content = $this->render($pluginsubmission);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }
                }
            }
        }

        $o .= $warningmsg;
        $o .= \html_writer::table($t);
        $o .= $this->output->box_end();

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Output the attempt history for this assignment
     *
     * @param \assign_attempt_history $history
     * @return string
     */
    public function render_assign_attempt_history(\assign_attempt_history $history) {
        $o = '';

        // Don't show the last one because it is the current submission.
        array_pop($history->submissions);

        // Show newest to oldest.
        $history->submissions = array_reverse($history->submissions);

        if (empty($history->submissions)) {
            return '';
        }

        $containerid = 'attempthistory' . uniqid();
        $o .= $this->output->heading(get_string('attempthistory', 'assign'), 3);
        $o .= $this->box_start('attempthistory', $containerid);

        foreach ($history->submissions as $i => $submission) {
            $grade = null;
            foreach ($history->grades as $onegrade) {
                if ($onegrade->attemptnumber == $submission->attemptnumber) {
                    if ($onegrade->grade != ASSIGN_GRADE_NOT_SET) {
                        $grade = $onegrade;
                    }
                    break;
                }
            }

            if ($submission) {
                $submissionsummary = userdate($submission->timemodified, '%d %B %Y, %I:%M:%S %p');
            } else {
                $submissionsummary = get_string('nosubmission', 'assign');
            }

            $attemptsummaryparams = ['attemptnumber' => $submission->attemptnumber + 1,
                                          'submissionsummary' => $submissionsummary];
            $o .= $this->heading(get_string('attemptheading', 'assign', $attemptsummaryparams), 4);

            $t = new \html_table();

            if ($submission) {
                $cell1content = get_string('submissionstatus', 'assign');
                $cell2content = get_string('submissionstatus_' . $submission->status, 'assign');
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                foreach ($history->submissionplugins as $plugin) {
                    $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                    if ($plugin->is_enabled() &&
                            $plugin->is_visible() &&
                            $plugin->has_user_summary() &&
                            $pluginshowsummary) {

                        $cell1content = $plugin->get_name();
                        $pluginsubmission = new \assign_submission_plugin_submission($plugin,
                                                                                    $submission,
                                                                                    \assign_submission_plugin_submission::SUMMARY,
                                                                                    $history->coursemoduleid,
                                                                                    $history->returnaction,
                                                                                    $history->returnparams);
                        $cell2content = $this->render($pluginsubmission);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }
                }
            }

            if ($grade) {
                // Heading 'feedback'.
                $title = get_string('feedback', 'assign', $i);
                $title .= $this->output->spacer(['width' => 10]);
                if ($history->cangrade) {
                    // Edit previous feedback.
                    $returnparams = http_build_query($history->returnparams);
                    $urlparams = ['id' => $history->coursemoduleid,
                                   'rownum' => $history->rownum,
                                   'useridlistid' => $history->useridlistid,
                                   'attemptnumber' => $grade->attemptnumber,
                                   'action' => 'grade',
                                   'returnaction' => $history->returnaction,
                                   'returnparams' => $returnparams];
                    $url = new \moodle_url('/mod/assign/view.php', $urlparams);
                    $icon = new \pix_icon('gradefeedback',
                                            get_string('editattemptfeedback', 'assign', $grade->attemptnumber + 1),
                                            'mod_assign');
                    $title .= $this->output->action_icon($url, $icon);
                }
                $cell = new \html_table_cell($title);
                $cell->attributes['class'] = 'feedbacktitle';
                $cell->colspan = 2;
                $t->data[] = new \html_table_row([$cell]);

                // Grade.
                $cell1content = get_string('gradenoun');
                $cell2content = $grade->gradefordisplay;
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                // Graded on.
                $cell1content = get_string('gradedon', 'assign');
                $cell2content = userdate($grade->timemodified);
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                // Graded by set to a real user. Not set can be empty or -1.
                if (!empty($grade->grader) && is_object($grade->grader)) {
                    $cell1content = get_string('gradedby', 'assign');
                    $cell2content = $this->output->user_picture($grade->grader) .
                                    $this->output->spacer(['width' => 30]) . fullname($grade->grader);
                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }

                // Feedback from plugins.
                foreach ($history->feedbackplugins as $plugin) {
                    if ($plugin->is_enabled() &&
                        $plugin->is_visible() &&
                        $plugin->has_user_summary() &&
                        !$plugin->is_empty($grade)) {

                        $pluginfeedback = new \assign_feedback_plugin_feedback(
                            $plugin, $grade, \assign_feedback_plugin_feedback::SUMMARY, $history->coursemoduleid,
                            $history->returnaction, $history->returnparams
                        );

                        $cell1content = $plugin->get_name();
                        $cell2content = $this->render($pluginfeedback);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }

                }

            }

            $o .= \html_writer::table($t);
        }
        $o .= $this->box_end();

        $this->page->requires->yui_module('moodle-mod_assign-history', 'Y.one("#' . $containerid . '").history');

        return $o;
    }

    /**
     * Do some extra checks to determine if warning should be displayed.
     *
     * @param \assign_grading_table $table
     * @return string html of grading table.
     */
    public function render_assign_grading_table(\assign_grading_table $table) {
        global $DB;
        // SU_AMEND_START: Workflow notifications.
        $cmid = $table->get_course_module_id();
        $courseid = $table->get_course_id();
        $modinfo = get_fast_modinfo($courseid);
        $cm = $modinfo->get_cm($cmid);
        // Store the normally rendered table so that we can append contextual notifications before returning.
        $rendered = parent::render_assign_grading_table($table);
        $o = '';
        $assign = new assign(context_module::instance($cm->id), $cm, $cm->get_course());
        // This is not a Summative assignment.
        if ($cm->idnumber == '') {
            $o .= $this->output->notification(get_string('assign_formativeinfo', 'theme_solent'), \core\notification::INFO);
            return $o . $rendered;
        }

        // Standard table filtering is done via query params, and these are saved in user preferences for later retrieval.
        // Get the saved options first, then check the query params to find what the latest filters should be.
        // Note the query param could be empty.
        $gradingprefs = get_user_preferences('flextable_mod_assign_grading-' . $assign->get_context()->id);
        if (!$gradingprefs) {
            $prefs = [
                'i_first'  => '',
                'i_last'   => '',
            ];
        } else {
            $prefs = json_decode($gradingprefs, true);
        }
        $parammapping = ['tifirst' => 'i_first', 'tilast' => 'i_last'];
        $showprefwarning = false;
        $showfilterwarning = false;
        foreach ($parammapping as $paramkey => $tablekey) {
            $param = optional_param($paramkey, null, PARAM_RAW);
            if ($param != null) {
                $prefs[$tablekey] = $param;
            }
            if ($prefs[$tablekey] != '') {
                $showprefwarning = true;
            }
        }

        $workflowanchor = '';
        if ($assign->get_instance()->markingworkflow) {
            $workflowfilter = get_user_preferences('assign_workflowfilter');
            if (!empty($workflowfilter)) {
                $showfilterwarning = true;
                $workflowanchor = 'id_general';
            }
        }
        if ($assign->get_instance()->markingallocation) {
            $markerfilter = get_user_preferences('assign_markerfilter');
            if (!empty($markerfilter)) {
                $showfilterwarning = true;
                $workflowanchor = 'id_general';
            }
        }
        if ($assign->is_any_submission_plugin_enabled()) {
            $filter = get_user_preferences('assign_filter');
            if (!empty($filter)) {
                $showfilterwarning = true;
                $workflowanchor = 'id_general';
            }
        }
        if ($table->currpage > 0) {
            $showfilterwarning = true;
        }
        if ($showfilterwarning || $showprefwarning) {
            // Do I need to check which workflow states are available. Are we only concerned with releasing?
            // How do we do anything more clever than that?
            // Who sees this? Do I need to be more discriminating?

            $o .= '<span data-quercus="disable-selectall"></span>';
            $resetfilterurl = new moodle_url('/mod/assign/view.php', [
                'action' => 'grading',
                'id' => $cmid,
                'treset' => 1,
            ], $workflowanchor);
            $msg = '';
            if ($showprefwarning) {
                $msg .= get_string('assign_resetprefs', 'theme_solent', ['url' => $resetfilterurl->out()]);
            }
            if ($showfilterwarning) {
                $msg .= get_string('assign_resetworkflow', 'theme_solent', ['url' => $resetfilterurl->out()]);
            }

            $resetstring = get_string('assign_filterwarning', 'theme_solent', ['msg' => $msg]);
            $o .= $this->output->notification($resetstring, \core\notification::WARNING);
        }
        $o .= $rendered;
        // SU_AMEND_END.
        return $o;
    }
}
