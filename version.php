<?php

defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2016040100;
$plugin->requires  = 2015051100;
$plugin->maturity  = MATURITY_STABLE;
$plugin->release = '1.0';
$plugin->component = 'theme_awesome';

$plugin->dependencies = array(
    'format_onetopic'  => 2016020501,
    'bootstrap' => 2015103000
);
