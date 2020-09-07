<?php
namespace theme_solent\output\core;

defined('MOODLE_INTERNAL') || die();

use import_course_search;
use html_writer;
use restore_course_search;
use html_table;
use html_table_row;
use html_table_cell;
use context_course;

require_once($CFG->dirroot.'/backup/util/ui/renderer.php');

class backup_renderer extends \core_backup_renderer {

  /**
   * Renders an import course search object
   *
   * @param import_course_search $component
   * @return string
   */
  public function render_import_course_search(import_course_search $component) {
      $url = $component->get_url();

      $output = html_writer::start_tag('div', array('class' => 'import-course-search'));
      if ($component->get_count() === 0) {
          $output .= $this->output->notification(get_string('nomatchingcourses', 'backup'));

          $output .= html_writer::start_tag('div', array('class' => 'ics-search'));
          $attrs = array(
              'type' => 'text',
              'name' => restore_course_search::$VAR_SEARCH,
              'value' => $component->get_search(),
              'class' => 'form-control'
          );
          $output .= html_writer::empty_tag('input', $attrs);
          $attrs = array(
              'type' => 'submit',
              'name' => 'searchcourses',
              'value' => get_string('search'),
              'class' => 'btn btn-secondary'
          );
          $output .= html_writer::empty_tag('input', $attrs);
          $output .= html_writer::end_tag('div');

          $output .= html_writer::end_tag('div');
          return $output;
      }

      $countstr = '';
      if ($component->has_more_results()) {
          $countstr = get_string('morecoursesearchresults', 'backup', $component->get_count());
      } else {
          $countstr = get_string('totalcoursesearchresults', 'backup', $component->get_count());
      }

      $output .= html_writer::tag('div', $countstr, array('class' => 'ics-totalresults'));
      $output .= html_writer::start_tag('div', array('class' => 'ics-results'));

      $table = new html_table();
      $table->head = array('', get_string('shortnamecourse'), get_string('fullnamecourse'));
      $table->data = array();
      foreach ($component->get_results() as $course) {
  // SU_AMEND START - Unit start date: Course import
      global $DB;
      $category = $DB->get_record_sql('SELECT cc.idnumber FROM {course_categories} cc JOIN {course} c ON c.category = cc.id WHERE c.id = ?', array($course->id));
      $getcourse = get_course($course->id);

      if(isset($category)){
        $catname = strtolower('x'.$category->idnumber);

        if(strpos($catname, 'modules_') !== false){
          $startdate = ' - Start date: ' . date('d-m-Y', $getcourse->startdate);
        }else{
          $startdate = '';
        }
      }
  // SU_AMEND END
          $row = new html_table_row();
          $row->attributes['class'] = 'ics-course';
          if (!$course->visible) {
              $row->attributes['class'] .= ' dimmed';
          }
          $row->cells = array(
              html_writer::empty_tag('input', array('type' => 'radio', 'name' => 'importid', 'value' => $course->id)),
              format_string($course->shortname, true, array('context' => context_course::instance($course->id))),
  // SU_AMEND START - Unit start date: Course import
              //format_string($course->fullname, true, array('context' => context_course::instance($course->id)))
              format_string($course->fullname . $startdate, true, array('context' => context_course::instance($course->id)))
  // SU_AMEND END
          );
          $table->data[] = $row;
      }
      if ($component->has_more_results()) {
          $cell = new html_table_cell(get_string('moreresults', 'backup'));
          $cell->colspan = 3;
          $cell->attributes['class'] = 'notifyproblem';
          $row = new html_table_row(array($cell));
          $row->attributes['class'] = 'rcs-course';
          $table->data[] = $row;
      }
      $output .= html_writer::table($table);
      $output .= html_writer::end_tag('div');

      $output .= html_writer::start_tag('div', array('class' => 'ics-search'));
      $attrs = array(
          'type' => 'text',
          'name' => restore_course_search::$VAR_SEARCH,
          'value' => $component->get_search(),
          'class' => 'form-control');
      $output .= html_writer::empty_tag('input', $attrs);
      $attrs = array(
          'type' => 'submit',
          'name' => 'searchcourses',
          'value' => get_string('search'),
          'class' => 'btn btn-secondary'
      );
      $output .= html_writer::empty_tag('input', $attrs);
      $output .= html_writer::end_tag('div');

      $output .= html_writer::end_tag('div');
      return $output;
  }
}
