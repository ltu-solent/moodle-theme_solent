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
 * Tests for helper functions
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_solent;

use advanced_testcase;
use core\context;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Helper class tests
 */
final class helper_test extends \advanced_testcase {
    /**
     * Test show unit descriptor
     * @covers \theme_solent\helper::course_unit_descriptor
     * @return void
     */
    public function test_course_unit_descriptor(): void {
        $this->resetAfterTest();
        set_config('moduledescriptor', 'Module descriptor: [modulecode] [startenddates]', 'theme_solent');
        set_config('coursedescriptor', 'Course descriptor: [modulecode]', 'theme_solent');
        $modulecat = $this->getDataGenerator()->create_category(['name' => 'Modules', 'idnumber' => 'modules_ABCDEF']);
        $coursecat = $this->getDataGenerator()->create_category(['name' => 'Courses', 'idnumber' => 'courses_ABCDEF']);
        $othercat = $this->getDataGenerator()->create_category(['name' => 'Other', 'idnumber' => 'other_ABCDEF']);
        $hiddencat = $this->getDataGenerator()->create_category([
            'name' => 'Hidden',
            'idnumber' => 'hidden_HIDDEN',
            'visible' => 0,
        ]);
        $module = $this->getDataGenerator()->create_course([
            'fullname' => 'Module course title',
            'shortname' => 'ABC101_A_SEM1_2023/34',
            'idnumber' => 'ABC101_A_SEM1_2023/34',
            'startdate' => strtotime('2023-09-25 01:00:00'),
            'endate' => strtotime('2023-01-10 23:59:59'),
            'category' => $modulecat->id,
        ]);
        // Hidden module in a visible category.
        $hiddenmodule = $this->getDataGenerator()->create_course([
            'fullname' => 'Module course title',
            'shortname' => 'ABC102_A_SEM1_2023/34',
            'idnumber' => 'ABC102_A_SEM1_2023/34',
            'startdate' => strtotime('2023-09-25 01:00:00'),
            'endate' => strtotime('2023-01-10 23:59:59'),
            'category' => $modulecat->id,
            'visible' => 0,
        ]);
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'Course course title',
            'shortname' => 'XXBSCABCD',
            'idnumber' => 'XXBSCABCD',
            'category' => $coursecat->id,
        ]);
        $other = $this->getDataGenerator()->create_course([
            'fullname' => 'Other course title',
            'shortname' => 'OTHER',
            'idnumber' => 'OTHER',
            'category' => $othercat->id,
        ]);
        // Course itself isn't hidden, just the category it's in.
        $hidden = $this->getDataGenerator()->create_course([
            'fullname' => 'Hidden cat course title',
            'shortname' => 'hiddenCat',
            'idnumber' => 'hiddenCat',
            'category' => $hiddencat->id,
        ]);
        // Test with user that can see hidden courses, but not hidden categories.
        $halfhalf = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();

        $systemcontext = context\system::instance();
        $halfhalfroleid = $this->getDataGenerator()->create_role(['archetype' => 'manager']);
        // Give the manager role with the capability to manage data requests.
        assign_capability('moodle/category:viewhiddencategories', CAP_PREVENT, $halfhalfroleid, $systemcontext->id, true);
        assign_capability('moodle/course:viewhiddencourses', CAP_ALLOW, $halfhalfroleid, $systemcontext->id, true);
        $this->getDataGenerator()->enrol_user($teacher->id, $module->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $hiddenmodule->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $other->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $hidden->id, 'editingteacher');
    }

    /**
     * Test get category type, mostly used to distinguish modules and courses
     *
     * @param array $category
     * @param string $response
     * @return void
     * @covers \theme_solent\helper::get_category_type
     * @dataProvider get_category_type_provider
     */
    public function test_get_category_type($category, $response): void {
        $this->resetAfterTest();
        $cat = $this->getDataGenerator()->create_category($category);
        $type = helper::get_category_type($cat);
        $this->assertEquals($response, $type);
    }

    /**
     * Provider for test_get_category_type
     *
     * @return array
     */
    public static function get_category_type_provider(): array {
        return [
            'modules' => [
                'category' => [
                    'name' => 'Modules',
                    'idnumber' => 'modules_ABC101',
                ],
                'response' => 'modules',
            ],
            'courses' => [
                'category' => [
                    'name' => 'Courses',
                    'idnumber' => 'courses_ABC101',
                ],
                'response' => 'courses',
            ],
            'empty' => [
                'category' => [
                    'name' => 'Nothing special',
                    'idnumber' => '',
                ],
                'response' => '',
            ],
            'random' => [
                'category' => [
                    'name' => 'RANDOM',
                    'idnumber' => 'RANDOM',
                ],
                'response' => 'RANDOM',
            ],
        ];
    }

    /**
     * Test for is_module
     *
     * @param string|null $category
     * @param bool $response
     * @return void
     * @covers \theme_solent\helper::is_module
     * @dataProvider is_module_provider
     */
    public function test_is_module($category, $response): void {
        $this->resetAfterTest();
        $catid = null;
        if ($category) {
            $cat = $this->getDataGenerator()->create_category(['idnumber' => $category]);
            $catid = $cat->id;
        }
        $course = $this->getDataGenerator()->create_course(['category' => $catid]);
        $ismodule = helper::is_module($course->category);
        $this->assertEquals($response, $ismodule);
    }

    /**
     * Is_module provider
     *
     * @return array
     */
    public static function is_module_provider(): array {
        return [
            'modules' => [
                'category' => 'modules_ABC',
                'response' => true,
            ],
            'courses' => [
                'category' => 'courses_ABC',
                'response' => false,
            ],
            'empty' => [
                'category' => null,
                'response' => false,
            ],
            'random' => [
                'category' => 'RANDOM',
                'response' => false,
            ],
        ];
    }

    /**
     * Test for is_course
     *
     * @param string|null $category
     * @param bool $response
     * @return void
     * @covers \theme_solent\helper::is_module
     * @dataProvider is_course_provider
     */
    public function test_is_course($category, $response): void {
        $this->resetAfterTest();
        $catid = null;
        if ($category) {
            $cat = $this->getDataGenerator()->create_category(['idnumber' => $category]);
            $catid = $cat->id;
        }
        $course = $this->getDataGenerator()->create_course(['category' => $catid]);
        $ismodule = helper::is_course($course->category);
        $this->assertEquals($response, $ismodule);
    }

    /**
     * Is_course provider
     *
     * @return array
     */
    public static function is_course_provider(): array {
        return [
            'modules' => [
                'category' => 'modules_ABC',
                'response' => false,
            ],
            'courses' => [
                'category' => 'courses_ABC',
                'response' => true,
            ],
            'empty' => [
                'category' => null,
                'response' => false,
            ],
            'random' => [
                'category' => 'RANDOM',
                'response' => false,
            ],
        ];
    }
}
