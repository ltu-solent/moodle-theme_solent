<?php
namespace theme_solent\output;

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

require_once($CFG->dirroot.'/course/format/onetopic/renderer.php');
require_once($CFG->dirroot.'/course/format/renderer.php');

class format_onetopic_renderer extends \format_onetopic_renderer {

  /**
   * Output the html for a single section page .
   *
   * @param stdClass $course The course entry from DB
   * @param array $sections The course_sections entries from the DB
   * @param array $mods used for print_section()
   * @param array $modnames used for print_section()
   * @param array $modnamesused used for print_section()
   * @param int $displaysection The section number in the course which is being displayed
   */   
	public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {

        $realcoursedisplay = $course->realcoursedisplay;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $course->realcoursedisplay = $realcoursedisplay;

        if (!$sections) {
            $sections = $modinfo->get_section_info_all();
        }

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $formatdata = new stdClass();
        $formatdata->mods = $mods;
        $formatdata->modinfo = $modinfo;
        $this->_course = $course;
        $this->_format_data = $formatdata;

        // General section if non-empty and course_display is multiple.
        if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $thissection = $sections[0];
            if ((($thissection->visible && $thissection->available) || $canviewhidden) &&
                    ($thissection->summary || $thissection->sequence || $this->page->user_is_editing() ||
                    (string)$thissection->name !== '')) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true);

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);

                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        $cssclass = 'single-section onetopic';
        $cssclass .= $this->_course->tabsview == format_onetopic::TABSVIEW_VERTICAL ? ' verticaltabs' : '';
        echo html_writer::start_tag('div', array('class' => $cssclass));

        // Move controls.
        $canmove = false;
        if ($this->page->user_is_editing() && has_capability('moodle/course:movesections', $context) && $displaysection > 0) {
            $canmove = true;
        }
        $movelisthtml = '';

        // Init custom tabs.
        $section = 0;

        $tabs = array();
        $inactivetabs = array();

        while ($section <= $this->numsections) {

            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

			$thissection = $sections[$section];

			$showsection = true;
			if (!$thissection->visible || !$thissection->available) {
				$showsection = $canviewhidden || !($course->hiddensections == 1);
			}

            if ($showsection) {

                $formatoptions = course_get_format($course)->get_format_options($thissection);

                $sectionname = get_section_name($course, $thissection);

                $customstyles = '';
                $level = 0;
                if (is_array($formatoptions)) {

// SU_AMEND START - Course: Pre-selected tab colours
					// if (!empty($formatoptions['fontcolor'])) {
					//     $customstyles .= 'color: ' . $formatoptions['fontcolor'] . ';';
					// }
					if (!empty($formatoptions['bgcolor'])) {
						$customstyles .= 'color: ' . '#FFFFFF' . ';'; //change it back to the default colour
						$customstyles .= 'border: 1px solid ' . $formatoptions['bgcolor'] .';';
					}else{
						$customstyles .= ''; //remove color
					}
// SU_AMEND END
					if (!empty($formatoptions['fontcolor'])) {
                        $customstyles .= 'color: ' . $formatoptions['fontcolor'] . ';';
                    }

                    if (!empty($formatoptions['bgcolor'])) {
                        $customstyles .= 'background-color: ' . $formatoptions['bgcolor'] . ';';
                    }

                    if (!empty($formatoptions['cssstyles'])) {
                        $customstyles .= $formatoptions['cssstyles'] . ';';
                    }

                    if (isset($formatoptions['level'])) {
                        $level = $formatoptions['level'];
                    }
                }

                if ($section == 0) {
                    $url = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => 0));
                } else {
                    $url = course_get_url($course, $section);
                }

                $specialstyle = 'tab_position_' . $section . ' tab_level_' . $level;
                if ($course->marker == $section) {
                    $specialstyle = ' marker ';
                }

                if (!$thissection->visible || !$thissection->available) {
                    $specialstyle .= ' dimmed ';

                    if (!$canviewhidden) {
                        $inactivetabs[] = "tab_topic_" . $section;
                    }
                }
				
				// Check if display available message is required.
                $availablemessage = '';
                if ($course->hiddensections == 2) {
                    $availabilitytext = $this->section_availability_message($thissection,
                        has_capability('moodle/course:viewhiddensections', $context));

                    if (!empty($availabilitytext)) {
                        $uniqueid = 'format_onetopic_winfo_' . time() . '-' . rand(0, 1000);
                        $availablemessage = '<span class="iconhelp" data-infoid="' . $uniqueid . '">' .
                                            $this->output->pix_icon('e/help', get_string('info')) .
                                        '</span>';

                        $availablemessage .= '<div id="' . $uniqueid . '" class="availability_info_box" style="display: none;">' .
                            $availabilitytext . '</div>';

                        $this->showyuidialogue = true;
                    }
                }

                $newtab = new tabobject("tab_topic_" . $section, $url . '#tabs-tree-start',
                '<innertab style="' . $customstyles . '" class="tab_content ' . $specialstyle . '">' .
                '<span class="sectionname">' . $sectionname . "</span>" . $availablemessage . "</innertab>", $sectionname);

                if (is_array($formatoptions) && isset($formatoptions['level'])) {

                    if ($formatoptions['level'] == 0 || count($tabs) == 0) {
                        $tabs[] = $newtab;
                        $newtab->level = 1;
                    } else {
                        $parentindex = count($tabs) - 1;
                        if (!is_array($tabs[$parentindex]->subtree)) {
                            $tabs[$parentindex]->subtree = array();
                        } else if (count($tabs[$parentindex]->subtree) == 0) {
                            $tabs[$parentindex]->subtree[0] = clone($tabs[$parentindex]);
                            $tabs[$parentindex]->subtree[0]->id .= '_index';

                            $prevsectionindex = $section - 1;
                            do {
                                $parentsection = $sections[$prevsectionindex];
                                $parentformatoptions = course_get_format($course)->get_format_options($parentsection);
                                $prevsectionindex--;
                            } while ($parentformatoptions['level'] == 1 && $prevsectionindex >= 0);

                            if ($parentformatoptions['firsttabtext']) {
                                $firsttabtext = $parentformatoptions['firsttabtext'];
                            } else {
                                $firsttabtext = get_string('index', 'format_onetopic');
                            }
                            $tabs[$parentindex]->subtree[0]->text = '<innertab class="tab_content tab_initial">' .
                                                                    $firsttabtext . "</innertab>";
                            $tabs[$parentindex]->subtree[0]->level = 2;

                            if ($displaysection == $section - 1) {
                                $tabs[$parentindex]->subtree[0]->selected = true;
                            }
                        }
                        $newtab->level = 2;
                        $tabs[$parentindex]->subtree[] = $newtab;
                    }
                } else {
                    $tabs[] = $newtab;
                }

                // Init move section list.
                if ($canmove) {
                    if ($section > 0) { // Move section.
                        $baseurl = course_get_url($course, $displaysection);
                        $baseurl->param('sesskey', sesskey());

                        $url = clone($baseurl);

                        $url->param('move', $section - $displaysection);

                        // Define class from sublevels in order to move a margen in the left.
                        // Not apply if it is the first element (condition !empty($movelisthtml))
                        // because the first element can't be a sublevel.
                        $liclass = '';
                        if (is_array($formatoptions) && isset($formatoptions['level']) && $formatoptions['level'] > 0 &&
                                !empty($movelisthtml)) {
                            $liclass = 'sublevel';
                        }

						if ($displaysection != $section) {
// SU_AMEND START - Course: Prevent anyone except admins moving default sections
                          //$movelisthtml .= html_writer::tag('li', html_writer::link($url, $sectionname),
                                          //array('class' => $liclass));
                          $category = core_course_category::get($course->category, IGNORE_MISSING);
                          $catname = strtolower('x'.$category->idnumber);
                          if(strpos($catname, 'modules_') !== false){
                            if($section > 4 && $displaysection > 4 || is_siteadmin()){
                              $movelisthtml .= html_writer::tag('li', html_writer::link($url, $sectionname),
                                      array('class' => $liclass));
                            }else{
                              $movelisthtml .= html_writer::tag('li', $sectionname, array('class' => $liclass));
                            }
                          }else{
                            $movelisthtml .= html_writer::tag('li', html_writer::link($url, $sectionname),
                                                          array('class' => $liclass));
                          }
// SU_AMEND END
                      } else {
                            $movelisthtml .= html_writer::tag('li', $sectionname, array('class' => $liclass));
                        }
                    } else {
                        $movelisthtml .= html_writer::tag('li', $sectionname);
                    }
                }
                // End move section list.
			}

			$section++;
		}

      // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);

        if ($this->page->user_is_editing() && has_capability('moodle/course:update', $context)) {

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                    'increase' => true,
                    'sesskey' => sesskey(),
                    'insertsection' => 0));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            $tabs[] = new tabobject("tab_topic_add", $url, $icon, s($straddsection));

        }

        $hiddenmsg = course_get_format($course)->get_hidden_message();
        if (!empty($hiddenmsg)) {
            echo $this->output->notification($hiddenmsg);
        }

        if ($this->page->user_is_editing() || (!$course->hidetabsbar && count($tabs) > 0)) {
            echo html_writer::tag('a', '', array('name' => 'tabs-tree-start'));
            echo $this->output->tabtree($tabs, "tab_topic_" . $displaysection, $inactivetabs);
        }

        // Start content div.
        echo html_writer::start_tag('div', array('class' => 'content-section'));

		if ($sections[$displaysection]->uservisible || $canviewhidden) {

            if ($course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE || $displaysection !== 0) {
                // Now the list of sections.
                echo $this->start_section_list();

                // The requested section page.
                $thissection = $sections[$displaysection];
                echo $this->section_header($thissection, $course, true);
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $completioninfo->display_help_icon();

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->page->user_is_editing() || $this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

		// Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close content-section div.
        echo html_writer::end_tag('div');

        // Close single-section div.
        echo html_writer::end_tag('div');

        if ($this->page->user_is_editing() && has_capability('moodle/course:update', $context)) {

            echo '<br class="utilities-separator" />';
// SU_AMEND START - Course: Expand section editing panel
          // print_collapsible_region_start('move-list-box clearfix collapsible mform', 'course_format_onetopic_config_movesection',
          //     get_string('utilities', 'format_onetopic'), '', true);
          print_collapsible_region_start('move-list-box clearfix collapsible mform', 'course_format_onetopic_config_movesection',
              get_string('utilities', 'format_onetopic'), '', false);
//SU_AMEND END
            // Move controls.
            if ($canmove && !empty($movelisthtml)) {
                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('movesectionto', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo html_writer::tag('ul', $movelisthtml, array('class' => 'move-list'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('movesectionto_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            $baseurl = course_get_url($course, $displaysection);
            $baseurl->param('sesskey', sesskey());

            $url = clone($baseurl);

            global $USER;
            if (isset($USER->onetopic_da[$course->id]) && $USER->onetopic_da[$course->id]) {
                $url->param('onetopic_da', 0);
                $textbuttondisableajax = get_string('enable', 'format_onetopic');
            } else {
                $url->param('onetopic_da', 1);
                $textbuttondisableajax = get_string('disable', 'format_onetopic');
            }

            echo html_writer::start_div("form-item clearfix");
                echo html_writer::start_div("form-label");
                    echo html_writer::tag('label', get_string('disableajax', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-setting");
                    echo html_writer::link($url, $textbuttondisableajax);
                echo html_writer::end_div();
                echo html_writer::start_div("form-description");
                    echo html_writer::tag('p', get_string('disableajax_help', 'format_onetopic'));
                echo html_writer::end_div();
            echo html_writer::end_div();

            // Duplicate current section option.
            if (has_capability('moodle/course:manageactivities', $context)) {
                $urlduplicate = new moodle_url('/course/format/onetopic/duplicate.php',
                                array('courseid' => $course->id, 'section' => $displaysection, 'sesskey' => sesskey()));

                $link = new action_link($urlduplicate, get_string('duplicate', 'format_onetopic'));
                $link->add_action(new confirm_action(get_string('duplicate_confirm', 'format_onetopic'), null,
                    get_string('duplicate', 'format_onetopic')));

                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('duplicatesection', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo $this->render($link);
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('duplicatesection_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            echo html_writer::start_div("form-item clearfix form-group row fitem");
                echo $this->change_number_sections($course, 0);
            echo html_writer::end_div();

            print_collapsible_region_end();
        }
    }

	/**
	 * Generate the edit control items of a section
	 *
	 * @param stdClass $course The course entry from DB
	 * @param stdClass $section The course_section entry from DB
	 * @param bool $onsectionpage true if being printed on a section page
	 * @return array of edit control items
	 */
	protected function section_edit_control_items($course, $section, $onsectionpage = false) {

		if (!$this->page->user_is_editing()) {
			return array();
		}

		$coursecontext = context_course::instance($course->id);

		if ($onsectionpage) {
			$url = course_get_url($course, $section->section);
		} else {
			$url = course_get_url($course);
		}
		$url->param('sesskey', sesskey());

		$isstealth = $section->section > $this->numsections;
		$controls = array();
		if (!$isstealth && $section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
			if ($course->marker == $section->section) {  // Show the "light globe" on/off.
				$url->param('marker', 0);
				$markedthistopic = get_string('markedthistopic');
				$highlightoff = get_string('highlightoff');
				$controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
											   'name' => $highlightoff,
											   'pixattr' => array('class' => '', 'alt' => $markedthistopic),
											   'attr' => array('class' => 'editing_highlight', 'title' => $markedthistopic,
												   'data-action' => 'removemarker'));
			} else {
				$url->param('marker', $section->section);
				$markthistopic = get_string('markthistopic');
				$highlight = get_string('highlight');
				$controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
											   'name' => $highlight,
											   'pixattr' => array('class' => '', 'alt' => $markthistopic),
											   'attr' => array('class' => 'editing_highlight', 'title' => $markthistopic,
												   'data-action' => 'setmarker'));
			}
		}

		$parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

		// If the delete key exists, we are going to insert our controls after it.
		if (array_key_exists("delete", $parentcontrols)) {
			$url = new moodle_url('/course/editsection.php', array(
					'id' => $section->id,
					'sr' => $section->section - 1,
					'delete' => 1,
					'sesskey' => sesskey()));
			$parentcontrols['delete']['url'] = $url;
		}

		// If the edit key exists, we are going to insert our controls after it.
		if (array_key_exists("edit", $parentcontrols)) {
			$merged = array();
			// We can't use splice because we are using associative arrays.
			// Step through the array and merge the arrays.
			foreach ($parentcontrols as $key => $action) {
				$merged[$key] = $action;
				if ($key == "edit") {
					// If we have come to the edit key, merge these controls here.
					$merged = array_merge($merged, $controls);
				}
			}

// SU_AMEND START - Course: Prevent anyone except admins hiding or deleting default sections
			global $CFG;
			$category = core_course_category::get($course->category, IGNORE_MISSING);
			$catname = strtolower('x'.$category->idnumber);
			if((strpos($catname, 'modules_') !== false &&  $section->section < 5) && !is_siteadmin()){
			  unset($merged['visiblity']);
			  unset($merged['delete']);
			}
// SU_AMEND END
			return $merged;
		} else {
			return array_merge($controls, $parentcontrols);
		}
	}
}
