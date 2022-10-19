<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// $THEME is defined before this page is included and we can define settings by adding properties to this global object.


$THEME->name = 'solent';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];

$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->enable_dock = false;
$THEME->haseditswitch = true;
$THEME->requiredblocks = '';
$THEME->yuicssmodules = array();

$THEME->scss = function($theme) {
    return theme_solent_get_main_scss_content($theme);
};

// Call css/scss processing functions and renderers.
$THEME->prescsscallback = 'theme_solent_get_pre_scss';
$THEME->rendererfactory = 'theme_overridden_renderer_factory';


