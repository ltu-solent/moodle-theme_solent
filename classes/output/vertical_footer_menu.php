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
 * Vertical footer menu
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use stdClass;
use theme_solent\helper;

/**
 * Multicolumn menu
 */
class vertical_footer_menu implements renderable, templatable {
    /**
     * Menu title
     *
     * @var string
     */
    private $title;
    /**
     * list of links
     *
     * @var array
     */
    private $nodes;

    /**
     * Constructor
     *
     * @param string $menutext Multiline text to be parsed into a menu
     * @param string $title
     */
    public function __construct($menutext, $title = null) {
        $this->title = $title;
        $this->nodes = helper::convert_text_to_menu($menutext);
    }

    /**
     * Export template context
     *
     * @param renderer_base $output
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        $context = new stdClass();
        $context->title = $this->title;
        $context->nodes = $this->nodes;
        return $context;
    }

    /**
     * How many nodes?
     *
     * @return int
     */
    public function count() {
        return count($this->nodes);
    }
}
