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
 * Theme solent strings
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Advanced settings';
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['blockwidth'] = 'Block width';
$string['blockwidth_desc'] = 'Block area width in pixels';
$string['brandcolor'] = 'Brand colour';
$string['brandcolor_desc'] = 'The accent colour.';
$string['breadcrumbicon'] = 'The breadcrumb fontawesome icon.';
$string['breadcrumbicon_desc'] = 'The breadcrumb fontawesome icon.';
$string['bootswatch'] = 'Bootswatch';
$string['bootswatch_desc'] = 'A bootswatch is a set of Bootstrap variables and css to style Bootstrap';

$string['choosereadme'] = 'Solent is a modern highly-customisable theme. This theme is intended to be used directly, or as a parent theme when creating new themes utilising Bootstrap 4.';
$string['colours_settings'] = 'Colour settings';
$string['configtitle'] = 'Solent theme settings';
$string['copyright'] = '&copy; Solent University';
$string['coursedescriptor'] = 'Course descriptor';
$string['coursedescriptor_desc'] = 'This content will be displayed for Course pages. You can use filters within it. It will appear whereever the [moduledescriptor] shortcode is used. You can use HTML here.';
$string['coursedescriptorfolder'] = 'Course descriptors folder';
$string['coursedescriptorfolder_desc'] = 'ID of course descriptors folder activity';
$string['courseexternalexaminerlink'] = 'Course External examiner link';
$string['courseexternalexaminerlink_desc'] = 'Course External examiner url. Include ::IDNUMBER:: in the url. This will be replaced with the course code.';
$string['courseinstance'] = 'Instance: ';
$string['currentinparentheses'] = '(current)';
$string['customselectors'] = 'Custom selectors';
$string['customselectors_desc'] = 'This allows you to specify other types of media to which this will apply, without changing the code. e.g. Facebook videos.<br />
    Each line should contain a css selector for fitvids to operate on. e.g. <code>iframe[src*=\'facebook.com\/plugins\/video.php\']</code>
';

$string['descriptors'] = 'Module and course descriptors';
$string['descriptors_desc'] = 'Module and course descriptor settings';
$string['descriptorfolder'] = 'Module descriptors folder';
$string['descriptorfolder_desc'] = 'ID of module descriptors folder activity';
$string['drawerwidth'] = 'Navdrawer width';
$string['drawerwidth_desc'] = 'Navdrawer width in pixels';

$string['enable_fitvid'] = 'Enable fitvid';
$string['enable_fitvid_desc'] = 'Fitvid automatically fits a video to the screen size.';
$string['enableaccessibilitytool'] = 'Enable accessibility tool';
$string['enableaccessibilitytool_desc'] = 'Accessibility tool will allow users to choose their own colour scheme,
    font and font sizes for themselves.';
$string['enablebanner'] = 'Enable course banner';
$string['enablebanner_desc'] = 'This will turn on or off banners across all courses';
$string['enablebottomnavbar'] = 'Enable bottom navbar';
$string['enablebottomnavbar_desc'] = 'Display navbar at the bottom of the page to make it easier to navigate back when user is can\'t see the breadcrumbs at the top.';
$string['enablecourseimage'] = 'Enable course image';
$string['enablecourseimage_desc'] = 'Use course image as banner instead of Solent banner picker';
$string['enablenavbar'] = 'Enable navbar';
$string['enablenavbar_desc'] = 'Determines whether to display the breadcrumb trail';
$string['enablescrollspy'] = 'Enable ScrollSpy';
$string['enablescrollspy_desc'] = 'ScrollSpy remembers where on the page you were before enabling editing mode and take you back there to continue your work.';
$string['enablestudentsecondarynav'] = 'Enable student secondary nav';
$string['enablestudentsecondarynav_desc'] = 'The secondary nav is below the banner and includes Course - other users may be affected by turning this off.';
$string['enablewelcome'] = 'Enable welcome message';
$string['enablewelcome_desc'] = 'Display welcome message when user logs in.';
$string['ends'] = 'ends';
$string['excludebreadcrumbs'] = 'Breadcrumb names to exclude (comma separated)';
$string['excludebreadcrumbs_desc'] = 'Breadcrumb names to exclude (comma separated)';
$string['excludesecondarynavitems'] = 'Exclude secondary nav items';
$string['excludesecondarynavitems_desc'] = 'Comma separated list of secondary nav items to be removed.';
$string['expandfieldsets'] = 'Auto expand fieldsets';
$string['expandfieldsets_desc'] = 'List of html element ids of groups of settings you want to always have open by default. e.g. #id_activitycompletionheader';
$string['externalexaminer'] = 'External Examiner report';
$string['externalexaminerlink'] = 'External examiner link';
$string['externalexaminerlink_desc'] = 'External examiner url. Include ::IDNUMBER:: in the url. This will be replaced with the course code.';

$string['fitvids'] = 'Responsive videos';
$string['fitvids_desc'] = 'Make videos responsive according to available screen size.';
$string['fontfamily'] = 'Font family';
$string['fontfamily_desc'] = 'This choice will apply across SOL';
$string['fontsizebase'] = 'Theme base fontsize';
$string['fontsizebase_desc'] = 'Enter a fontsize';
$string['footer_settings'] = 'Footer settings';

$string['generalsettings'] = 'General settings';

$string['headerdefaultimage'] = 'Default header image';
$string['headerdefaultimage_desc'] = 'Default image for course headers and non-course pages';
$string['headerimagecurrent'] = 'Option {$a->opt} is currently selected.';
$string['headerimageinstructions'] = 'Click the radio button next to an image to select it. You will automatically redirected back to the previous page.';
$string['hidenodesprimarynavigationsetting'] = 'Hide nodes in primary navigation';
$string['hidenodesprimarynavigationsetting_desc'] = 'With this setting, you can hide one or multiple nodes from the primary navigation.';

$string['ignoreselectors'] = 'Ignore selectors';
$string['ignoreselectors_desc'] = 'Ignore these selectors. One per line.';
$string['imagesettings'] = 'Image settings';

$string['layoutsettings'] = 'Layout settings';
$string['loginimage'] = 'Default Login image';
$string['loginimage_desc'] = 'Background image for login page';

$string['moduledescriptor'] = 'Module descriptor';
$string['moduledescriptor_desc'] = 'This content will be displayed for Module pages. You can use filters within it. It will appear whereever the [moduledescriptor] shortcode is used. You can use HTML here.';
$string['moduledescriptorfile'] = 'Returns the url to the Module descriptor file.';
$string['moduledescriptorfor'] = 'Module descriptor for {$a}';
$string['modulerunsfrom'] = 'Module runs: ';

$string['navbarheight'] = 'Navbar height';
$string['navbarheight_desc'] = 'Navbar height in pixels';
$string['navigationsettings'] = 'Navigation settings';
$string['nomoduledescriptor'] = 'No module descriptor available';
$string['nobootswatch'] = 'None';

$string['organise'] = 'Organise';
$string['organisemenuitems'] = 'Organise menu items';
$string['organisemenuitems_desc'] = 'Organise menu items';

$string['pluginname'] = 'Solent';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Solent_Presets">Solent presets</a> for information on creating and sharing your own preset files, and see the <a href="http://moodle.net/Solent">Presets repository</a> for presets that others have shared.';
$string['presetsettings'] = 'Preset settings';
$string['privacy:metadata'] = 'The Solent theme does not store any personal data about any user.';
$string['privacy:metadata:preference:draweropennav'] = 'The user\'s preference for hiding or showing the drawer menu navigation.';
$string['privacy:drawernavclosed'] = 'The current preference for the navigation drawer is closed.';
$string['privacy:drawernavopen'] = 'The current preference for the navigation drawer is open.';

$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['region-side-pre'] = 'Right';
$string['resources_settings'] = 'Resources settings';
$string['runsfrom'] = 'runs from';
$string['runsto'] = 'runs to';

$string['shortcode:externalexaminerlink'] = 'Outputs a link to the external examiner report';
$string['shortcode:moduledescriptor'] = 'Outputs a module descriptor that is context dependent.';
$string['shortcode:moduledescriptorfile'] = 'Returns the url to the Module descriptor file.';
$string['socialmenuitems'] = 'Social menu items';
$string['socialmenuitems_desc'] = 'Social menu items';
$string['solentfutures'] = 'Solent Futures';
$string['solentfuturesmenuitems'] = 'Solent Futures menu items';
$string['solentfuturesmenuitems_desc'] = 'Solent Futures menu items';
$string['startdate'] = 'Start date: {$a}';
$string['startenddates'] = 'Course start and ends dates';
$string['starts'] = 'starts';
$string['study'] = 'Study';
$string['studymenuitems'] = 'Study menu items';
$string['studymenuitems_desc'] = 'Study menu items';
$string['support'] = 'Support';
$string['supportmenuitems'] = 'Support menu items';
$string['supportmenuitems_desc'] = 'Support menu items';

$string['tabcolor'] = 'Tab colour';
$string['tabcolor_desc'] = 'Course page tab colour';
$string['tandcsmenuitems'] = 'Terms & Conditions menu items';
$string['tandcsmenuitems_desc'] = 'Terms & Conditions menu items';
$string['totop'] = 'Back to top';

$string['vidmaxwidth'] = 'Video MaxWidth';
$string['vidmaxwidth_desc'] = 'Resizes videos, but limited the width to this size. 0 means, it will fill large screens.';
$string['vidmaxheight'] = 'Video MaxHeight';
$string['vidmaxheight_desc'] = 'Resizes videos, but limited the height to this size. 0 means, it will fill large screens.';
