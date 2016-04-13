<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Logo file setting.
    $name = 'theme_awesome/logo';
    $title = get_string('logo','theme_awesome');
    $description = get_string('logodesc', 'theme_awesome');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
