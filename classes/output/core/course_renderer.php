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
   * Renders html to display a name with the link to the course module on a course page
   *
   * If module is unavailable for user but still needs to be displayed
   * in the list, just the name is returned without a link
   *
   * Note, that for course modules that never have separate pages (i.e. labels)
   * this function return an empty string
   *
   * @param cm_info $mod
   * @param array $displayoptions
   * @return string
   */
  public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
    return parent::course_section_cm_name($mod, $displayoptions);
      if (!$mod->is_visible_on_course_page() || !$mod->url) {
          // Nothing to be displayed to the user.
          return '';
      }

      list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);
      $groupinglabel = $mod->get_grouping_label($textclasses);

      // Render element that allows to edit activity name inline. It calls {@link course_section_cm_name_title()}
      // to get the display title of the activity.
      $tmpl = new \core_course\output\course_module_name($mod, $this->page->user_is_editing(), $displayoptions);
// SU_AMEND START - Marks upload: Prevent quick edit of assignment name
      // return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output)) .
      //     $groupinglabel;
      if($mod->modname == 'assign' && $mod->idnumber){
        return $this->output->render_from_template('theme_solent/inplace_non_editable', $tmpl->export_for_template($this->output)) .
        $groupinglabel;
      }else{
        return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output)) .
        $groupinglabel;
      }
// SU_AMEND END
  }

  /**
   * Returns the CSS classes for the activity name/content
   *
   * For items which are hidden, unavailable or stealth but should be displayed
   * to current user ($mod->is_visible_on_course_page()), we show those as dimmed.
   * Students will also see as dimmed activities names that are not yet available
   * but should still be displayed (without link) with availability info.
   *
   * @param cm_info $mod
   * @return array array of two elements ($linkclasses, $textclasses)
   */
  protected function course_section_cm_classes(cm_info $mod) {
      $linkclasses = '';
      $textclasses = '';
      if ($mod->uservisible) {
          $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
          $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
              has_capability('moodle/course:viewhiddenactivities', $mod->context);
          if ($accessiblebutdim) {
              $linkclasses .= ' dimmed';
              $textclasses .= ' dimmed_text';
              if ($conditionalhidden) {
                  $linkclasses .= ' conditionalhidden';
                  $textclasses .= ' conditionalhidden';
              }
          }
          if ($mod->is_stealth()) {
              // Stealth activity is the one that is not visible on course page.
              // It still may be displayed to the users who can manage it.
              $linkclasses .= ' stealth';
              $textclasses .= ' stealth';
          }
      } else {
          $linkclasses .= ' dimmed';
          $textclasses .= ' dimmed dimmed_text';
      }
      return array($linkclasses, $textclasses);
// SU_AMEND START - Marks upload: Prevent quick edit of assignment name
      if($mod->modname == 'assign' && $mod->idnumber){
        return $this->output->render_from_template('theme/solent/inplace_non_editable', $tmpl->export_for_template($this->output));
      }else{
        return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output));
      }
// SU_AMEND END
  }

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
