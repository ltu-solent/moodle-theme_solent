<?php
require_once('../../../config.php');

$course = required_param('course', PARAM_INT);
$opt = required_param('opt', PARAM_ALPHANUM);
$coursecontext = context_course::instance($course);
require_capability('moodle/course:update', $coursecontext);

$PAGE->set_context($coursecontext);
$PAGE->set_url(new moodle_url('/theme/solent/layout/header_options.php', array('course' => $course, 'opt' => $opt)));
$PAGE->set_title('Header options');
$PAGE->set_heading('Header options');
$PAGE->set_pagelayout('report');

$PAGE->requires->js_call_amd('theme_solent/headerimage', 'init', [$CFG->wwwroot]);

echo $OUTPUT->header();

$dir = $CFG->dirroot . '/theme/solent/pix/unit-header/01.png';

$dir = dirname($dir);
$files = scandir($dir);
natsort($files);

$options = null;



foreach ($files as $k=>$v) {
	$name = substr($v, 0, strpos($v, "."));
	//Check if the file is an image
	if(strpos($v, 'png') || strpos($v, 'jpg') || strpos($v, 'jpeg')){

		if($opt == $name){
			$checked = 'checked="checked"';
		}else{
			$checked = "";
		}

		$options .= '<tr>
						<td align="left"><input type="radio" id="' . $name . '" name="opt" value="' . $name .'" ' . $checked . '"></td>
						<td><label for="opt">Option ' . $name. '<img class="header-image-option" src="../pix/unit-header/' . $v . '"></label></td>
					</tr>';
	}
}

$templatecontext = [
	'currentoptiontext'=> get_string('headerimagecurrent', 'theme_solent', array('opt'=>$opt)),
	'instructiontext'=> get_string('headerimageinstructions', 'theme_solent'),
	'action'=> $CFG->wwwroot .'/theme/solent/set_header_image.php',
	'formid'=> 'header-image-form',
	'options'=> $options,
	'course'=> $course
];

echo $OUTPUT->render_from_template('theme_solent/header_image_form', $templatecontext);

echo $OUTPUT->footer();
