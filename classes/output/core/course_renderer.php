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
 * Course renderer override
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output\core;

use stdClass;
use core\output\html_writer;
use core_course_category;
use coursecat_helper;
use core_course_list_element;
use core_course_renderer;
use theme_solent\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');

/**
 * Override core course renderer to add unit descriptor and external examiner info to the coursecat page.
 *
 */
class course_renderer extends core_course_renderer {
    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = html_writer::start_tag('div', ['class' => 'd-flex']);
        // SSU_AMEND_START: Only output the overview files section if there's something there, otherwise you get spacing issues.
        $courseoverviewfiles = $this->course_overview_files($course);
        if (!empty($courseoverviewfiles)) {
            $content .= $courseoverviewfiles;
        }
        // SSU_AMEND_END.
        $content .= html_writer::start_tag('div', ['class' => 'flex-grow-1']);
        // SU_AMEND_START: Add unit descriptor if available.
        $content .= helper::course_unit_descriptor($course->id, $course->category);
        // SU_AMEND_END.
        $content .= $this->course_summary($chelper, $course);
        $content .= $this->course_contacts($course);
        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');
        return $content;
    }
}
