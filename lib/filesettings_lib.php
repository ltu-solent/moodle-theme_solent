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
 * Theme file settings lib
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_solent_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('solent');
    }
    if ($filearea == 'headerdefaultimage') {
        return $theme->setting_file_serve('headerdefaultimage', $args, $forcedownload, $options);
    } else if ($filearea === 'loginimage') { // Login page image.
        return $theme->setting_file_serve('loginimage', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}
