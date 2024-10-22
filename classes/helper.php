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

use core\context;
use core_course_category;
use filter_manager;
use moodle_exception;
use moodle_url;
use stdClass;

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
                if ($setting !== '') {
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
        $category = core_course_category::get($course->category, IGNORE_MISSING, true);
        if (!$category) {
            return false;
        }
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
        $category = core_course_category::get($course->category, IGNORE_MISSING, true);
        if (!$category) {
            return false;
        }
        $cattype = self::get_category_type($category);
        return $cattype == 'courses';
    }

    /**
     * Is this category a course or module category. Returns the type.
     *
     * @param core_course_category $category
     * @return string modules, courses
     */
    public static function get_category_type(core_course_category $category) {
        if (empty($category->idnumber)) {
            return '';
        }
        $catparts = explode('_', $category->idnumber);
        $cattype = $catparts[0]; // Modules, Courses.
        return $cattype;
    }

    /**
     * Get url for unit descriptor document
     *
     * @param object $course Course object
     * @return array|null [moodle_url, filename]
     */
    public static function get_unit_descriptor_file_url($course) {
        global $DB;
        $config = get_config('theme_solent');
        // Is this a course or a module?
        $ismodule = self::is_module($course);
        $iscourse = false;
        if (!$ismodule) {
            $iscourse = self::is_course($course);
        }
        if (!($ismodule || $iscourse)) {
            return null;
        }
        // Descriptor folder: 1 for course and 1 for module.
        $descriptorfolderid = 0;
        if ($ismodule) {
            $descriptorfolderid = $config->descriptorfolder;
        } else {
            $descriptorfolderid = $config->coursedescriptorfolder;
        }
        if ($descriptorfolderid == 0) {
            return null;
        }
        $filename = substr($course->shortname, 0, strpos($course->shortname, '_'));
        $sqllike = $DB->sql_like('filename', ':filename');
        $file = $DB->get_record_sql("
            SELECT f.id, filename, contextid, filepath
            FROM {files} f
            JOIN {context} ctx ON ctx.id = f.contextid
            WHERE ctx.instanceid = :descriptorfolderid
                AND (component = 'mod_folder' AND filearea = 'content')
                AND {$sqllike}
            ORDER BY timemodified DESC
            LIMIT 1", [
                'descriptorfolderid' => $descriptorfolderid,
                'filename' => $DB->sql_like_escape($filename) . '%',
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
        return [$url, $file->filename];
    }

    /**
     * Course unit descriptor
     *
     * @param stdClass $course
     * @return string Rendered descriptor
     */
    public static function course_unit_descriptor($course): string {
        $content = '';
        $category = core_course_category::get($course->category, IGNORE_MISSING, true);
        if (!$category) {
            return $content;
        }
        $cattype = self::get_category_type($category);
        if (!in_array($cattype, ['modules', 'courses'])) {
            return $content;
        }

        $coursecontext = context\course::instance($course->id);
        $filterman = filter_manager::instance();
        $descriptor = '';
        if ($cattype == 'modules') {
            $descriptor = get_config('theme_solent', 'moduledescriptor');
        } else {
            $descriptor = get_config('theme_solent', 'coursedescriptor');
        }
        $descriptor = trim(clean_text($descriptor));
        if (empty($descriptor)) {
            return '';
        }
        // Before passing into the filter, add the courseid to module startenddate.
        // On a course page this can be inferred from context, however, in search results it needs to be explicit.
        $descriptor = str_replace('[startenddates]', '[startenddates courseid=' . $course->id . ']', $descriptor);
        // And to the modulecode.
        $descriptor = str_replace('[modulecode]', '[modulecode courseid=' . $course->id . ']', $descriptor);
        $descriptor = str_replace('[moduledescriptorfile]', '[moduledescriptorfile courseid=' . $course->id . ']', $descriptor);
        $descriptor = str_replace('[externalexaminerlink]', '[externalexaminerlink courseid=' . $course->id . ']', $descriptor);
        $content = $filterman->filter_text($descriptor, $coursecontext);
        return $content;
    }
}
