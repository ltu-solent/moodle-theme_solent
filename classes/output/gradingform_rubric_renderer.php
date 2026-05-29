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

use gradingform_rubric_controller;
use stdClass;

/**
 * Class gradingform_renderer
 *
 * @package    theme_solent
 * @copyright  2026 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_rubric_renderer extends \gradingform_rubric_renderer {

    /**
     * This function returns html code for displaying rubric. Depending on $mode it may be the code
     * to edit rubric, to preview the rubric, to evaluate somebody or to review the evaluation.
     *
     * It is very unlikely that this function needs to be overriden by theme. It does not produce
     * any html code, it just prepares data about rubric design and evaluation, adds the CSS
     * class to elements and calls the functions level_template, criterion_template and
     * rubric_template
     *
     * @param array $criteria data about the rubric design
     * @param array $options display options for this rubric. See: {@link gradingform_rubric_controller::get_default_options()}
     * @param int $mode rubric display mode, see {@link gradingform_rubric_controller}
     * @param string $elementname the name of the form element (in editor mode) or the prefix for div ids (in view mode)
     * @param array $values evaluation result
     * @return string
     */
    public function display_rubric($criteria, $options, $mode, $elementname = null, $values = null) {
        $criteriastr = '';
        $cnt = 0;
        $context = new stdClass();
        $context->elementname = $elementname;
        $context->mode = match ($mode) {
            gradingform_rubric_controller::DISPLAY_EDIT_FULL => 'editfull',
            gradingform_rubric_controller::DISPLAY_EDIT_FROZEN => 'editfrozen',
            gradingform_rubric_controller::DISPLAY_PREVIEW => 'preview',
            gradingform_rubric_controller::DISPLAY_PREVIEW_GRADED => 'previewgraded',
            gradingform_rubric_controller::DISPLAY_EVAL => 'eval',
            gradingform_rubric_controller::DISPLAY_EVAL_FROZEN => 'evalfrozen',
            gradingform_rubric_controller::DISPLAY_REVIEW => 'review',
            gradingform_rubric_controller::DISPLAY_VIEW => 'view',
            default => '',
        };
        $context->displayeditfull = ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL);
        $context->displayeditfrozen = ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FROZEN);
        $context->displaypreview = ($mode == gradingform_rubric_controller::DISPLAY_PREVIEW);
        $context->displaypreviewgraded = ($mode == gradingform_rubric_controller::DISPLAY_PREVIEW_GRADED);
        $context->displayeval = ($mode == gradingform_rubric_controller::DISPLAY_EVAL);
        $context->displayevalfrozen = ($mode == gradingform_rubric_controller::DISPLAY_EVAL_FROZEN);
        $context->displayreview = ($mode == gradingform_rubric_controller::DISPLAY_REVIEW);
        $context->displayview = ($mode == gradingform_rubric_controller::DISPLAY_VIEW);
        $context->criteria = [];
        foreach ($criteria as $id => $criterion) {
            $criterion['class'] = $this->get_css_class_suffix($cnt++, sizeof($criteria) -1);
            $criterion['id'] = $id;
            $levelsstr = '';
            $levelcnt = 0;
            if (isset($values['criteria'][$id])) {
                $criterionvalue = $values['criteria'][$id];
            } else {
                $criterionvalue = null;
            }
            $index = 1;
            $cc = new stdClass();
            $cc->class = $criterion['class'];
            $cc->id = $id;
            $cc->value = $criterionvalue;
            $cc->description = $criterion['description'];
            $cc->elementname = $elementname;
            $cc->levels = [];
            foreach ($criterion['levels'] as $levelid => $level) {
                $level['id'] = $levelid;
                $level['class'] = $this->get_css_class_suffix($levelcnt++, sizeof($criterion['levels']) -1);
                $level['checked'] = (isset($criterionvalue['levelid']) && ((int)$criterionvalue['levelid'] === $levelid));
                if ($level['checked'] && ($mode == gradingform_rubric_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_rubric_controller::DISPLAY_REVIEW || $mode == gradingform_rubric_controller::DISPLAY_VIEW)) {
                    $level['class'] .= ' checked';
                    //in mode DISPLAY_EVAL the class 'checked' will be added by JS if it is enabled. If JS is not enabled, the 'checked' class will only confuse
                }
                if (isset($criterionvalue['savedlevelid']) && ((int)$criterionvalue['savedlevelid'] === $levelid)) {
                    $level['class'] .= ' currentchecked';
                }
                $level['tdwidth'] = 100/count($criterion['levels']);
                $level['index'] = $index;
                $level['elementname'] = $elementname;
                $cc->levels[] = (object)$level;
                $levelsstr .= $this->level_template($mode, $options, $elementname, $id, $level);
                $index++;
            }
            $context->criteria[] = $cc;
            // $criteriastr .= $this->criterion_template($mode, $options, $elementname, $criterion, $levelsstr, $criterionvalue);
            $criteriastr .= $this->render_from_template('theme_solent/gradingform_rubric_criterion', $cc);
        }
        return $this->rubric_template($mode, $options, $elementname, $criteriastr);
    }

    public function rubric_template($mode, $options, $elementname, $criteriastr) {
        // CSS suffix for class of the main div. Depends on the mode.
        $classsuffix = match ($mode) {
            gradingform_rubric_controller::DISPLAY_EDIT_FULL => ' editor editable',
            gradingform_rubric_controller::DISPLAY_EDIT_FROZEN => ' editor frozen',
            gradingform_rubric_controller::DISPLAY_PREVIEW,
            gradingform_rubric_controller::DISPLAY_PREVIEW_GRADED => ' editor preview',
            gradingform_rubric_controller::DISPLAY_EVAL => ' evaluate editable',
            gradingform_rubric_controller::DISPLAY_EVAL_FROZEN => ' evaluate frozen',
            gradingform_rubric_controller::DISPLAY_REVIEW => ' review',
            gradingform_rubric_controller::DISPLAY_VIEW => ' view',
            default => '',
        };
        $context = new stdClass();
        $context->classsuffix = $classsuffix;
        $context->elementname = $elementname;
        $context->criteriastr = $criteriastr;
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $context->addcriterion = true;
        }
        $context->editoptions = $this->rubric_edit_options($mode, $options);
        return $this->render_from_template('theme_solent/gradingform_rubric', $context);
    }
}
