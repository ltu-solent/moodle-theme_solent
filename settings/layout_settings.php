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
 * Layout settings
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_solent_layout', get_string('layoutsettings', 'theme_solent'));

// Navdrawer width.
$name = 'theme_solent/drawerwidth';
$title = get_string('drawerwidth', 'theme_solent');
$description = get_string('drawerwidth_desc', 'theme_solent');
$default = '285px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Navbar height.
$name = 'theme_solent/navbarheight';
$title = get_string('navbarheight', 'theme_solent');
$description = get_string('navbarheight_desc', 'theme_solent');
$default = '50px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Block area width.
$name = 'theme_solent/blockwidth';
$title = get_string('blockwidth', 'theme_solent');
$description = get_string('blockwidth_desc', 'theme_solent');
$default = '360px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Module descriptor.
$name = 'theme_solent/descriptors';
$title = get_string('descriptors', 'theme_solent');
$description = get_string('descriptors_desc', 'theme_solent');
$default = 0;
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Exclude breadcrumbs.
$name = 'theme_solent/excludebreadcrumbs';
$title = get_string('excludebreadcrumbs', 'theme_solent');
$description = get_string('excludebreadcrumbs_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Breadcrumb icon.
$name = 'theme_solent/breadcrumbicon';
$title = get_string('breadcrumbicon', 'theme_solent');
$description = get_string('breadcrumbicon_desc', 'theme_solent');
$default = 'fa fa-caret-right fa-fw';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// To banner or not to banner.
$name = 'theme_solent/enablebanner';
$title = new lang_string('enablebanner', 'theme_solent');
$description = new lang_string('enablebanner_desc', 'theme_solent');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Accessibility tool.
$name = 'theme_solent/enableaccessibilitytool';
$title = new lang_string('enableaccessibilitytool', 'theme_solent');
$description = new lang_string('enableaccessibilitytool_desc', 'theme_solent');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Font.
$name = 'theme_solent/fontfamily';
$title = new lang_string('fontfamily', 'theme_solent');
$description = new lang_string('fontfamily_desc', 'theme_solent');
$default = 'Open Sans';
$options = [
    'Open Sans' => 'Open Sans',
    'PT Sans' => 'PT Sans'
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Font size.
$name = 'theme_solent/fontsizebase';
$title = get_string('fontsizebase', 'theme_solent');
$description = get_string('fontsizebase_desc', 'theme_solent');
$default = '0.9375rem';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);