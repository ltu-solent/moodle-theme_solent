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

// Navdrawer width (Not using).
$name = 'theme_solent/drawerwidth';
$title = get_string('drawerwidth', 'theme_solent') . ' (Not using)';
$description = get_string('drawerwidth_desc', 'theme_solent');
$default = '285px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Navbar height (Not using).
$name = 'theme_solent/navbarheight';
$title = get_string('navbarheight', 'theme_solent') . ' (Not using)';
$description = get_string('navbarheight_desc', 'theme_solent');
$default = '50px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Block area width (Not using).
$name = 'theme_solent/blockwidth';
$title = get_string('blockwidth', 'theme_solent') . ' (Not using)';
$description = get_string('blockwidth_desc', 'theme_solent');
$default = '360px';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/expandfieldsets';
$title = new lang_string('expandfieldsets', 'theme_solent');
$description = new lang_string('expandfieldsets_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
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

// Show course image instead of old banner.
$name = 'theme_solent/enablecourseimage';
$title = new lang_string('enablecourseimage', 'theme_solent');
$description = new lang_string('enablecourseimage_desc', 'theme_solent');
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
    'PT Sans' => 'PT Sans',
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

$name = 'theme_solent/enablewelcome';
$title = new lang_string('enablewelcome', 'theme_solent');
$description = new lang_string('enablewelcome_desc', 'theme_solent');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$page->add(new admin_setting_heading(
    'theme_solent/fitvids',
    new lang_string('fitvids', 'theme_solent'),
    new lang_string('fitvids_desc', 'theme_solent')
));

$name = 'theme_solent/enable_fitvid';
$title = new lang_string('enable_fitvid', 'theme_solent');
$description = new lang_string('enable_fitvid_desc', 'theme_solent');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Embedded video max width.
$name = 'theme_solent/vidmaxwidth';
$title = new lang_string('vidmaxwidth', 'theme_solent');
$description = new lang_string('vidmaxwidth_desc', 'theme_solent');
$default = 450;
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/vidmaxheight';
$title = new lang_string('vidmaxheight', 'theme_solent');
$description = new lang_string('vidmaxheight_desc', 'theme_solent');
$default = 300;
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/customselectors';
$title = new lang_string('customselectors', 'theme_solent');
$description = new lang_string('customselectors_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_solent/ignoreselectors';
$title = new lang_string('ignoreselectors', 'theme_solent');
$description = new lang_string('ignoreselectors_desc', 'theme_solent');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
