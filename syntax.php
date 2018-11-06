<?php
/**
 * Plugin SQL:  executes SQL queries
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Slim Amamou <slim.amamou@gmail.com>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/parserutils.php');
require_once('DB.php');
 
function property($prop, $xml)
{
	$pattern = '/'.$prop ."='([^']*)')/";
	if (preg_match($pattern, $xml, $matches)) {
		return $matches[1];
	}
	$pattern = '/'.$prop .'="([^"]*)"/';
	if (preg_match($pattern, $xml, $matches)) {
		return $matches[1];
	}
	return FALSE;
}
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_sql extends DokuWiki_Syntax_Plugin {
    var $databases = array();
	var $wikitext_enabled = TRUE;
	var $display_inline = FALSE;
	var $vertical_position = FALSE;
  
    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }
	 
    /**
     * Where to sort in?
     */ 
    function getSort(){
        return 555;
    }
 
 
    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
      $this->Lexer->addEntryPattern('<sql [^>]*>',$mode,'plugin_sql');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('</sql>','plugin_sql');
    }
 
 
    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
          case DOKU_LEXER_ENTER : 
			$urn = property('db',$match);
			$wikitext = property('wikitext', $match);
			$display = property('display', $match);
			$position = property('position', $match);
			return array('urn' => $urn, 'wikitext' => $wikitext, 'display' => $display, 'position' => $position);
            break;
          case DOKU_LEXER_UNMATCHED :
			$queries = explode(';', $match);
			if (trim(end($queries)) == "") {
				array_pop($queries);
			}
			return array('sql' => $queries);
            break;
          case DOKU_LEXER_EXIT :
			$this->wikitext_enabled = TRUE;
			$this->display_inline = FALSE;
			$this->vertical_position = FALSE;
			return array('wikitext' => 'enable', 'display' => 'block', 'position' => 'horizontal');
            break;
        }
        return array();
    }
 
    /**
     * Create output
     */
    function render($mode, Doku_Renderer $renderer, $data) {
		$renderer->info['cache'] = false;
		
        if($mode == 'xhtml'){
			
			if ($data['wikitext'] == 'disable') {
				$this->wikitext_enabled = FALSE;
			} else if ($data['wikitext'] == 'enable') {
				$this->wikitext_enabled = TRUE;
			}
			if ($data['display'] == 'inline') {
				$this->display_inline = TRUE;
			} else if ($data['display'] == 'block') {
				$this->display_inline = FALSE;
			}
			if ($data['position'] == 'vertical') {
				$this->vertical_position = TRUE;
			} else if ($data['position'] == 'horizontal') {
				$this->vertical_position = FALSE;
			}
			if ($data['urn'] != "") {
				$db =& DB::connect($data['urn']);
				if (DB::isError($db)) {
					$error = $db->getMessage();
					$renderer->doc .= '<div class="error">'. $error .'</div>';
					return TRUE;
				}
				else {
					array_push($this->databases, $db);
				}
			}
			elseif (!empty($data['sql'])) {
			    $db =& array_pop($this->databases);
				if (!empty($db)) {
					foreach ($data['sql'] as $query) {
						$db->setFetchMode(DB_FETCHMODE_ASSOC);
						$result =& $db->getAll($query);
						if (DB::isError($result)) {
							$error = $result->getMessage();
							$renderer->doc .= '<div class="error">'. $error .'</div>';
							return TRUE;
						}
						elseif ($result == DB_OK or empty($result)) {
						}
						else {

							if (! $this->vertical_position) {
								if ($this->display_inline) {
									$renderer->doc .= '<table class="inline" style="display:inline"><tbody>';
								} else {
									$renderer->doc .= '<table class="inline"><tbody>';
								}
								$renderer->doc .= '<tr>';
								foreach (array_keys($result[0]) as $header) {
									$renderer->doc .= '<th>';
									if ($this->wikitext_enabled) {
										$renderer->nest(p_get_instructions($header));
									} else {
										$renderer->cdata($header);
									}
									$renderer->doc .= '</th>';
								}
								$renderer->doc .= '</tr>';
								foreach ($result as $row) {
									$renderer->doc .= '<tr>';
									foreach ($row as $cell) {
										$renderer->doc .= '<td>';
										if ($this->wikitext_enabled) {
											$renderer->nest(p_get_instructions($cell));
										} else {
											$renderer->cdata($cell);
										}
										$renderer->doc .= '</td>';
									}
									$renderer->doc .= '</tr>';
								}
								$renderer->doc .= '</tbody></table>';
							} else {
								foreach ($result as $row) {
									$renderer->doc .= '<table class="inline"><tbody>';
									foreach ($row as $name => $cell) {
										$renderer->doc .= '<tr>';
										$renderer->doc .= "<th>$name</th>";
										$renderer->doc .= '<td>';
										if ($this->wikitext_enabled) {
											$renderer->nest(p_get_instructions($cell));
										} else {
											$renderer->cdata($cell);
										}
										$renderer->doc .= '</td>';
										$renderer->doc .= '</tr>';
									}
									$renderer->doc .= '</tbody></table>';
								}
							}
						}
					}
				}
			}
            return true;
        }
        return false;
    }
}
