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
 * Override boostnavbar so we can manage our own links
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent;

/**
 * Override boostnavbar
 */
class boostnavbar extends \theme_boost\boostnavbar {
    /**
     * Prepares the navigation nodes for use with boost.
     */
    protected function prepare_nodes_for_boost(): void {
        global $PAGE;

        // Remove the navbar nodes that already exist in the primary navigation menu.
        // phpcs:disable
        // $this->remove_items_that_exist_in_navigation($PAGE->primarynav);
        // phpcs:enable
        // Defines whether section items with an action should be removed by default.
        $removesections = true;

        if ($this->page->context->contextlevel == CONTEXT_COURSECAT) {
            // Remove the 'Permissions' navbar node in the Check permissions page.
            if ($this->page->pagetype === 'admin-roles-check') {
                $this->remove('permissions');
            }
        }

        if ($this->page->context->contextlevel == CONTEXT_COURSE) {
            // Remove any duplicate navbar nodes.
            $this->remove_duplicate_items();
            // Remove 'My courses' and 'Courses' if we are in the course context.
            $this->remove('mycourses');
            $this->remove('courses');
            // Remove the course category breadcrumb node.
            $this->remove($this->page->course->category, \breadcrumb_navigation_node::TYPE_CATEGORY);
            // SU_AMEND_START: We want the course in the breadcrumb.
            // Remove the course breadcrumb node.
            // phpcs:disable
            // $this->remove($this->page->course->id, \breadcrumb_navigation_node::TYPE_COURSE);
            // Remove the navbar nodes that already exist in the secondary navigation menu.
            // $this->remove_items_that_exist_in_navigation($PAGE->secondarynav);
            // phpcs:enable
            // SU_AMEND_END.
            switch ($this->page->pagetype) {
                case 'group-groupings':
                case 'group-grouping':
                case 'group-overview':
                case 'group-assign':
                    // Remove the 'Groups' navbar node in the Groupings, Grouping, group Overview and Assign pages.
                    $this->remove('groups');
                case 'backup-backup':
                case 'backup-restorefile':
                case 'backup-copy':
                case 'course-reset':
                    // Remove the 'Import' navbar node in the Backup, Restore, Copy course and Reset pages.
                    $this->remove('import');
                case 'course-user':
                    $this->remove('mygrades');
                    $this->remove('grades');
            }
        }
        $excludebreadcrumbs = explode(',', get_config('theme_solent', 'excludebreadcrumbs'));
        foreach ($excludebreadcrumbs as $breadcrumb) {
            // Allows for deleting by key, or guessing the key. e.g. "My Courses" becomes "mycourses".
            $breadcrumb = str_replace(' ', '', strtolower($breadcrumb));
            $this->remove($breadcrumb);
        }
    }
}
