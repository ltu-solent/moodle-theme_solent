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
 * @package   theme_solent
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingsolent', get_string('configtitle', 'theme_solent'));
    $page = new admin_settingpage('theme_solent_general', get_string('generalsettings', 'theme_solent'));

    // Preset.
    $name = 'theme_solent/preset';
    $title = get_string('preset', 'theme_solent');
    $description = get_string('preset_desc', 'theme_solent');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_solent', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_solent/presetfiles';
    $title = get_string('presetfiles','theme_solent');
    $description = get_string('presetfiles_desc', 'theme_solent');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_solent/backgroundimage';
    $title = get_string('backgroundimage', 'theme_solent');
    $description = get_string('backgroundimage_desc', 'theme_solent');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_solent/brandcolor';
    $title = get_string('brandcolor', 'theme_solent');
    $description = get_string('brandcolor_desc', 'theme_solent');
    $default = '#425B6C';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Onetopic tab color
    $name = 'theme_solent/tabcolor';
    $title = get_string('tabcolor', 'theme_solent');
    $description = get_string('tabcolor_desc', 'theme_solent');
    $default = '#425B6C';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
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
    
    // Font size.
    $name = 'theme_solent/fontsizebase';
    $title = get_string('fontsizebase', 'theme_solent');
    $description = get_string('fontsizebase_desc', 'theme_solent');
	$default = '1rem';
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

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_solent_advanced', get_string('advancedsettings', 'theme_solent'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_solent/scsspre',
        get_string('rawscsspre', 'theme_solent'), get_string('rawscsspre_desc', 'theme_solent'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_solent/scss', get_string('rawscss', 'theme_solent'),
        get_string('rawscss_desc', 'theme_solent'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
