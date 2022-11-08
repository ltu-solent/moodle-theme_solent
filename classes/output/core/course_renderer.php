<?php
namespace theme_solent\output\core;

defined('MOODLE_INTERNAL') || die();

use context_course;
use stdClass;
use html_writer;
use moodle_url;
use tabobject;
use completion_info;
use format_onetopic;
use action_link;
use confirm_action;
use core_course_category;
use cm_info;
use coursecat_helper;
use core_course_list_element;

require_once($CFG->dirroot.'/course/renderer.php');

class course_renderer extends \core_course_renderer {

  /**
   * Displays one course in the list of courses.
   *
   * This is an internal function, to display an information about just one course
   * please use {@link core_course_renderer::course_info_box()}
   *
   * @param coursecat_helper $chelper various display options
   * @param core_course_list_element|stdClass $course
   * @param string $additionalclasses additional classes to add to the main <div> tag (usually
   *    depend on the course position in list - first/last/even/odd)
   * @return string
   */
  protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
      if (!isset($this->strings->summary)) {
          $this->strings->summary = get_string('summary');
      }
      if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
          return '';
      }
      if ($course instanceof stdClass) {
          $course = new core_course_list_element($course);
      }
      $content = '';
      $classes = trim('coursebox clearfix '. $additionalclasses);
      if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
          $classes .= ' collapsed';
      }

      // .coursebox
      $content .= html_writer::start_tag('div', array(
          'class' => $classes,
          'data-courseid' => $course->id,
          'data-type' => self::COURSECAT_TYPE_COURSE,
      ));

      $content .= html_writer::start_tag('div', array('class' => 'info'));
      $content .= $this->course_name($chelper, $course);
// SU_AMEND START - Unit descriptor: Search results
      $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $content .= '<span class="solent_startdate_search">' .  unit_descriptor_course($course) . '</span>';
// SU_AMEND END
      $content .= $this->course_enrolment_icons($course);
      $content .= html_writer::end_tag('div');

      $content .= html_writer::start_tag('div', array('class' => 'content'));
      $content .= $this->coursecat_coursebox_content($chelper, $course);
      $content .= html_writer::end_tag('div');

      $content .= html_writer::end_tag('div'); // .coursebox
      return $content;
  }

}
