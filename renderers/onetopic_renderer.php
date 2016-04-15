<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/onetopic/renderer.php');

/**
 * Awesome course renderers.
 *
 * @package    theme_awesome
 * @copyright  2016 Willian Mano - willianmano.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_awesome_format_onetopic_renderer extends format_onetopic_renderer {
    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected final function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics col-md-10 col-sm-12'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE, $OUTPUT;;

        $real_course_display = $course->realcoursedisplay;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $course->realcoursedisplay = $real_course_display;
        $sections = $modinfo->get_section_info_all();

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $format_data = new stdClass();
        $format_data->mods = $mods;
        $format_data->modinfo = $modinfo;
        $this->_course = $course;
        $this->_format_data = $format_data;

        // General section if non-empty and course_display is multiple.
        if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $thissection = $sections[0];
            if ((($thissection->visible && $thissection->available) || $canviewhidden) && ($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true);

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                }
                else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);

                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section onetopic row-fluid'));

        //Move controls
        $can_move = false;
        if ($PAGE->user_is_editing() && has_capability('moodle/course:movesections', $context) && $displaysection > 0) {
            $can_move = true;
        }
        $move_list_html = '';
        $count_move_sections = 0;

        //Init custom tabs
        $section = 0;

        $sectionmenu = array();
        $tabs = array();
        $inactive_tabs = array();

        $default_topic = -1;

        while ($section <= $course->numsections) {

            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

            $thissection = $sections[$section];

            $showsection = true;
            if (!$thissection->visible || !$thissection->available) {
                $showsection = false;
            }
            else if ($section == 0 && !($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())){
                $showsection = false;
            }

            if (!$showsection) {
                $showsection = $canviewhidden || !$course->hiddensections;
            }

            if (isset($displaysection)) {
                if ($showsection) {

                    if ($default_topic < 0) {
                        $default_topic = $section;

                        if ($displaysection == 0) {
                            $displaysection = $default_topic;
                        }
                    }

                    $format_options = course_get_format($course)->get_format_options($thissection);

                    $sectionname = get_section_name($course, $thissection);

                    if ($displaysection != $section) {
                        $sectionmenu[$section] = $sectionname;
                    }

                    $custom_styles = '';
                    $level = 0;
                    if (is_array($format_options)) {

                        if (!empty($format_options['fontcolor'])) {
                            $custom_styles .= 'color: ' . $format_options['fontcolor'] . ';';
                        }

                        if (!empty($format_options['bgcolor'])) {
                            $custom_styles .= 'background-color: ' . $format_options['bgcolor'] . ';';
                        }

                        if (!empty($format_options['cssstyles'])) {
                            $custom_styles .= $format_options['cssstyles'] . ';';
                        }

                        if (isset($format_options['level'])) {
                            $level = $format_options['level'];
                        }

                        $icon = '';
                        if (isset($format_options['icon']) && $format_options['icon'] != '') {
                            $icon = "<i class='{$format_options['icon']}'></i> ";
                        }
                    }

                    if ($section == 0) {
                        $url = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => 0));
                    } else {
                        $url = course_get_url($course, $section);
                    }

                    $special_style = 'tab_position_' . $section . ' tab_level_' . $level;
                    if ($course->marker == $section) {
                        $special_style = ' marker ';
                    }

                    if (!$thissection->visible || !$thissection->available) {
                        $special_style .= ' dimmed ';

                        if (!$canviewhidden) {
                            $inactive_tabs[] = "tab_topic_" . $section;
                        }
                    }

                    if ($icon) {
                        $new_tab = new tabobject("tab_topic_" . $section, $url,
                        '<div style="' . $custom_styles . '" class="tab_content ' . $special_style . '"><span class="icon-tab">' . $icon . s($sectionname) . "</span></div>", s($sectionname));
                    } else {
                      $new_tab = new tabobject("tab_topic_" . $section, $url,
                        '<div style="' . $custom_styles . '" class="tab_content ' . $special_style . '">' . s($sectionname) . "</div>", s($sectionname));
                    }

                    if (is_array($format_options) && isset($format_options['level'])) {

                        if($format_options['level'] == 0 || count($tabs) == 0) {
                            $tabs[] = $new_tab;
                            $new_tab->level = 1;
                        }
                        else {
                            $parent_index = count($tabs) - 1;
                            if (!is_array($tabs[$parent_index]->subtree)) {
                                $tabs[$parent_index]->subtree = array();
                            }
                            else if (count($tabs[$parent_index]->subtree) == 0) {
                                $tabs[$parent_index]->subtree[0] = clone($tabs[$parent_index]);
                                $tabs[$parent_index]->subtree[0]->id .= '_index';
                                $parent_section = $sections[$section-1];
                                $parentformat_options = course_get_format($course)->get_format_options($parent_section);
                                if ($parentformat_options['firsttabtext']) {
                                    $firsttab_text = $parentformat_options['firsttabtext'];
                                } else {
                                    $firsttab_text = get_string('index', 'format_onetopic');
                                }
                                $tabs[$parent_index]->subtree[0]->text = '<div class="tab_content tab_initial">' . $firsttab_text. "</div>";
                                $tabs[$parent_index]->subtree[0]->level = 2;

                                if($displaysection == $section - 1) {
                                    $tabs[$parent_index]->subtree[0]->selected = true;
                                }
                            }
                            $new_tab->level = 2;
                            $tabs[$parent_index]->subtree[] = $new_tab;
                        }
                    }
                    else {
                        $tabs[] = $new_tab;
                    }

                    //Init move section list***************************************************************************
                    if ($can_move) {
                        if ($section > 0) { // Move section
                            $baseurl = course_get_url($course, $displaysection);
                            $baseurl->param('sesskey', sesskey());

                            $url = clone($baseurl);

                            $url->param('move', $section - $displaysection);

                            //ToDo: For new feature: subtabs. It is not implemented yet
                            /*
                            $strsubtopictoright = get_string('subtopictoright', 'format_onetopic');
                            $url = new moodle_url('/course/view.php', array('id' => $course->id, 'subtopicmove' => 'right', 'subtopic' => $section));
                            $icon = $this->output->pix_icon('t/right', $strsubtopictoright);
                            $subtopic_move = html_writer::link($url, $icon.get_accesshide($strsubtopictoright), array('class' => 'subtopic-increase-sections'));


                            if ($displaysection != $section) {
                                $move_list_html .= html_writer::tag('li', $subtopic_move . html_writer::link($url, $sectionname));
                               }
                            else {
                                $move_list_html .= html_writer::tag('li', $subtopic_move . $sectionname);
                            }
                            */

                            //Define class from sublevels in order to move a margen in the left. Not apply if it is the first element (condition !empty($move_list_html)) because the first element can't be a sublevel
                            $li_class = '';
                            if (is_array($format_options) && isset($format_options['level']) && $format_options['level'] > 0 && !empty($move_list_html)) {
                                $li_class = 'sublevel';
                            }

                            if ($displaysection != $section) {
                                $move_list_html .= html_writer::tag('li', html_writer::link($url, $sectionname), array('class' => $li_class));
                               }
                            else {
                                $move_list_html .= html_writer::tag('li', $sectionname, array('class' => $li_class));
                            }
                        }
                    }
                    //End move section list***************************************************************************
                }
            }

            $section++;
        }

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);
        $sectiontitle = '';

        if (!$course->hidetabsbar && count($tabs[0]) > 0) {

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                // Increase number of sections.
                $straddsection = get_string('increasesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                        'increase' => true,
                        'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
                $tabs[] = new tabobject("tab_topic_add", $url, $icon, s($straddsection));

                if ($course->numsections > 0) {
                    // Reduce number of sections.
                    $strremovesection = get_string('reducesections', 'moodle');
                    $url = new moodle_url('/course/changenumsections.php',
                        array('courseid' => $course->id,
                            'increase' => false,
                            'sesskey' => sesskey()));
                    $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                    $tabs[] = new tabobject("tab_topic_remove", $url, $icon, s($strremovesection));
                }
            }

            $sectiontitle .= $OUTPUT->tabtree($tabs, "tab_topic_" . $displaysection, $inactive_tabs);//print_tabs($tabs, "tab_topic_" . $displaysection, $inactive_tabs, $active_tabs, true);
        }

        echo $sectiontitle;

        if (!$sections[$displaysection]->uservisible && !$canviewhidden) {
            if (!$course->hiddensections) {
                //Not used more, is controled in /course/view.php
            }
            // Can't view this section.
        }
        else {

            if ($course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE || $displaysection !== 0) {
                // Now the list of sections..
                echo $this->start_section_list();

                // The requested section page.
                $thissection = $sections[$displaysection];
                echo $this->section_header($thissection, $course, true);
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $completioninfo->display_help_icon();

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                }
                else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // close single-section div.
        echo html_writer::end_tag('div');

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {

            echo '<br class="utilities-separator" />';
            print_collapsible_region_start('move-list-box clearfix collapsible mform', 'course_format_onetopic_config_movesection', get_string('utilities', 'format_onetopic'), '', true);


            //Move controls
            if ($can_move && !empty($move_list_html)) {
                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('movesectionto', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo html_writer::tag('ul', $move_list_html, array('class' => 'move-list'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('movesectionto_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            $baseurl = course_get_url($course, $displaysection);
            $baseurl->param('sesskey', sesskey());

            $url = clone($baseurl);

            global $USER, $OUTPUT;
            if (isset($USER->onetopic_da[$course->id]) && $USER->onetopic_da[$course->id]) {
                $url->param('onetopic_da', 0);
                $text_button_disableajax = get_string('enable', 'format_onetopic');
            }
            else {
                $url->param('onetopic_da', 1);
                $text_button_disableajax = get_string('disable', 'format_onetopic');
            }

            echo html_writer::start_div("form-item clearfix");
                echo html_writer::start_div("form-label");
                    echo html_writer::tag('label', get_string('disableajax', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-setting");
                    echo html_writer::link($url, $text_button_disableajax);
                echo html_writer::end_div();
                echo html_writer::start_div("form-description");
                    echo html_writer::tag('p', get_string('disableajax_help', 'format_onetopic'));
                echo html_writer::end_div();
            echo html_writer::end_div();

            //Duplicate current section option
            if (has_capability('moodle/course:manageactivities', $context)) {
                $url_duplicate = new moodle_url('/course/format/onetopic/duplicate.php', array('courseid' => $course->id, 'section' => $displaysection, 'sesskey' => sesskey()));

                $link = new action_link($url_duplicate, get_string('duplicate', 'format_onetopic'));
                $link->add_action(new confirm_action(get_string('duplicate_confirm', 'format_onetopic'), null, get_string('duplicate', 'format_onetopic')));

                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('duplicatesection', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo $this->render($link);
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('duplicatesection_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            print_collapsible_region_end();
        }
    }
}
