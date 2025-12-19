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
 * Backup renderer
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output\core;

defined('MOODLE_INTERNAL') || die();

use import_course_search;
use core\output\html_writer;
use restore_course_search;
use html_table;
use html_table_row;
use html_table_cell;
use core\context;
use theme_solent\helper;

require_once($CFG->dirroot . '/backup/util/ui/renderer.php');

/**
 * Backup renderer override
 */
class backup_renderer extends \core_backup_renderer {
    /**
     * Renders an import course search object
     *
     * @param import_course_search $component
     * @return string
     */
    public function render_import_course_search(import_course_search $component) {
        /** @var \core\output\core_renderer $core */
        $core = $this->page->get_renderer('core');
        $output = html_writer::start_tag('div', ['class' => 'import-course-search']);
        if ($component->get_count() === 0) {
            $output .= $core->notification(get_string('nomatchingcourses', 'backup'));

            $output .= html_writer::start_tag('div', ['class' => 'ics-search d-flex flex-wrap align-items-center']);
            $attrs = [
                'type' => 'text',
                'name' => restore_course_search::$VAR_SEARCH, // phpcs:ignore
                'value' => $component->get_search(),
                'aria-label' => get_string('searchcourses'),
                'placeholder' => get_string('searchcourses'),
                'class' => 'form-control',
            ];
            $output .= html_writer::empty_tag('input', $attrs);
            $attrs = [
                'type' => 'submit',
                'name' => 'searchcourses',
                'value' => get_string('search'),
                'class' => 'btn btn-secondary ml-1',
            ];
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

        $output .= html_writer::tag('div', $countstr, ['class' => 'ics-totalresults']);
        $output .= html_writer::start_tag('div', ['class' => 'ics-results']);

        $table = new html_table();
        $table->head = ['&nbsp;', get_string('shortnamecourse'), get_string('fullnamecourse')];
        $table->data = [];
        foreach ($component->get_results() as $course) {
            // SU_AMEND_START: Adds Unit start date (if relevant) to list of courses to import from.
            $courseobj = get_course($course->id);
            if (helper::is_module($courseobj->category)) {
                $course->fullname .= ' - ' . get_string('startdate', 'theme_solent', userdate($courseobj->startdate, "%d/%m/%Y"));
            }
            // SU_AMEND END.
            $row = new html_table_row();
            $row->attributes['class'] = 'ics-course';
            if (!$course->visible) {
                $row->attributes['class'] .= ' dimmed';
            }
            $id = $this->make_unique_id('import-course');
            $row->cells = [
                html_writer::empty_tag('input', ['type' => 'radio', 'name' => 'importid', 'value' => $course->id,
                    'id' => $id]),
                html_writer::label(
                    format_string($course->shortname, true, ['context' => context\course::instance($course->id)]),
                    $id,
                    true,
                    ['class' => 'd-block']
                ),
                format_string($course->fullname, true, ['context' => context\course::instance($course->id)]),
            ];
            $table->data[] = $row;
        }
        if ($component->has_more_results()) {
            $cell = new html_table_cell(get_string('moreresults', 'backup'));
            $cell->colspan = 3;
            $cell->attributes['class'] = 'notifyproblem';
            $row = new html_table_row([$cell]);
            $row->attributes['class'] = 'rcs-course';
            $table->data[] = $row;
        }
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', ['class' => 'ics-search d-flex flex-wrap align-items-center']);
        $attrs = [
            'type' => 'text',
            'name' => restore_course_search::$VAR_SEARCH, // phpcs:ignore
            'value' => $component->get_search(),
            'aria-label' => get_string('searchcourses'),
            'placeholder' => get_string('searchcourses'),
            'class' => 'form-control'];
        $output .= html_writer::empty_tag('input', $attrs);
        $attrs = [
            'type' => 'submit',
            'name' => 'searchcourses',
            'value' => get_string('search'),
            'class' => 'btn btn-secondary ms-1',
        ];
        $output .= html_writer::empty_tag('input', $attrs);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }
}
