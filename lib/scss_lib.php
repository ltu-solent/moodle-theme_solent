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
 * Theme scss lib functions
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function theme_solent_get_main_scss_content($theme) {
    global $CFG;
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();
    $iterator = new DirectoryIterator($CFG->dirroot . '/theme/solent/scss/preset/');
    $presetisset = '';
    foreach ($iterator as $pfile) {
        if (!$pfile->isDot()) {
            $presetname = substr($pfile, 0, strlen($pfile) - 5); // Name - '.scss'.
            if ($filename == $presetname) {
                $scss .= file_get_contents($CFG->dirroot . '/theme/solent/scss/preset/' . $pfile);
                $presetisset = true;
            }
        }
    }
    if (!$presetisset) {
        $filename .= '.scss';
        if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_solent', 'preset', 0    , '/', $filename))) {
            $scss .= $presetfile->get_content();
        } else {
            // Safety fallback - maybe new installs etc.
            $scss .= file_get_contents($CFG->dirroot . '/theme/solent/scss/preset/default.scss');
        }
    }
    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_solent_get_extra_scss($theme) {
    // Adapted from Boost to allow other changes or settings if required.
    $extrascss = '';
    if (!empty($theme->settings->scss)) {
        $extrascss .= $theme->settings->scss;
    }

    return $extrascss;
}

function theme_solent_get_pre_scss($theme) {
    $prescss = '';
    $configurable = [
        'fontfamily' => ['font-family-base'],
        'fontsizebase' => ['font-size-base']
    ];
    foreach ($configurable as $configkey => $targets) {
        $value = $theme->settings->{$configkey};
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$prescss, $value) {
            $prescss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $prescss .= $theme->settings->scsspre;
    }

    return $prescss;
}