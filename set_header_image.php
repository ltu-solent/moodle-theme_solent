<?php
// SU_AMEND START - Theme: Header image
require('../../config.php');
global $DB;

$c = required_param('course', PARAM_TEXT);
$o = required_param('opt', PARAM_TEXT);

$record = new stdclass;
$record->course = $c;
$record->opt = $o;

$opt = $DB->get_record('theme_header', array('course' => $course), '*');

$sql = "UPDATE {theme_header} SET opt = ? WHERE course = ?";

$result = $DB->execute($sql, array($o, $c));

header( 'Location: '.$CFG->wwwroot ."/course/view.php?id=". $c) ; 
// SSU_AMEND END
?>