<?php

$THEME->doctype = 'html5';
$THEME->yuicssmodules = array();
$THEME->name = 'awesome';
$THEME->parents = array("bootstrap");
$THEME->sheets = array('moodle');
$THEME->enable_dock = true;
$THEME->supportscssoptimisation = false;

$THEME->lessvariablescallback = 'theme_elegance_less_variables';
$THEME->extralesscallback = 'theme_elegance_extra_less';

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_bootstrap_process_css';

$THEME->layouts = array(
    // Main course page.
    'course' => array(
        'file' => 'course.php',
        'regions' => array('side-post'),
        'defaultregion' => 'side-post',
        'options' => array('langmenu' => false),
    ),
    // The site home page.
    // 'frontpage' => array(
    //     'file' => 'columns1.php',
    //     'regions' => array('side-pre', 'side-post'),
    //     'defaultregion' => 'side-pre',
    //     'options' => array('nonavbar' => true),
    // ),
);

// $THEME->javascripts = array(
// );
//
// $THEME->javascripts_footer = array(
//     'moodlebootstrap', 'dock'
// );

$THEME->hidefromselector = false;
