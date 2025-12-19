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
 * TODO describe file course_settings
 *
 * @package    theme_solent
 * @copyright  2024 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

use core\lang_string;

$page = new admin_settingpage('theme_solent_course', get_string('coursesettings', 'theme_solent'));

// Show course image instead of old banner.
$name = 'theme_solent/enablebulkedit';
$title = new lang_string('enablebulkedit', 'theme_solent');
$description = new lang_string('enablebulkedit_desc', 'theme_solent');
$default = '0';
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

/** @var \theme_boost_admin_settingspage_tabs $settings */
$settings->add($page);
