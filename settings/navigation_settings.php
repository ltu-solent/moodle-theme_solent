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
 * Navgiation settings
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\lang_string;

$page = new admin_settingpage('theme_solent_navigation', new lang_string('navigationsettings', 'theme_solent'));

// Prepare hide nodes options.
$hidenodesoptions = [
    'home' => get_string('home'),
    'myhome' => get_string('myhome'),
    'courses' => get_string('mycourses'),
    'siteadmin' => get_string('administrationsite'),
];

// Setting: Hide nodes in primary navigation.
$name = 'theme_solent/hidenodesprimarynavigation';
$title = new lang_string('hidenodesprimarynavigationsetting', 'theme_solent');
$description = new lang_string('hidenodesprimarynavigationsetting_desc', 'theme_solent');
$setting = new admin_setting_configmulticheckbox($name, $title, $description, [], $hidenodesoptions);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Exclude breadcrumbs.
$name = 'theme_solent/excludebreadcrumbs';
$title = new lang_string('excludebreadcrumbs', 'theme_solent');
$description = new lang_string('excludebreadcrumbs_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Exclude Secondary nav items.
$name = 'theme_solent/excludesecondarynavitems';
$title = new lang_string('excludesecondarynavitems', 'theme_solent');
$description = new lang_string('excludesecondarynavitems_desc', 'theme_solent');
$default = 'grades';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Navbar at bottom of page.
$name = 'theme_solent/enablebottomnavbar';
$title = new lang_string('enablebottomnavbar', 'theme_solent');
$description = new lang_string('enablebottomnavbar_desc', 'theme_solent');
$default = '0';
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// ScrollSpy.
$name = 'theme_solent/enablescrollspy';
$title = new lang_string('enablescrollspy', 'theme_solent');
$description = new lang_string('enablescrollspy_desc', 'theme_solent');
$default = '0';
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/enablestudentsecondarynav';
$title = new lang_string('enablestudentsecondarynav', 'theme_solent');
$description = new lang_string('enablestudentsecondarynav_desc', 'theme_solent');
$default = '1';
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

/** @var \theme_boost_admin_settingspage_tabs $settings */
$settings->add($page);
