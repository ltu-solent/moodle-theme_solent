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
 * Theme lib file
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\html_writer;

require_once('lib/theme_lib.php');
require_once('lib/scss_lib.php');
require_once('lib/filesettings_lib.php');
require_once('lib/solentzone_lib.php');

/**
 * Gets the header image for the current course.
 *
 * @return stdClass Template ready context for imageclass and imageselector.
 */
function theme_solent_header_image() {
    global $DB, $COURSE, $PAGE;
    $oncoursepage = in_array($PAGE->pagelayout, ['course', 'incourse']);
    $isediting = $PAGE->user_is_editing();

    $header = new stdClass();
    $header->imageclass = null;
    $header->imageselector = null;

    if ($COURSE->id == 1 || !$oncoursepage) {
        return $header;
    }

    $record = $DB->get_record('theme_header', ['course' => $COURSE->id]);
    if (!$record) {
        $record = new stdclass();
        $record->course = $COURSE->id;

        $currentcategory = $DB->get_record('course_categories', ['id' => $COURSE->category]);
        $catname = strtolower('x' . $currentcategory->name);
        if (isset($catname)) {
            if (strpos($catname, 'course pages') !== false) {
                $record->opt = '08';
                $record->id = $DB->insert_record('theme_header', $record);
            } else {
                $record->opt = '01';
                $record->id = $DB->insert_record('theme_header', $record);
            }
        }
    }

    if ($isediting) {
        $url = new core\url('/theme/solent/layout/header_options.php',
            ['course' => $COURSE->id, 'opt' => $record->opt]);
        $header->imageselector = html_writer::link($url, 'Select header image', ['class' => 'header-image-link btn btn-secondary']);
    }
    $header->imageclass = 'header-image opt' . $record->opt;
    return $header;
}

/**
 * Called by Moodle when the $page object is ready.
 *
 * @param moodle_page $page
 * @return void
 */
function theme_solent_page_init(moodle_page $page) {
    global $CFG;
    $config = get_config('theme_solent');
    $includeaccessibilitytool = get_config('theme_solent', 'enableaccessibilitytool');
    if ($includeaccessibilitytool && file_exists($CFG->dirroot . "/local/accessibilitytool/lib.php")) {
        require_once($CFG->dirroot . "/local/accessibilitytool/lib.php");
        local_accessibilitytool_page_init($page);
    }
    if (file_exists($CFG->dirroot . '/local/solent/lib.php')) {
        require_once($CFG->dirroot . '/local/solent/lib.php');
        local_solent_page_init($page);
    }
    $page->requires->css('/theme/solent/fonts/fontawesome5/css/all.min.css');
    $page->requires->css('/theme/solent/fonts/fontawesome5/css/v4-shims.min.css');

    if ($config->enablescrollspy) {
        $page->requires->js_call_amd('theme_solent/scrollspy', 'init');
    }

    $expands = explode("\r\n", $config->expandfieldsets);
    $openfieldsets = ['id' => []];
    foreach ($expands as $expand) {
        $expand = trim($expand);
        $expand = preg_replace('/[^a-zA-Z0-9#_-]/i', '', (string)$expand);
        if (!empty($expand)) {
            $expand = (strpos($expand, '#') === 0) ? $expand : '#' . $expand;
            $openfieldsets['id'][] = $expand;
        }
    }
    $page->requires->js_call_amd('theme_solent/solent', 'togglefieldsets', $openfieldsets);
    $fitvidsenabled = $config->enable_fitvid ?? false;
    if ($fitvidsenabled) {
        $settings = [];
        $settings['maxwidth'] = $config->vidmaxwidth;
        $settings['maxheight'] = $config->vidmaxheight;
        $settings['customSelector'] = explode("\n", $config->customselectors);
        $settings['ignore'] = explode("\n", $config->ignoreselectors);
        $page->requires->js_call_amd('theme_solent/fitvids', 'init', [$settings]);
    }
}

/**
 * Override default icons with our preferred FA icons
 *
 * @return array
 */
function theme_solent_get_fontawesome_icon_map() {
    return [
        'atto_styles:icon' => 'fa-fill-drip',
    ];
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_solent_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core\user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core\user::class, 'is_current_user'],
        ],
    ];
}
