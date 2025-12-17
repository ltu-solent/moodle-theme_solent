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
 * h5pactivity renderer overrider
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent\output;

use core_h5p\output\renderer as renderer_base;

/**
 * Renderer class
 */
class core_h5p_renderer extends renderer_base {
    /**
     * Alter which stylesheets are loaded for H5P.
     *
     * @param array|object $styles List of stylesheets that will be loaded
     * @param array $libraries Array of libraries indexed by the library's machineName
     * @param string $embedtype Possible values: div, iframe, external, editor
     */
    public function h5p_alter_styles(&$styles, $libraries, $embedtype) {
        global $CFG;
        $styles[] = (object) [
            'path'    => $CFG->wwwroot . '/theme/solent/style/h5pstyle.css',
            'version' => '?ver=0.0.3',
        ];
    }
}
