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
 * Shortcodes
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\local;

use html_writer;
use theme_solent\helper;

/**
 * Shortcodes
 */
class shortcodes {
    /**
     * Returns Module descriptor block for the course.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. Optional 'courseid'.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function moduledescriptor($shortcode, $args, $content, $env, $next) {
        global $DB, $COURSE;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }
        $html = helper::course_unit_descriptor($course);
        return $html;
    }

    /**
     * Returns formatted link to the module descriptor file if it exists.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. Optional 'courseid'.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The link or empty string.
     */
    public static function moduledescriptorfile($shortcode, $args, $content, $env, $next) {
        global $DB, $COURSE;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }
        [$url, $filename] = helper::get_unit_descriptor_file_url($course);
        if ($url) {
            return html_writer::link($url, $filename);
        }
        return '';
    }

    /**
     * Returns formatted link to the external examiner report page.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. Optional 'courseid'.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The link or empty string.
     */
    public static function externalexaminerlink($shortcode, $args, $content, $env, $next) {
        global $DB, $COURSE;
        $config = get_config('theme_solent');
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }

        // Is this a course or a module?
        $ismodule = helper::is_module($course);
        $iscourse = false;
        if (!$ismodule) {
            $iscourse = helper::is_course($course);
        }
        if (!($ismodule || $iscourse)) {
            return '';
        }

        $eeurl = '';
        if ($ismodule) {
            $eeurl = $config->externalexaminerlink;
        } else {
            $eeurl = $config->courseexternalexaminerlink;
        }
        if (!empty($eeurl)) {
            $eeurl = str_replace("::IDNUMBER::", $course->idnumber, $eeurl);
            // If IDNUMBER is in the string it should have been replaced, and therefore, the course isn't valid.
            if (strpos($eeurl, "::IDNUMBER::") === false) {
                return html_writer::link($eeurl, get_string('externalexaminer', 'theme_solent'));
            }
        }
        return '';
    }
}
