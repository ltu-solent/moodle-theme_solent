<?php
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

$plugin->version = '2020102703';
$plugin->requires = '2018120302.00';
$plugin->release = "v3.9.3";

$plugin->component = 'theme_solent';

$plugin->dependencies = [
    'theme_boost' => '2018120300'
];
