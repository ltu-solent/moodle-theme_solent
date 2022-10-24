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
 * Theme helper class. Bundle of functions to do general tasks
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent;

use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class helper {
    /**
     * Takes a string text similar to custommenu format and builds a structure for output.
     * There is no depth to this menu, it's just a flat list. No language.
     *
     * @param string $text Format "Text|url|Title attribute|css class"
     * @return array
     */
    public static function convert_text_to_menu($text) {
        $menu = [];
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            $item = new stdClass();
            // Parse item settings.
            $item->text = null;
            $item->url = null;
            $item->title = null;
            $item->class = null;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if (!empty($setting)) {
                    switch ($i) {
                        case 0: // Menu text.
                            $item->text = ltrim($setting, '-');
                            break;
                        case 1: // URL.
                            try {
                                $item->url = new moodle_url($setting);
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL.
                                $item->url = null;
                            }
                            break;
                        case 2: // Title attribute.
                            $item->title = $setting;
                            break;
                        case 3: // Class on the link.
                            $item->class = $setting;
                            break;
                    }
                }
            }
            $menu[] = $item;
        }

        return $menu;
    }
}
