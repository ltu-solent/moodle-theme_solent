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

use core\output\renderer_base;

/**
 * Class context_header
 *
 * @package    theme_solent
 * @copyright  2024 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_header extends \core\output\context_header {
    /**
     * Adds an array element for a formatted image.
     */
    protected function format_button_images() {

        foreach ($this->additionalbuttons as $buttontype => $button) {
            $page = $button['page'];
            // If no image is provided then just use the title.
            if (!isset($button['image'])) {
                $this->additionalbuttons[$buttontype]['formattedimage'] = $button['title'];
            } else {
                // Check to see if this is an internal Moodle icon.
                $internalimage = $page->theme->resolve_image_location('t/' . $button['image'], 'moodle');
                if ($internalimage) {
                    $this->additionalbuttons[$buttontype]['formattedimage'] = 't/' . $button['image'];
                } else {
                    // Treat as an external image.
                    $this->additionalbuttons[$buttontype]['formattedimage'] = $button['image'];
                }
            }
            // SSU_AMEND_START: Button style and padding.
            if (isset($button['linkattributes']['class'])) {
                $class = $button['linkattributes']['class'] . ' btn btn-light p-0';
            } else {
                $class = 'btn btn-light p-0';
            }
            // SSU_AMEND_END.
            // Add the bootstrap 'btn' class for formatting.
            $this->additionalbuttons[$buttontype]['linkattributes'] = array_merge(
                $button['linkattributes'],
                ['class' => $class]
            );
        }
    }
    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        // Heading.
        /* @phpstan-ignore isset.property */
        $headingtext = isset($this->heading) ? $this->heading : $output->get_page()->heading;
        // SSU_AMEND_START: Padding.
        /* @phpstan-ignore method.notFound */
        $heading = $output->heading($headingtext, $this->headinglevel, "h2 mb-2");
        // SSU_AMEND_END.
        // Buttons.
        $additionalbuttons = [];
        /* @phpstan-ignore isset.property */
        if (isset($this->additionalbuttons)) {
            foreach ($this->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }
                    if ($button['buttontype'] === 'message') {
                        \core_message\helper::messageuser_requirejs();
                    }
                }
                foreach ($button['linkattributes'] as $key => $value) {
                    $button['attributes'][] = ['name' => $key, 'value' => $value];
                }
                $additionalbuttons[] = $button;
            }
        }

        return [
            'heading' => $heading,
            'headinglevel' => $this->headinglevel,
            'imagedata' => $this->imagedata,
            'prefix' => $this->prefix,
            'hasadditionalbuttons' => !empty($additionalbuttons),
            'additionalbuttons' => $additionalbuttons,
        ];
    }
}
