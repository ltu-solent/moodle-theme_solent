<?php
namespace theme_solent\output;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use html_table;
use html_table_row;
use html_table_cell;
use moodle_url;
use assign_submission_status;
use assign_submission_plugin_submission;
use assign_attempt_history;

require_once($CFG->dirroot.'/mod/assign/renderer.php');

class mod_assign_renderer extends \mod_assign_renderer {

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

      if ($status->allowsubmissionsfromdate &&
              $time <= $status->allowsubmissionsfromdate) {
          $o .= $this->output->box_start('generalbox boxaligncenter submissionsalloweddates');
          if ($status->alwaysshowdescription) {
              $date = userdate($status->allowsubmissionsfromdate);
              $o .= get_string('allowsubmissionsfromdatesummary', 'assign', $date);
          } else {
              $date = userdate($status->allowsubmissionsfromdate);
              $o .= get_string('allowsubmissionsanddescriptionfromdatesummary', 'assign', $date);
          }
          $o .= $this->output->box_end();
      }
      $o .= $this->output->box_start('boxaligncenter submissionsummarytable');

      $t = new html_table();

      $warningmsg = '';
      if ($status->teamsubmissionenabled) {
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('submissionteam', 'assign'));
          $group = $status->submissiongroup;
          if ($group) {
              $cell2 = new html_table_cell(format_string($group->name, false, $status->context));
          } else if ($status->preventsubmissionnotingroup) {
              if (count($status->usergroups) == 0) {
                  $notification = new \core\output\notification(get_string('noteam', 'assign'), 'error');
                  $notification->set_show_closebutton(false);
                  $cell2 = new html_table_cell(
                      $this->output->render($notification)
                  );
                  $warningmsg = $this->output->notification(get_string('noteam_desc', 'assign'), 'error');
              } else if (count($status->usergroups) > 1) {
                  $notification = new \core\output\notification(get_string('multipleteams', 'assign'), 'error');
                  $notification->set_show_closebutton(false);
                  $cell2 = new html_table_cell(
                      $this->output->render($notification)
                  );
                  $warningmsg = $this->output->notification(get_string('multipleteams_desc', 'assign'), 'error');
              }
          } else {
              $cell2 = new html_table_cell(get_string('defaultteam', 'assign'));
          }
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
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

          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('attemptnumber', 'assign'));
          $maxattempts = $status->maxattempts;
          if ($maxattempts == ASSIGN_UNLIMITED_ATTEMPTS) {
              $message = get_string('currentattempt', 'assign', $currentattempt);
          } else {
              $message = get_string('currentattemptof', 'assign', array('attemptnumber'=>$currentattempt,
                                                                        'maxattempts'=>$maxattempts));
          }
          $cell2 = new html_table_cell($message);
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

      $row = new html_table_row();
      $cell1 = new html_table_cell(get_string('submissionstatus', 'assign'));
      if (!$status->teamsubmissionenabled) {
          if ($status->submission && $status->submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
              $statusstr = get_string('submissionstatus_' . $status->submission->status, 'assign');
              $cell2 = new html_table_cell($statusstr);
              $cell2->attributes = array('class'=>'submissionstatus' . $status->submission->status);
          } else {
              if (!$status->submissionsenabled) {
                  $cell2 = new html_table_cell(get_string('noonlinesubmissions', 'assign'));
              } else {
                  $cell2 = new html_table_cell(get_string('noattempt', 'assign'));
              }
          }
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      } else {
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('submissionstatus', 'assign'));
          $group = $status->submissiongroup;
          if (!$group && $status->preventsubmissionnotingroup) {
              $cell2 = new html_table_cell(get_string('nosubmission', 'assign'));
          } else if ($status->teamsubmission && $status->teamsubmission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
              $teamstatus = $status->teamsubmission->status;
              $submissionsummary = get_string('submissionstatus_' . $teamstatus, 'assign');
              $groupid = 0;
              if ($status->submissiongroup) {
                  $groupid = $status->submissiongroup->id;
              }

              $members = $status->submissiongroupmemberswhoneedtosubmit;
              $userslist = array();
              foreach ($members as $member) {
                  $urlparams = array('id' => $member->id, 'course'=>$status->courseid);
                  $url = new moodle_url('/user/view.php', $urlparams);
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
                  $submissionsummary .= $this->output->container($formatteduserstr);
              }

              $cell2 = new html_table_cell($submissionsummary);
              $cell2->attributes = array('class'=>'submissionstatus' . $status->teamsubmission->status);
          } else {
              $cell2 = new html_table_cell(get_string('nosubmission', 'assign'));
              if (!$status->submissionsenabled) {
                  $cell2 = new html_table_cell(get_string('noonlinesubmissions', 'assign'));
              } else {
                  $cell2 = new html_table_cell(get_string('nosubmission', 'assign'));
              }
          }
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

      // Is locked?
      if ($status->locked) {
          $row = new html_table_row();
          $cell1 = new html_table_cell();
          $cell2 = new html_table_cell(get_string('submissionslocked', 'assign'));
          $cell2->attributes = array('class'=>'submissionlocked');
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

      // Grading status.
      $row = new html_table_row();
      $cell1 = new html_table_cell(get_string('gradingstatus', 'assign'));

      if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
          $status->gradingstatus == ASSIGN_GRADING_STATUS_NOT_GRADED) {
          $cell2 = new html_table_cell(get_string($status->gradingstatus, 'assign'));
      } else {
          $gradingstatus = 'markingworkflowstate' . $status->gradingstatus;
          $cell2 = new html_table_cell(get_string($gradingstatus, 'assign'));
      }
      if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
          $status->gradingstatus == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
          $cell2->attributes = array('class' => 'submissiongraded');
      } else {
          $cell2->attributes = array('class' => 'submissionnotgraded');
      }
      $row->cells = array($cell1, $cell2);
      $t->data[] = $row;

      $submission = $status->teamsubmission ? $status->teamsubmission : $status->submission;
      $duedate = $status->duedate;
      if ($duedate > 0) {
          // Due date.
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('duedate', 'assign'));
          $cell2 = new html_table_cell(userdate($duedate));
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;

          if ($status->view == assign_submission_status::GRADER_VIEW) {
              if ($status->cutoffdate) {
                  // Cut off date.
                  $row = new html_table_row();
                  $cell1 = new html_table_cell(get_string('cutoffdate', 'assign'));
                  $cell2 = new html_table_cell(userdate($status->cutoffdate));
                  $row->cells = array($cell1, $cell2);
                  $t->data[] = $row;
              }
          }


          if ($status->extensionduedate) {
              // Extension date.
              $row = new html_table_row();
              $cell1 = new html_table_cell(get_string('extensionduedate', 'assign'));
              $cell2 = new html_table_cell(userdate($status->extensionduedate));
              $row->cells = array($cell1, $cell2);
              $t->data[] = $row;
              $duedate = $status->extensionduedate;
          }

          // Time remaining.
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('timeremaining', 'assign'));
          if ($duedate - $time <= 0) {
              if (!$submission ||
                      $submission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                  if ($status->submissionsenabled) {
                      $overduestr = get_string('overdue', 'assign', format_time($time - $duedate));
                      $cell2 = new html_table_cell($overduestr);
                      $cell2->attributes = array('class'=>'overdue');
                  } else {
                      $cell2 = new html_table_cell(get_string('duedatereached', 'assign'));
                  }
              } else {
                  if ($submission->timemodified > $duedate) {
                      $latestr = get_string('submittedlate',
                                            'assign',
                                            format_time($submission->timemodified - $duedate));
                      $cell2 = new html_table_cell($latestr);
                      $cell2->attributes = array('class'=>'latesubmission');
                  } else {
                      $earlystr = get_string('submittedearly',
                                             'assign',
                                             format_time($submission->timemodified - $duedate));
                      $cell2 = new html_table_cell($earlystr);
                      $cell2->attributes = array('class'=>'earlysubmission');
                  }
              }
          } else {
              $cell2 = new html_table_cell(format_time($duedate - $time));
          }
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

// SU_AMEND START - Assignment: Cut off date/time remaining in submission status
    if($status->view == assign_submission_status::STUDENT_VIEW){
      $cutoffdate = 0;
        $cutoffdate = $status->cutoffdate;
        if($cutoffdate){
          if($cutoffdate > $status->duedate){
            $row = new html_table_row();
            $cell1c = new html_table_cell(get_string('latesubmissions', 'assign'));
            $late = get_string('latesubmissionsaccepted', 'assign', userdate($status->cutoffdate));
            $cell2c = new html_table_cell($late);
            $row->cells = array($cell1c, $cell2c);
            $t->data[] = $row;
          }
        }
    }
// SU_AMEND END

      // Show graders whether this submission is editable by students.
      if ($status->view == assign_submission_status::GRADER_VIEW) {
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('editingstatus', 'assign'));
          if ($status->canedit) {
              $cell2 = new html_table_cell(get_string('submissioneditable', 'assign'));
              $cell2->attributes = array('class'=>'submissioneditable');
          } else {
              $cell2 = new html_table_cell(get_string('submissionnoteditable', 'assign'));
              $cell2->attributes = array('class'=>'submissionnoteditable');
          }
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

      // Grading criteria preview.
      if (!empty($status->gradingcontrollerpreview)) {
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('gradingmethodpreview', 'assign'));
          $cell2 = new html_table_cell($status->gradingcontrollerpreview);
          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;
      }

      // Last modified.
      if ($submission) {
          $row = new html_table_row();
          $cell1 = new html_table_cell(get_string('timemodified', 'assign'));

          if ($submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
// SU_AMEND START - Assignment: Show seconds for submission time
              //$cell2 = new html_table_cell(userdate($submission->timemodified));
              $cell2 = new html_table_cell(userdate($submission->timemodified, '%d %B %Y, %I:%M:%S %p'));
// SU_AMEND END
          } else {
              $cell2 = new html_table_cell('-');
          }

          $row->cells = array($cell1, $cell2);
          $t->data[] = $row;

          if (!$status->teamsubmission || $status->submissiongroup != false || !$status->preventsubmissionnotingroup) {
              foreach ($status->submissionplugins as $plugin) {
                  $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                  if ($plugin->is_enabled() &&
                      $plugin->is_visible() &&
                      $plugin->has_user_summary() &&
                      $pluginshowsummary
                  ) {

                      $row = new html_table_row();
                      $cell1 = new html_table_cell($plugin->get_name());
                      $displaymode = assign_submission_plugin_submission::SUMMARY;
                      $pluginsubmission = new assign_submission_plugin_submission($plugin,
                          $submission,
                          $displaymode,
                          $status->coursemoduleid,
                          $status->returnaction,
                          $status->returnparams);
                      $cell2 = new html_table_cell($this->render($pluginsubmission));
                      $row->cells = array($cell1, $cell2);
                      $t->data[] = $row;
                  }
              }
          }
      }

      $o .= $warningmsg;
      $o .= html_writer::table($t);
      $o .= $this->output->box_end();

      // Links.
      if ($status->view == assign_submission_status::STUDENT_VIEW) {
          if ($status->canedit) {
              if (!$submission || $submission->status == ASSIGN_SUBMISSION_STATUS_NEW) {
                  $o .= $this->output->box_start('generalbox submissionaction');
                  $urlparams = array('id' => $status->coursemoduleid, 'action' => 'editsubmission');
// SU_AMEND START - Assignment: Submission help string position (add)
                  //New string position
                  $o .= get_string('editsubmission_help', 'assign');
// SU_AMEND END
                  $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php', $urlparams),
                                                     get_string('addsubmission', 'assign'), 'get');
                  $o .= $this->output->box_start('boxaligncenter submithelp');
// SU_AMEND START - Assignment: Submission help string position (add)
                  // Old help string positions
                  //$o .= get_string('addsubmission_help', 'assign');
// SU_AMEND END
                  $o .= $this->output->box_end();
              } else if ($submission->status == ASSIGN_SUBMISSION_STATUS_REOPENED) {
                  $o .= $this->output->box_start('generalbox submissionaction');
                  $urlparams = array('id' => $status->coursemoduleid,
                                     'action' => 'editprevioussubmission',
                                     'sesskey'=>sesskey());
                  $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php', $urlparams),
                                                     get_string('addnewattemptfromprevious', 'assign'), 'get');
                  $o .= $this->output->box_start('boxaligncenter submithelp');
                  $o .= get_string('addnewattemptfromprevious_help', 'assign');
                  $o .= $this->output->box_end();
                  $o .= $this->output->box_end();
                  $o .= $this->output->box_start('generalbox submissionaction');
                  $urlparams = array('id' => $status->coursemoduleid, 'action' => 'editsubmission');
                  $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php', $urlparams),
                                                     get_string('addnewattempt', 'assign'), 'get');
                  $o .= $this->output->box_start('boxaligncenter submithelp');
                  $o .= get_string('addnewattempt_help', 'assign');
                  $o .= $this->output->box_end();
                  $o .= $this->output->box_end();
              } else {
                  $o .= $this->output->box_start('generalbox submissionaction');
                  $urlparams = array('id' => $status->coursemoduleid, 'action' => 'editsubmission');
// SU_AMEND START - Assignment: Submission help string position (edit)
                  //New string position
                  global $DB;
                  $file = $DB->get_record_sql("SELECT cm.instance
                      FROM {assign} a
                      JOIN {course_modules} cm ON cm.instance = a.id
                      JOIN {assign_plugin_config} pc ON pc.assignment = a.id
                      WHERE (pc.plugin = 'file' AND pc.subtype = 'assignsubmission' AND pc.name = 'enabled')
                      AND pc.value = 1
                      AND cm.id = ?", array($status->coursemoduleid));
                  if($file){
                    $o .= $this->output->box_start('boxaligncenter submithelp');
                    $o .= get_string('editsubmission_help', 'assign');
                    $o .= $this->output->box_end();
                  }
// SU_AMEND END
                  $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php', $urlparams),
                                                     get_string('editsubmission', 'assign'), 'get');
                  $o .= $this->output->box_start('boxaligncenter submithelp');
// SU_AMEND START - Assignment: Submission help string position (edit)
                  // Old string position
                  // $o .= $this->output->box_start('boxaligncenter submithelp');
                  // $o .= get_string('editsubmission_help', 'assign');
                  // $o .= $this->output->box_end();
// SU_AMEND END
                  $o .= $this->output->box_end();
              }
          }

          if ($status->cansubmit) {
              $urlparams = array('id' => $status->coursemoduleid, 'action'=>'submit');
              $o .= $this->output->box_start('generalbox submissionaction');
// SU_AMEND START - Assignment: Submission help string position (submit)
              //New string position
              if($file){
                $o .= $this->output->box_start('boxaligncenter submithelp');
                $o .= get_string('submitassignment_help', 'assign');
                $o .= $this->output->box_end();
              }
// SU_AMEND END
              $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php', $urlparams),
                                                 get_string('submitassignment', 'assign'), 'get');
              $o .= $this->output->box_start('boxaligncenter submithelp');
// SU_AMEND START - Assignment: Submission help string position (submit)
              // Old string position
              // $o .= $this->output->box_start('boxaligncenter submithelp');
              // $o .= get_string('submitassignment_help', 'assign');
              // $o .= $this->output->box_end();
// SU_AMEND END
              $o .= $this->output->box_end();
          }
      }

      $o .= $this->output->container_end();
      return $o;
  }


  /**
   * Output the attempt history for this assignment
   *
   * @param assign_attempt_history $history
   * @return string
   */
  public function render_assign_attempt_history(assign_attempt_history $history) {
      $o = '';

      $submittedstr = get_string('submitted', 'assign');
      $gradestr = get_string('grade');
      $gradedonstr = get_string('gradedon', 'assign');
      $gradedbystr = get_string('gradedby', 'assign');

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

          $editbtn = '';

          if ($submission) {
// SU_AMEND START - Assignment: Show seconds for submission time
              //$submissionsummary = userdate($submission->timemodified);
              $submissionsummary = userdate($submission->timemodified, '%d %B %Y, %I:%M:%S %p');
// SU_AMEND END
          } else {
              $submissionsummary = get_string('nosubmission', 'assign');
          }

          $attemptsummaryparams = array('attemptnumber'=>$submission->attemptnumber+1,
                                        'submissionsummary'=>$submissionsummary);
          $o .= $this->heading(get_string('attemptheading', 'assign', $attemptsummaryparams), 4);

          $t = new html_table();

          if ($submission) {
              $cell1 = new html_table_cell(get_string('submissionstatus', 'assign'));
              $cell2 = new html_table_cell(get_string('submissionstatus_' . $submission->status, 'assign'));
              $t->data[] = new html_table_row(array($cell1, $cell2));

              foreach ($history->submissionplugins as $plugin) {
                  $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                  if ($plugin->is_enabled() &&
                          $plugin->is_visible() &&
                          $plugin->has_user_summary() &&
                          $pluginshowsummary) {

                      $cell1 = new html_table_cell($plugin->get_name());
                      $pluginsubmission = new assign_submission_plugin_submission($plugin,
                                                                                  $submission,
                                                                                  assign_submission_plugin_submission::SUMMARY,
                                                                                  $history->coursemoduleid,
                                                                                  $history->returnaction,
                                                                                  $history->returnparams);
                      $cell2 = new html_table_cell($this->render($pluginsubmission));

                      $t->data[] = new html_table_row(array($cell1, $cell2));
                  }
              }
          }

          if ($grade) {
              // Heading 'feedback'.
              $title = get_string('feedback', 'assign', $i);
              $title .= $this->output->spacer(array('width'=>10));
              if ($history->cangrade) {
                  // Edit previous feedback.
                  $returnparams = http_build_query($history->returnparams);
                  $urlparams = array('id' => $history->coursemoduleid,
                                 'rownum'=>$history->rownum,
                                 'useridlistid'=>$history->useridlistid,
                                 'attemptnumber'=>$grade->attemptnumber,
                                 'action'=>'grade',
                                 'returnaction'=>$history->returnaction,
                                 'returnparams'=>$returnparams);
                  $url = new moodle_url('/mod/assign/view.php', $urlparams);
                  $icon = new pix_icon('gradefeedback',
                                          get_string('editattemptfeedback', 'assign', $grade->attemptnumber+1),
                                          'mod_assign');
                  $title .= $this->output->action_icon($url, $icon);
              }
              $cell = new html_table_cell($title);
              $cell->attributes['class'] = 'feedbacktitle';
              $cell->colspan = 2;
              $t->data[] = new html_table_row(array($cell));

              // Grade.
              $cell1 = new html_table_cell($gradestr);
              $cell2 = $grade->gradefordisplay;
              $t->data[] = new html_table_row(array($cell1, $cell2));

              // Graded on.
              $cell1 = new html_table_cell($gradedonstr);
              $cell2 = new html_table_cell(userdate($grade->timemodified));
              $t->data[] = new html_table_row(array($cell1, $cell2));

              // Graded by set to a real user. Not set can be empty or -1.
              if (!empty($grade->grader) && is_object($grade->grader)) {
                  $cell1 = new html_table_cell($gradedbystr);
                  $cell2 = new html_table_cell($this->output->user_picture($grade->grader) .
                                               $this->output->spacer(array('width' => 30)) . fullname($grade->grader));
                  $t->data[] = new html_table_row(array($cell1, $cell2));
              }

              // Feedback from plugins.
              foreach ($history->feedbackplugins as $plugin) {
                  if ($plugin->is_enabled() &&
                      $plugin->is_visible() &&
                      $plugin->has_user_summary() &&
                      !$plugin->is_empty($grade)) {

                      $cell1 = new html_table_cell($plugin->get_name());
                      $pluginfeedback = new assign_feedback_plugin_feedback(
                          $plugin, $grade, assign_feedback_plugin_feedback::SUMMARY, $history->coursemoduleid,
                          $history->returnaction, $history->returnparams
                      );
                      $cell2 = new html_table_cell($this->render($pluginfeedback));
                      $t->data[] = new html_table_row(array($cell1, $cell2));
                  }

              }

          }

          $o .= html_writer::table($t);
      }
      $o .= $this->box_end();
      $jsparams = array($containerid);

      $this->page->requires->yui_module('moodle-mod_assign-history', 'Y.one("#' . $containerid . '").history');

      return $o;
  }

}
