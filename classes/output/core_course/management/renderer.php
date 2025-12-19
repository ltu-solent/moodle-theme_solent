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
 * @package    theme_solent
 * @copyright  2021 Sarah Cotton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost\output\core_course\management;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/classes/management_renderer.php");

use stdClass;
use core\output\html_writer;
use core_course_category;
use core_course_list_element;
use course_listing;
use core\url;
use theme_solent\helper;

/**
 * Main renderer for the course management pages.
 *
 * @package theme_solent
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
        /** @var \core\output\core_renderer $output */
        $output = $this->page->get_renderer('core');
        $text = $course->get_formatted_name();
        // SU_AMEND_START: Unit start date: Manage categories.
        if (helper::is_module($course->category)) {
            $text .= ' (' . userdate($course->startdate, '%d/%m/%Y') . ')';
        }
        // SU_AMEND_END.
        $attributes = [
                'class' => 'listitem listitem-course list-group-item list-group-item-action',
                'data-id' => $course->id,
                'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
                'data-visible' => $course->visible ? '1' : '0',
        ];

        $bulkcourseinput = [
                'id' => 'courselistitem' . $course->id,
                'type' => 'checkbox',
                'name' => 'bc[]',
                'value' => $course->id,
                'class' => 'bulk-action-checkbox custom-control-input',
                'data-action' => 'select',
        ];

        $checkboxclass = '';
        if (!$category->has_manage_capability()) {
            // Very very hardcoded here.
            $checkboxclass = 'd-none';
        }

        $viewcourseurl = new url($this->page->url, ['courseid' => $course->id]);

        $html  = html_writer::start_tag('li', $attributes);
        // SSU_AMEND_START: Leave clearfix here as the border underlines mess up with flex.
        $html .= html_writer::start_div('clearfix');
        // SSU_AMEND_END.

        if ($category->can_resort_courses()) {
            // In order for dnd to be available the user must be able to resort the category children..
            $html .= html_writer::div($output->pix_icon('i/move_2d', get_string('dndcourse')), 'float-left drag-handle');
        }

        $html .= html_writer::start_div('float-start ' . $checkboxclass);
        $html .= html_writer::start_div('custom-control custom-checkbox me-1 ');
        $html .= html_writer::empty_tag('input', $bulkcourseinput);
        $labeltext = html_writer::span(get_string('bulkactionselect', 'moodle', $text), 'sr-only');
        $html .= html_writer::tag('label', $labeltext, [
            'class' => 'custom-control-label',
            'for' => 'courselistitem' . $course->id]);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        // SSU_AMEND_START: Don't include classes "col" or flex.
        $html .= html_writer::link(
            $viewcourseurl,
            $text,
            ['class' => 'text-break ps-0 mb-2 coursename aalink']
        );
        $html .= html_writer::start_div('float-end');
        // SSU_AMEND_END.
        if ($course->idnumber) {
            $html .= html_writer::tag('span', s($course->idnumber), ['class' => 'text-muted idnumber']);
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

        // SU_AMEND_START: Unit start date: Manage categories search.
        if (helper::is_module($course->category)) {
            $text .= ' (' . userdate($course->startdate, '%d/%m/%Y') . ')';
        }
        // SU_AMEND_END.

        $attributes = [
                'class' => 'listitem listitem-course list-group-item list-group-item-action',
                'data-id' => $course->id,
                'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
                'data-visible' => $course->visible ? '1' : '0',
        ];
        $bulkcourseinput = '';
        if (core_course_category::get($course->category)->can_move_courses_out_of()) {
            $bulkcourseinput = [
                    'type' => 'checkbox',
                    'id' => 'coursesearchlistitem' . $course->id,
                    'name' => 'bc[]',
                    'value' => $course->id,
                    'class' => 'bulk-action-checkbox custom-control-input',
                    'data-action' => 'select',
            ];
        }
        $viewcourseurl = new url($this->page->url, ['courseid' => $course->id]);
        $categoryname = core_course_category::get($course->category)->get_formatted_name();

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= html_writer::start_div('float-start');
        if ($bulkcourseinput) {
            $html .= html_writer::start_div('custom-control custom-checkbox me-1');
            $html .= html_writer::empty_tag('input', $bulkcourseinput);
            $labeltext = html_writer::span(get_string('bulkactionselect', 'moodle', $text), 'sr-only');
            $html .= html_writer::tag('label', $labeltext, [
                'class' => 'custom-control-label',
                'for' => 'coursesearchlistitem' . $course->id]);
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, ['class' => 'float-start coursename aalink']);
        $html .= html_writer::tag('span', $categoryname, ['class' => 'float-start ms-3 text-muted']);
        $html .= html_writer::start_div('float-end');
        // SSU_AMEND_START: Swap idnumber and actions.
        $html .= html_writer::tag('span', s($course->idnumber), ['class' => 'text-muted idnumber']);
        $html .= $this->search_listitem_actions($course);
        // SSU_AMEND_END.
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }
}
