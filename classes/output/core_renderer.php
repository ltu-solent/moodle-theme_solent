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

use custom_menu_item;
use custom_menu;
use navigation_node;
use stdClass;
use action_menu;
use context_course;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_solent
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \core_renderer {

    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        global $PAGE, $DB, $COURSE, $CFG;

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();

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

		$imageselector = '';
		$oncoursepage = strpos($_SERVER['REQUEST_URI'], 'course/view');
		if ($PAGE->user_is_editing() && $oncoursepage != false){
		  if ($COURSE->id > 1){
			$option = $DB->get_record('theme_header', array('course' => $COURSE->id), '*');
			$dir = $CFG->dirroot . '/theme/solent/pix/unit-header';
			$files = scandir($dir);
			array_splice($files, 0, 1);
			array_splice($files, 0, 1);

			$options = array();
			foreach ($files as $k=>$v) {
			  $img = substr($v, 0, strpos($v, "."));
			  $options[$img] = $img;
			}
			natsort($options);
			
			$imageselector .=	'<div class="divcoursefieldset"><fieldset class="coursefieldset fieldsetheader">
				 <form action="'. $CFG->wwwroot .'/theme/solent/set_header_image.php" method="post">
				 <label for="opt">Select header image (<a href="/theme/solent/pix/unit-header/options.php" target="_blank">browse options</a>):&nbsp;
				 </label><select name="opt">';

			$imageselector .= '<option value="00">No image</option>';
			foreach($options as $key=>$val){
			  if(($val != 'options') && ($val != 'succeed') && ($val != '')){
				$imageselector .= '<option value="' . $key . '"'; if($key == $option->opt)
				$imageselector .= ' selected="selected"';
				$imageselector .= '>Option ' . $val . '</option>';
			  }
			}

			$imageselector .= '  </select><input type="hidden" name="course" value="'. $COURSE->id .'"/>';
			$imageselector .= '  <input type="hidden" name="id" value="'. $option->id .'"/>';
			$imageselector .= '&nbsp;&nbsp;&nbsp;<input type="submit" value="Save">
			   </form></fieldset></div>';
		  }
		}

		if ($oncoursepage != false && $COURSE->id > 1 ){
		  $header->imageclass = 'header-image opt'. $opt;
		  $header->imageselector = $imageselector;
		}

        return $this->render_from_template('theme_solent/header', $header);
    }
}
