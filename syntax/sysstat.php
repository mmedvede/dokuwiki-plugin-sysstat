<?php
/**
 * DokuWiki Plugin sysstat (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Mikhail Medvedev <mmedvede@cs.uml.edu>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_sysstat_sysstat extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 100;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~SYSSTAT~~',$mode,'plugin_sysstat_sysstat');
        //        $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_sysstat_sysstat');
    }

    //    public function postConnect() {
    //        $this->Lexer->addExitPattern('</FIXME>','plugin_sysstat_sysstat');
    //    }

    /**
     * Handle matches of the sysstat syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, &$handler){
        $data = array();

        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, &$renderer, $data) {
        if($mode != 'xhtml'){
            return false;
        }

        global $conf;

        $datadir = $conf['datadir'];
        $mediadir =$conf['mediadir'];
        $freeb = $this->_freespace($datadir);
        $freeh = $this->_freespaceh($datadir);
        $pagesb = $this->_dirsize($datadir);
        $pagesh = $this->_dirsizeh($datadir);
        $mediab = $this->_dirsize($mediadir);
        $mediah = $this->_dirsizeh($mediadir);

        $totalb = $freeb + $pagesb + $mediab;

        $doc = '<div class="sysstat">';

        $doc .= '<div class="sysstat_part syspages" style="min-width:' . ($pagesb / $totalb *100 ) . '%">';
        $doc .= $pagesh . ' text';
        $doc .= '</div>';

        $doc .= '<div class="sysstat_part sysmedia" style="min-width:' . ($mediab / $totalb *100 ) . '%">';
        $doc .= $mediah . ' media';
        $doc .= '</div>';

        $doc .= $freeh . ' free';
        $doc .= '</div>';

        $renderer->doc .=$doc;

        return true;
    }


    /*
     * Directory size in bytes
     */
    function _dirsize($dir, $opt = '-sb'){
        return shell_exec('du '. $opt .' '. $dir .' | cut -f1');
    }
    /*
     * Human readable
     */
    function _dirsizeh($dir){
        return $this->_dirsize($dir, '-sh');
    }

    /*
     * Partition free space in bytes
     */
    function _freespace($partition, $opt = '-B1'){
        $free = shell_exec('df '.$opt.' '. $partition .' | tail -n1');
        $free = preg_replace('/\s+/', ' ', $free);
        $free = explode(' ',$free);
        return $free[3];
    }
    /*
     * Human readable
     */
    function _freespaceh($partition){
        return $this->_freespace($partition, '-h');
    }
}

// vim:ts=4:sw=4:et:
