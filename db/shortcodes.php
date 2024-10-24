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
 * Shortcodes to be used with Solent theme
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$shortcodes = [
    'moduledescriptor' => [
        'callback' => 'theme_solent\local\shortcodes::moduledescriptor',
        'description' => 'shortcode:moduledescriptor',
    ],
    'moduledescriptorfile' => [
        'callback' => 'theme_solent\local\shortcodes::moduledescriptorfile',
        'description' => 'shortcode:moduledescriptorfile',
    ],
    'externalexaminerlink' => [
        'callback' => 'theme_solent\local\shortcodes::externalexaminerlink',
        'description' => 'shortcode:externalexaminerlink',
    ],
];
