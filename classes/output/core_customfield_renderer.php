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

/**
 * Class field_data
 *
 * @package    theme_solent
 * @copyright  2024 Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_customfield_renderer extends \core_customfield\output\renderer {
    /**
     * Render single custom field value
     *
     * @param \core_customfield\output\field_data $field
     * @return string HTML
     */
    protected function render_field_data(\core_customfield\output\field_data $field) {
        $context = $field->export_for_template($this);
        if ($context->shortname == 'related_courses') {
            $value = explode(',', $context->value);
            $value = join(', ', $value);
            $context->value = $value;
        }
        return $this->render_from_template('core_customfield/field_data', $context);
    }
}
