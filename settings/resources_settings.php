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
 * Resourse settings file
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$page = new admin_settingpage('theme_solent_resources', new lang_string('resources_settings', 'theme_solent'));

$page->add(new admin_setting_heading(
    'theme_solent/descriptors',
    new lang_string('descriptors', 'theme_solent'),
    new lang_string('descriptors_desc', 'theme_solent')
));

// Module descriptor.
$name = 'theme_solent/moduledescriptor';
$title = new lang_string('moduledescriptor', 'theme_solent');
$desc = new lang_string('moduledescriptor_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $desc, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/descriptorfolder';
$title = new lang_string('descriptorfolder', 'theme_solent');
$desc = new lang_string('descriptorfolder_desc', 'theme_solent');
$default = 0;
$setting = new admin_setting_configtext($name, $title, $desc, $default, PARAM_INT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Module External examiner link.
$name = 'theme_solent/externalexaminerlink';
$title = new lang_string('externalexaminerlink', 'theme_solent');
$desc = new lang_string('externalexaminerlink_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtext($name, $title, $desc, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Course descriptor.
$name = 'theme_solent/coursedescriptor';
$title = new lang_string('coursedescriptor', 'theme_solent');
$desc = new lang_string('coursedescriptor_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $desc, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/coursedescriptorfolder';
$title = new lang_string('coursedescriptorfolder', 'theme_solent');
$desc = new lang_string('coursedescriptorfolder_desc', 'theme_solent');
$default = 0;
$setting = new admin_setting_configtext($name, $title, $desc, $default, PARAM_INT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Course External examiner link.
$name = 'theme_solent/courseexternalexaminerlink';
$title = new lang_string('courseexternalexaminerlink', 'theme_solent');
$desc = new lang_string('courseexternalexaminerlink_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtext($name, $title, $desc, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
