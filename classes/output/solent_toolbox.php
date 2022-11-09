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
 * Theme solent renderer
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output;

use core_course_category;
use stdClass;
use theme_solent\helper;

/**
 * Use the toolbox to add functions to renderers but still keeping them separate.
 */
trait solent_toolbox {
    /**
     * Course unit descriptor
     *
     * @param stdClass $course
     * @return string Rendered descriptor
     */
    public function course_unit_descriptor($course): string {
        global $PAGE;
        $content = '';
	    $category = core_course_category::get($course->category, IGNORE_MISSING);
        $cattype = helper::get_category_type($category);
        if (!in_array($cattype, ['modules', 'courses'])) {
            return $content;
        }

		$coursecode = substr($course->shortname, 0, strpos($course->shortname, "_"));
        $data = new stdClass();
		if ($cattype == 'modules') {
            $data->module = new stdClass();
            // Don't show start and end dates on search page.
			if ($PAGE->bodyid != 'page-course-search') {
                $data->module->startdate = userdate($course->startdate, get_string('strftimedatefullshort', 'langconfig'));
                if ($course->enddate > 0) {
                    $data->module->enddate = userdate($course->enddate, get_string('strftimedatefullshort', 'langconfig'));
                }
			}
            $data->module->instance = $course->shortname;
            if ($coursecode != '') {
                $descriptor = new stdClass();
                $descriptor->url = helper::get_unit_descriptor_file_url($coursecode);
                $descriptor->coursecode = $coursecode;
                if ($descriptor->url != null) {
                    $data->module->descriptor = $descriptor;
                }
            }
		}

		if ($cattype == 'courses') {
            $data->course = new stdClass();
            $eeurl = get_config('theme_solent', 'externalexaminerlink');
            if ($eeurl) {
                $neweeurl = str_replace("::IDNUMBER::", $course->idnumber, $eeurl);
                if ($neweeurl != $eeurl) {
                    $data->course->externalexaminer = $neweeurl;
                }
            }
		}
        return $this->render_from_template('theme_solent/solent/unit_descriptor', $data);
    }
}