<?php

require_once($CFG->dirroot . '/theme/bootstrap/renderers.php');

/**
 * Awesome core renderers.
 *
 * @package    theme_uemanet
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_awesome_core_renderer extends theme_bootstrap_core_renderer {

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';

        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }

        // Verifica se esta dentro de um curso ou nao
        // Caso esteja na pagina do curso vai imprimir as tabs com span2, caso contrario, vai imprimir normal
        $subtree = current($tabtree->subtree);
        if(isset($subtree) && strstr($subtree->id, 'tab_topic')) {
            return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs tabs-left col-md-2')) . $secondrow;
        }

        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs')) . $secondrow;
    }

}
