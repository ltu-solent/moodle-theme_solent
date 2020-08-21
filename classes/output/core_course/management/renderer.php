<?php
// This file is part of The Bootstrap Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_boost
 * @copyright   2018 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost\output\core_course\management;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/classes/management_renderer.php");

use stdClass;
use html_writer;
use core_course_category;
use core_course_list_element;
use course_listing;
use moodle_url;

/**
 * Main renderer for the course management pages.
 *
 * @package theme_boost
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_course_management_renderer {

    /**
     * Renderers a course list item.
     *
     * This function will be called for every course being displayed by course_listing.
     *
     * @param core_course_category $category The currently selected category and the category the course belongs to.
     * @param core_course_list_element $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     */
    public function course_listitem(core_course_category $category, core_course_list_element $course, $selectedcourse) {

        $text = $course->get_formatted_name();
// SU_AMEND START - Unit start date: Manage categories
        $categoryname = core_course_category::get($course->category)->get_formatted_name();
        $catname = strtolower('x'.$categoryname);
        if(strpos($catname, 'unit pages') !== false){
          $text .= ' (' .date('d-m-Y',$course->startdate) .')';
        }
// SU_AMEND END
        $attributes = array(
            'class' => 'listitem listitem-course list-group-item list-group-item-action',
            'data-id' => $course->id,
            'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
            'data-visible' => $course->visible ? '1' : '0'
        );

        $bulkcourseinput = array(
            'type' => 'checkbox',
            'name' => 'bc[]',
            'value' => $course->id,
            'class' => 'bulk-action-checkbox',
            'aria-label' => get_string('bulkactionselect', 'moodle', $text),
            'data-action' => 'select'
        );
        if (!$category->has_manage_capability()) {
            // Very very hardcoded here.
            $bulkcourseinput['style'] = 'visibility:hidden';
        }

        $viewcourseurl = new moodle_url($this->page->url, array('courseid' => $course->id));

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');

        if ($category->can_resort_courses()) {
            // In order for dnd to be available the user must be able to resort the category children..
            $html .= html_writer::div($this->output->pix_icon('i/move_2d', get_string('dndcourse')), 'float-left drag-handle');
        }

        $html .= html_writer::start_div('ba-checkbox float-left');
        $html .= html_writer::empty_tag('input', $bulkcourseinput).'&nbsp;';
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, array('class' => 'float-left coursename'));
        $html .= html_writer::start_div('float-right');
        if ($course->idnumber) {
            $html .= html_writer::tag('span', s($course->idnumber), array('class' => 'dimmed idnumber'));
        }
        $html .= $this->course_listitem_actions($category, $course);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }

    /**
     * Renderers a search result course list item.
     *
     * This function will be called for every course being displayed by course_listing.
     *
     * @param core_course_list_element $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     */
    public function search_listitem(core_course_list_element $course, $selectedcourse) {

        $text = $course->get_formatted_name();

// SU_AMEND START - Unit start date: Manage categories
        $categoryname = core_course_category::get($course->category)->get_formatted_name();
        $catname = strtolower('x'.$categoryname);
        if(strpos($catname, 'unit pages') !== false){
          $text .= ' (' .date('d-m-Y',$course->startdate) .')';
        }
// SU_AMEND END

        $attributes = array(
            'class' => 'listitem listitem-course list-group-item list-group-item-action',
            'data-id' => $course->id,
            'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
            'data-visible' => $course->visible ? '1' : '0'
        );
        $bulkcourseinput = '';
        if (core_course_category::get($course->category)->can_move_courses_out_of()) {
            $bulkcourseinput = array(
                'type' => 'checkbox',
                'name' => 'bc[]',
                'value' => $course->id,
                'class' => 'bulk-action-checkbox',
                'aria-label' => get_string('bulkactionselect', 'moodle', $text),
                'data-action' => 'select'
            );
        }
        $viewcourseurl = new moodle_url($this->page->url, array('courseid' => $course->id));
        $categoryname = core_course_category::get($course->category)->get_formatted_name();

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= html_writer::start_div('float-left');
        if ($bulkcourseinput) {
            $html .= html_writer::empty_tag('input', $bulkcourseinput).'&nbsp;';
        }
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, array('class' => 'float-left coursename'));
        $html .= html_writer::tag('span', $categoryname, array('class' => 'float-left categoryname'));
        $html .= html_writer::start_div('float-right');
        $html .= $this->search_listitem_actions($course);
        $html .= html_writer::tag('span', s($course->idnumber), array('class' => 'dimmed idnumber'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }
}
