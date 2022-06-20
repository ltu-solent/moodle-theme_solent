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

function theme_solent_get_pre_scss($theme) {                                                                                         
    global $CFG;                                                                                                                    
 
    $scss = '';                                                                                                                     
    $configurable = [                                                                                                                                                                                                    
        // 'highlightcolor' => ['highlight-color'],                                                                                                                                                                                            
        'brandcolor' => ['brand-color'],                                                                                                                                                                                            
        'tabcolor' => ['tab-color'],                                                                                                                                                                                            
        'drawerwidth' => ['drawer-width'],                                                                                                                                                                                            
        'blockwidth' => ['block-width'],                                                                                                                                                                                            
        'navbarheight' => ['navbar-height'],                                                                                                                                                                                            
        'fontsizebase' => ['font-size-base'],                                                                                                                                                                                            
    ];                                                                                                                              
 
    // Prepend variables first.                                                                                                     
    foreach ($configurable as $configkey => $targets) {                                                                             
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;                                     
        if (empty($value)) {                                                                                                        
            continue;                                                                                                               
        }                                                                                                                           
        array_map(function($target) use (&$scss, $value) {                                                                          
            $scss .= '$' . $target . ': ' . $value . ";\n";                                                                         
        }, (array) $targets);                                                                                                       
    }                                                                                                                               
 
    // Prepend pre-scss.                                                                                                            
    if (!empty($theme->settings->scsspre)) {                                                                                        
        $scss .= $theme->settings->scsspre;                                                                                         
    }                                                                                                                               
 
    return $scss;                                                                                                                   
}

function unit_descriptor_course($course){
	global $CFG, $PAGE;
	require_once('../config.php');
	$category = core_course_category::get($course->category, IGNORE_MISSING);

	if(isset($category)){
		$catname = strtolower('x'.$category->idnumber);
		$coursecode = substr($course->shortname, 0, strpos($course->shortname, "_"));

		if(strpos($catname, 'modules_') !== false){
			$details = html_writer::start_div('unit-details');
			if($PAGE->bodyid != 'page-course-search'){
				$details .= html_writer::start_div('unit-start') . get_string('modulerunsfrom', 'theme_solent') . date('d/m/Y',$course->startdate) . ' - ' . date('d/m/Y',$course->enddate) 
				. html_writer::end_div();				
			}
            
            $details .= html_writer::start_div('course-instance') . get_string('courseinstance', 'theme_solent') . $course->shortname . html_writer::end_div();
			
            if($coursecode != '') {
                $descriptor = get_file($coursecode);
            } else {
                $descriptor = null;
            }  
            
            if($descriptor){
                return $details . "<a href='".$descriptor."' class='unit-desc'>". get_string('moduledescriptor', 'theme_solent') ."</a></div>";
            }else{
                return $details . "<span class='unit-desc'>". get_string('nomoduledescriptor', 'theme_solent') ."</span></div>";
            }
		}

		if(strpos($catname, 'courses_') !== false){
			  $external = html_writer::start_div('unit-details');
			  $external .= html_writer::start_div('external') . '<a href="http://learn.solent.ac.uk/mod/data/view.php?d=288&perpage=1000&search='.
						  $course->idnumber .'&sort=0&order=ASC&advanced=0&filter=1&f_1174=&f_1175=&f_1176=&f_1177=&f_1178=&f_1179=&f_1180=&u_fn=&u_ln="
						  class="unit_desc" target="_blank">'. get_string('externalexaminer', 'theme_solent') .'</a>' .html_writer::end_div();
			  $external .= html_writer::end_div();
			  return $external;
		}
	}
}

function get_file_details($coursecode){
    global $DB;
    $file = $DB->get_record_sql("	SELECT f.id, filename, contextid, filepath FROM {files} f
									JOIN {context} ctx ON ctx.id = f.contextid
                                    WHERE ctx.instanceid = ?
                                    AND (component = ? AND filearea = ?)
									AND filename LIKE '$coursecode%' 
									ORDER BY timemodified DESC", array(get_config('theme_solent', 'descriptors'), "mod_folder", "content"));
    if($file){
      return $file;
    }else{
      return null;
    }
}

function get_file($coursecode){
	$file = get_file_details($coursecode);
	if($file){
		$url= moodle_url::make_pluginfile_url($file->contextid,'mod_folder','content', 0,$file->filepath,$file->filename, true);
		return $url;
	}else{
		return null;
	}
}

function header_image(){
	global $DB,$COURSE,$PAGE;
	$oncoursepage = strpos($_SERVER['REQUEST_URI'], 'course/view');
	$header = new stdClass();
	if($COURSE->id > 1 && $oncoursepage != false){		
		$opt = $DB->get_record('theme_header', array('course' => $COURSE->id), '*');
		if($opt){
		  $opt = $opt->opt;
		}else{
		  $record = new stdclass;
		  $record->id = null;
		  $record->course = $COURSE->id;

		  $currentcategory = $DB->get_record('course_categories', array('id' => $COURSE->category), '*');
		  $catname = strtolower('x'.$currentcategory->name);
		  if(isset($catname)){
			if(strpos($catname, 'course pages') !== false){
			  $record->opt = '08';
			  $DB->insert_record('theme_header', $record, $returnid=true);
			  $opt = '08';
			}else{
			  $record->opt = '01';
			  $DB->insert_record('theme_header', $record, $returnid=true);
			  $opt = '01';
			}
		  }
		}
		
		if($PAGE->user_is_editing()){
			$url = new moodle_url('/theme/solent/layout/header_options.php', array('course' => $COURSE->id, 'opt' => $opt));
			$header->imageselector = '<div class="header-image-link btn"><a class="btn btn-secondary" href="' . $url . '">Select header image</a></div>';
		}else{
			$header->imageselector = null;
		}			
		
		$header->imageclass = 'header-image opt'. $opt;			
		
	}else{		
		$header->imageclass = null;		  
		$header->imageselector = null;		
	}

	$imageselector = '';
	$oncoursepage = strpos($_SERVER['REQUEST_URI'], 'course/view');
	if ($PAGE->user_is_editing() && $oncoursepage != false){
	  if ($COURSE->id > 1){
		$url = new moodle_url('/theme/solent/layout/header_options.php', array('course' => $COURSE->id, 'opt' => $opt));
		$imageselector = '<div class="header-image-link btn"><a class="btn btn-secondary" href="' . $url . '">Select header image</a></div>';
	  }
	}
	
	$header = new stdClass();
	if ($oncoursepage != false && $COURSE->id > 1 ){		
		$header->imageclass = 'header-image opt'. $opt;		  
		$header->imageselector = $imageselector;
	}else{
		$header->imageclass = null;		  
		$header->imageselector = null;
	}
		
	return $header;
}

/**
 * Placeholder function to generate alerts within the theme.
 *
 * @param array $alerts of the \core\output\notification type
 * @return array
 */
function theme_solent_solentzone_alerts($alerts) {
	$alerts[] = 'Bad alert';
	return $alerts;
}

/**
 * Placeholder function to generate dashlinks.
 *
 * @param array $dashlinks
 * @return array
 */
function theme_solent_solentzone_dashlinks($dashlinks) {
	return $dashlinks;
}
