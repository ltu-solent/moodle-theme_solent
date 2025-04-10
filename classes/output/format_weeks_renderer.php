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

use core_courseformat\base;

/**
 * Class format_weeks_renderer
 *
 * @package    theme_solent
 * @copyright  2024 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_weeks_renderer extends \format_weeks\output\renderer {
    /**
     * Render the enable bulk editing button.
     * @param \core_courseformat\base $format the course format
     * @return string|null the enable bulk button HTML (or null if no bulk available).
     */
    public function bulk_editing_button(base $format): ?string {
        // SSU_AMEND_START: Allow disabling bulk edit button.
        $enabled = get_config('theme_solent', 'enablebulkedit');
        if ($enabled || is_siteadmin()) {
            return parent::bulk_editing_button($format);
        }
        return null;
        // SSU_AMEND_END.
    }
}
