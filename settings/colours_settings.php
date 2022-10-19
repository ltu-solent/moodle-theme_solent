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
 * Theme colours settings
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_solent_colours', get_string('colours_settings', 'theme_solent'));

// Raw SCSS to include before the content.
$setting = new admin_setting_scsscode('theme_solent/scsspre',
get_string('rawscsspre', 'theme_solent'), get_string('rawscsspre_desc', 'theme_solent'), '', PARAM_RAW);
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


// Raw SCSS to include after the content.
$setting = new admin_setting_scsscode('theme_solent/scss', get_string('rawscss', 'theme_solent'),
get_string('rawscss_desc', 'theme_solent'), '', PARAM_RAW);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
