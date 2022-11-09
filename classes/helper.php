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
 * Theme helper class. Bundle of functions to do general tasks
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent;

use core_course_category;
use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Theme helper class. Bundle of functions to do general tasks.
 */
class helper {
    /**
     * Takes a string text similar to custommenu format and builds a structure for output.
     * There is no depth to this menu, it's just a flat list. No language.
     *
     * @param string $text Format "Text|url|Title attribute|css class"
     * @return array
     */
    public static function convert_text_to_menu($text) {
        $menu = [];
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            $item = new stdClass();
            // Parse item settings.
            $item->text = null;
            $item->url = null;
            $item->title = null;
            $item->class = null;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if (!empty($setting)) {
                    switch ($i) {
                        case 0: // Menu text.
                            $item->text = ltrim($setting, '-');
                            break;
                        case 1: // URL.
                            try {
                                $item->url = new moodle_url($setting);
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL.
                                $item->url = null;
                            }
                            break;
                        case 2: // Title attribute.
                            $item->title = $setting;
                            break;
                        case 3: // Class on the link.
                            $item->class = $setting;
                            break;
                    }
                }
            }
            $menu[] = $item;
        }

        return $menu;
    }

    /**
     * Is this course a module page.
     *
     * @param stdClass $course Course object
     * @return boolean
     */
    public static function is_module($course) {
        if (!isset($course->category)) {
            return false;
        }
        $category = core_course_category::get($course->category, IGNORE_MISSING);
        $cattype = self::get_category_type($category);
        return $cattype == 'modules';
    }

    /**
     * Is this course a course page.
     *
     * @param stdClass $course Course object
     * @return boolean
     */
    public static function is_course($course) {
        if (!isset($course->category)) {
            return false;
        }
        $category = core_course_category::get($course->category, IGNORE_MISSING);
        $cattype = self::get_category_type($category);
        return $cattype == 'courses';
    }

    /**
     * Is this category a course or module category. Returns the type.
     *
     * @param core_course_category $category
     * @return void
     */
    public static function get_category_type(core_course_category $category) {
        $catparts = explode('_', $category->idnumber);
        $cattype = $catparts[0]; // Modules, Courses.
        return $cattype;
    }

    /**
     * Get url for unit descriptor document
     *
     * @param string $coursecode Course code (without instance information e.g. ABC101)
     * @return moodle_url|null
     */
    public static function get_unit_descriptor_file_url($coursecode) {
        global $DB;
        $descriptorinstanceid = get_config('theme_solent', 'descriptors');
        if (!($descriptorinstanceid > 0)) {
            return null;
        }
        $sqllike = $DB->sql_like('filename', ':filename');
        $file = $DB->get_record_sql("
            SELECT f.id, filename, contextid, filepath
            FROM {files} f
            JOIN {context} ctx ON ctx.id = f.contextid
            WHERE ctx.instanceid = :descriptorinstanceid
                AND (component = 'mod_folder' AND filearea = 'content')
                AND {$sqllike}
            ORDER BY timemodified DESC", [
                'descriptorinstanceid' => $descriptorinstanceid,
                'filename' => $DB->sql_like_escape($coursecode) . '%'
            ]
        );
        if (!$file) {
            return null;
        }
        $url = moodle_url::make_pluginfile_url(
            $file->contextid,
            'mod_folder',
            'content',
            0,
            $file->filepath,
            $file->filename,
            true
        );
        return $url;
    }
}
