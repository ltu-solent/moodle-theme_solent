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
 * Theme config file
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'solent';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->editor_scss = [];
$THEME->parents = ['boost'];

$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->enable_dock = false;
$THEME->haseditswitch = true;
$THEME->requiredblocks = '';
$THEME->yuicssmodules = [];

$THEME->scss = function($theme) {
    return theme_solent_get_main_scss_content($theme);
};

// Call css/scss processing functions and renderers.
$THEME->prescsscallback = 'theme_solent_get_pre_scss';
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->removedprimarynavitems = explode(',', get_config('theme_solent', 'hidenodesprimarynavigation'));
$THEME->activityheaderconfig = [
    'notitle' => true,
];
