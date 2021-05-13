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

use custom_menu_item;
use custom_menu;
use navigation_node;
use stdClass;
use action_menu;
use context_course;
use html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_solent
 * @copyright  2021 Sarah Cotton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \core_renderer {

    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
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
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();

// SU_AMEND START - Course: Header images
		if(strpos($_SERVER['REQUEST_URI'], 'course/view')){		
			$additionalheader = header_image();
			$header->imageclass = $additionalheader->imageclass;
			$header->imageselector = $additionalheader->imageselector;
		}
// SU_AMEND END
        return $this->render_from_template('theme_solent/header', $header);
    }
    
    /**
     * This renders the breadcrumbs
     * @return string $breadcrumbs
     */
    public function navbar() {
        $breadcrumbicon = get_config('theme_solent', 'breadcrumbicon');
        $excludebreadcrumbs = explode( ',', get_config('theme_solent', 'excludebreadcrumbs'));

        $breadcrumbs = html_writer::tag('span', get_string('pagepath'), array('class' => 'accesshide', 'id' => 'navbar-label'));
        $breadcrumbs .= html_writer::start_tag('nav', array('aria-labelledby' => 'navbar-label'));
        $breadcrumbs .= html_writer::start_tag('ul', array('class' => "breadcrumb "));
        foreach ($this->page->navbar->get_items() as $item) {

            if(!in_array($item->text, $excludebreadcrumbs)) {
                // Test for single space hide section name trick.
                if ((strlen($item->text) == 1) && ($item->text[0] == ' ')) {
                    continue;
                }

                $breadcrumbs .= html_writer::start_tag('li');
                $breadcrumbs .= $this->render($item);
                if(!$item->is_last()) {
                    $breadcrumbs .= html_writer::tag('span', '', array('class' => 'icon ' . $breadcrumbicon));
                }
                $breadcrumbs .= html_writer::end_tag('li');                
            }
        }
        $breadcrumbs .= html_writer::end_tag('ul');
        $breadcrumbs .= html_writer::end_tag('nav');

        return $breadcrumbs;
    }

}
