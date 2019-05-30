<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_solent_get_main_scss_content($theme) {
    global $CFG;
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_solent', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_solent and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }
    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/solent/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/solent/scss/post.scss');
    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

function su_unit_descriptor_course($course){
	global $CFG;
	require_once('../config.php');
	$category = core_course_category::get($course->category, IGNORE_MISSING);

	if(isset($category)){
		$catname = strtolower('x'.$category->name);
		$coursecode = substr($course->shortname, 0, strpos($course->shortname, "_"));

		if(strpos($catname, 'unit pages') !== false){
			$date = html_writer::start_div('unit_start') . 'Unit runs from  ' . date('d/m/Y',$course->startdate) . ' - ' . date('d/m/Y',$course->enddate) . html_writer::end_div();

			$descriptor = $CFG->wwwroot . '/amendments/course_docs/unit_descriptors/'.$coursecode.'.doc'; //STRING TO LOCATE THE UNIT CODE .DOC
			$descriptorx = $CFG->wwwroot . '/amendments/course_docs/unit_descriptors/'.$coursecode.'.docx'; //STRING TO LOCATE THE UNIT CODE .DOCX
			$d = @get_headers($descriptor);
			$x = @get_headers($descriptorx);

			//CHECK IF THE FILE EXISTS
			if ($d[0] == 'HTTP/1.1 200 OK'){
				return $date . "<a href='".$descriptor."' class='unit_desc' target='_blank'>Unit Descriptor</a>";//IF IT DOES EXIST ADD THE LINK
			}elseif ($x[0] == 'HTTP/1.1 200 OK'){
				return $date . "<a href='".$descriptorx."'  class='unit_desc' target='_blank'>Unit Descriptor</a>";//IF IT DOES EXIST ADD THE LINK
			}else{
				return $date . "<span class='unit_desc'>No unit descriptor available</span>";//IF IT DOSN'T EXIST ADD ALTERNATIVE LINK
			}

			clearstatcache();
		}

		if(strpos($catname, 'course pages') !== false){
			return '<a href="http://learn.solent.ac.uk/mod/data/view.php?d=288&perpage=1000&search='. $course->idnumber .'&sort=0&order=ASC&advanced=0&filter=1&f_1174=&f_1175=&f_1176=&f_1177=&f_1178=&f_1179=&f_1180=&u_fn=&u_ln="  class="unit_desc" target="_blank">External Examiner Report</a>';
		}
	}
}
