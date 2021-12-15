<?php
// SU_AMEND START - Theme: Header image
require('../../config.php');
global $DB;

$c = required_param('course', PARAM_INT);
$o = required_param('opt', PARAM_ALPHANUM);
require_capability('moodle/course:update', context_course::instance($c));

$opt = $DB->get_record('theme_header', array('course' => $c));
if ($opt) {
    $opt->opt = $o;
    $DB->update_record('theme_header', $opt);
} else {
    $record = new stdclass();
    $record->course = $c;
    $record->opt = $o;
    $DB->insert_record('theme_header', $record);
}

header( 'Location: '.$CFG->wwwroot ."/course/view.php?id=". $c);
// SSU_AMEND END
