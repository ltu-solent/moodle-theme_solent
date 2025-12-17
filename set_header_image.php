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
 * Old set header image
 *
 * @package   theme_solent
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


// SU_AMEND START - Theme: Header image.
require('../../config.php');
global $DB;

$c = required_param('course', PARAM_INT);
$o = required_param('opt', PARAM_ALPHANUM);
require_login($c);
require_capability('moodle/course:update', core\context\course::instance($c));

$opt = $DB->get_record('theme_header', ['course' => $c]);
if ($opt) {
    $opt->opt = $o;
    $DB->update_record('theme_header', $opt);
} else {
    $record = new stdclass();
    $record->course = $c;
    $record->opt = $o;
    $DB->insert_record('theme_header', $record);
}

header('Location: ' . $CFG->wwwroot . "/course/view.php?id=" . $c);
// SSU_AMEND END.
