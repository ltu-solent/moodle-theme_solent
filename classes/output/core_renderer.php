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

namespace theme_solent\output;

use navigation_node;
use stdClass;
use action_menu;
use context_course;
use core_course\external\course_summary_exporter;
use html_writer;
use theme_boost\output\core_renderer as core_renderer_base;
use theme_solent\helper as solent_helper;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_solent
 * @copyright  2021 Sarah Cotton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends core_renderer_base {

    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        global $CFG;
        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        $context = $this->page->context;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }
        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        if ($header->navbar == '') {
            unset($header->hasnavbar);
        }
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        $iswelcome = get_config('theme_solent', 'enablewelcome');
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            if ($iswelcome) {
                $header->welcomemessage = \core_user::welcome_message();
            }
        }

        // SU_AMEND START - Course: Header images.
        $showbanner = get_config('theme_solent', 'enablebanner');
        if ($showbanner) {
            $additionalheader = theme_solent_header_image();
            $header->imageclass = $additionalheader->imageclass;
            $header->imageselector = $additionalheader->imageselector;
        }
        $showcourseimage = get_config('theme_solent', 'enablecourseimage');
        $courseimage = false;
        if ($showcourseimage) {
            // Course context.
            if ($context->contextlevel == CONTEXT_COURSE && $this->page->course->id !== SITEID) {
                $courseimage = course_summary_exporter::get_course_image($this->page->course);
                if (!$courseimage) {
                    $courseimage = $this->get_generated_image_for_id($this->page->course->id);
                }
            } else if ($context->contextlevel == CONTEXT_MODULE && $this->page->course->id !== SITEID) {
                // Module context.
                $courseimage = \core_course\external\course_summary_exporter::get_course_image($this->page->course);
                if (!$courseimage) {
                    $courseimage = $this->get_generated_image_for_id($this->page->course->id);
                }
            }
        }
        if ($courseimage) {
            $header->courseimage = $courseimage;
        }
        // SU_AMEND END.
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * This renders the breadcrumbs
     * @return string $breadcrumbs
     */
    public function navbar(): string {
        $newnav = new \theme_solent\boostnavbar($this->page);
        return $this->render_from_template('core/navbar', $newnav);
    }

    /**
     * Return navbar if required at bottom of the page.
     *
     * @return string HTML for navbar.
     */
    public function bottom_navbar(): string {
        if (!get_config('theme_solent', 'enablebottomnavbar')) {
            return '';
        }
        $navbar =
            html_writer::start_div("bottomnavbar") .
                $this->navbar() .
            html_writer::end_div();
        return $navbar;
    }

    /**
     * Course search box for the navdrawer
     *
     * @return string Rendered HTML
     */
    public function course_search_box() {
        $data = new stdClass();
        $data->searchurl = \core_search\manager::get_course_search_url()->out(false);
        $data->value = optional_param('q', '', PARAM_TEXT);
        $data->areaids = 'core_course-course';

        return $this->render_from_template('theme_solent/course_search_box', $data);
    }

    /**
     * Gathers communications and extra dash info to be contextually incorporated.
     *
     * @return string Rendered HTML
     */
    public function solentzone() {
        $content = '';
        // Separating into different types so we can prioritise and group them.
        // More free html.
        $banners = [];
        // Use notification objects for alerts.
        $alerts = [];
        // Links to resources.
        $dashlinks = [];
        // Links to Reports.
        $reports = [];
        $notices = [];
        // Plugins.
        $pluginswithfunction = get_plugins_with_function('solentzone_alerts', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $alerts = $function($alerts);
            }
        }
        foreach ($alerts as $alert) {
            if ($alert instanceof \core\output\notification) {
                $content .= $this->render($alert);
            }
        }
        $pluginswithfunction = get_plugins_with_function('solentzone_notices', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $notices = $function($notices);
            }
        }
        foreach ($notices as $notice) {
            $content .= html_writer::div(format_text($notice), 'solentzone-notice border p-2 mb-2');
        }
        if ($content != '') {
            $content = html_writer::div($content, 'solentzone m-2');
        }
        return $content;
    }

    /**
     * Footer menu
     *
     * @return stdClass Context for footer menu template
     */
    public function solent_footer_menu() {
        $content = new stdClass();
        $content->vertical = [];
        $columns = ['study', 'organise', 'support', 'solentfutures'];
        foreach ($columns as $column) {
            $menuconfig = get_config('theme_solent', $column . 'menuitems');
            $menu = new vertical_footer_menu($menuconfig, get_string($column, 'theme_solent'));
            if ($menu->count() > 0) {
                $content->vertical[] = $this->render($menu);
            }
        }
        // Terms and conditions.
        $menuconfig = get_config('theme_solent', 'tandcsmenuitems');
        $menu = new tandcs_footer_menu($menuconfig);
        if ($menu->count() > 0) {
            $content->tandcs = $this->render($menu);
        }
        // Social links.
        $menuconfig = get_config('theme_solent', 'socialmenuitems');
        $menu = new social_footer_menu($menuconfig);
        if ($menu->count() > 0) {
            $content->social = $this->render($menu);
        }
        return $content;
    }

}
