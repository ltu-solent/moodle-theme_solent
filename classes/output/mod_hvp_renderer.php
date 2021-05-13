<?php

class theme_solent_mod_hvp_renderer extends mod_hvp_renderer {

    public function hvp_alter_styles(&$styles, $libraries, $embedType) {
        global $CFG;
        $styles[] = (object) array(
            'path'    => $CFG->wwwroot  . '/theme/solent/style/style.css',
            'version' => '?ver=0.0.1',
        );
    }
}