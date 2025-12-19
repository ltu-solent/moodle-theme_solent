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
 * Footer menus
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;

defined('MOODLE_INTERNAL') || die();


$page = new admin_settingpage('theme_solent_footer', new lang_string('footer_settings', 'theme_solent'));

// Study column menu.
$name = new lang_string('studymenuitems', 'theme_solent');
$desc = new lang_string('studymenuitems_desc', 'theme_solent');
$default =
'Succeed@Solent|/succeed
Referencing|/succeed/referencing
Subject Guides|https://libguides.solent.ac.uk/
Library|https://students.solent.ac.uk/studying/library
Ethics|https://ethics.app.solent.ac.uk';
$setting = new admin_setting_configtextarea('theme_solent/studymenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Organise column menu.
$name = new lang_string('organisemenuitems', 'theme_solent');
$desc = new lang_string('organisemenuitems_desc', 'theme_solent');
$default =
'Email|https://email.solent.ac.uk
Timetables|https://timetable.solent.ac.uk/
Term Dates|https://www.solent.ac.uk/about/term-dates
Portal|https://students.solent.ac.uk/';
$setting = new admin_setting_configtextarea('theme_solent/organisemenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Support column menu.
$name = new lang_string('supportmenuitems', 'theme_solent');
$desc = new lang_string('supportmenuitems_desc', 'theme_solent');
$default =
'Student Hub|https://students.solent.ac.uk/student-hub
IT & Media|https://students.solent.ac.uk/studying/learning-technologies-helpdesk
Printing|https://students.solent.ac.uk/studying/print-service
Extenuating Circumstances|https://students.solent.ac.uk/studying/attendance-monitoring/extenuating-circumstances';
$setting = new admin_setting_configtextarea('theme_solent/supportmenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Solent Futures column menu.
$name = new lang_string('solentfuturesmenuitems', 'theme_solent');
$desc = new lang_string('solentfuturesmenuitems_desc', 'theme_solent');
$default =
'Solent Futures Online|https://solentfutures.careercentre.me/u/qdvwuzkl
Job Vacancies|https://solentfutures.careercentre.me/u/7loi92gn
CV Help|https://students.solent.ac.uk/careers/cvs-applications-and-interviews
Placements|https://students.solent.ac.uk/careers/placements-and-work-experience
Events & Workshops|https://students.solent.ac.uk/events';
$setting = new admin_setting_configtextarea('theme_solent/solentfuturesmenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Terms & Conditions.
$name = new lang_string('tandcsmenuitems', 'theme_solent');
$desc = new lang_string('tandcsmenuitems_desc', 'theme_solent');
$default =
'Disclaimer|https://www.solent.ac.uk/disclaimer
Terms & Conditions|/terms
Cookies|https://www.solent.ac.uk/disclaimer/cookies
Accessibility Statement|/accessibilitystatement
';
$setting = new admin_setting_configtextarea('theme_solent/tandcsmenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Social menu items.
$name = new lang_string('socialmenuitems', 'theme_solent');
$desc = new lang_string('socialmenuitems_desc', 'theme_solent');
$default =
'Solent Twitter|https://twitter.com/solentuni
Solent Facebook|https://www.facebook.com/solentuniversity
Solent YouTube|https://www.youtube.com/user/SolentUniOfficial
Solent LinkedIn|https://www.linkedin.com/edu/southampton-solent-university-12644
';
$setting = new admin_setting_configtextarea('theme_solent/socialmenuitems', $name, $desc, $default, PARAM_RAW, '50', '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

/** @var \theme_boost_admin_settingspage_tabs $settings */
$settings->add($page);
