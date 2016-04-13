<?php

/**
 * Logo Image URL Fetch from theme settings
 *
 * @return string
 */

if (!function_exists('get_logo_url'))
{
  function get_logo_url() {

    global $PAGE;

    $slideimage = '';

    if (theme_awesome_get_setting('logo')) {
        $slideimage = $PAGE->theme->setting_file_url('logo' , 'logo');
    }

    return $slideimage;
  }
}


function theme_awesome_get_setting($setting, $format = false) {

    global $CFG;

    require_once($CFG->dirroot . '/lib/weblib.php');

    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('awesome');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}
