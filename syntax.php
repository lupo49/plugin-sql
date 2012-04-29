<?php
/**
 * Plugin SQL:  executes SQL queries
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Slim Amamou <slim.amamou@gmail.com>
 * @author     Tom Cafferty <tcafferty@glocalfocal.com>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/parserutils.php');
require_once('DB.php');
 
function property($prop, $xml)
{
	$pattern = $prop ."='([^']*)'";
	if (ereg($pattern, $xml, $matches)) {
		return $matches[1];
	}
	$pattern = $prop .'="([^"]*)"';
	if (ereg($pattern, $xml, $matches)) {
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
	var $table_class = 'inline';

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
    function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER : 
			$urn = property('db',$match);
			$wikitext = property('wikitext', $match);
			$display = property('display', $match);
			$position = property('position', $match);
			$tableid = property('id', $match);					
			$class = property('class', $match);
			$title = property('title', $match);			
			$headers = property('headers', $match);
			$row = property('row', $match);
			$col = property('col', $match);
			$table = property('table', $match);
			$theData = property('theData', $match);	
			return array('urn' => $urn, 'wikitext' => $wikitext, 'display' => $display, 'position' => $position, 'id' => $tableid, 'class' => $class, 'title' => $title, 'row' => $row, 'col' => $col, 'table' => $table, 'theData' => $theData, 'headers'=> $headers);
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
			$this->table_class = 'inline';
			$this->my_headers = FALSE;
			return array('wikitext' => 'enable', 'display' => 'block', 'position' => 'horizontal', 'class' => 'inline', 'id' => ' ', 'headers'=>'no');
            break;
        }
        return array();
    }
 
    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
		$renderer->info['cache'] = false;
		
        if($mode == 'xhtml'){
		
            if ($data['id'] != FALSE) 
				$this->tableId = $data['id'];			
           if ($data['class'] != FALSE) 
				$this->table_class = $data['class'];			
            if ($data['title'] != FALSE) 
				$this->title = $data['title'];								
            if ($data['row'] != FALSE) 
				$this->rowHeader = $data['row'];
            if ($data['col'] != FALSE) 
				$this->colHeader = $data['col'];				
            if ($data['table'] != FALSE) 
				$this->table_name = $data['table'];		
            if ($data['theData'] != FALSE) 
				$this->theData = $data['theData'];					
			if ($data['headers'] == 'no') {
				$this->my_headers = FALSE;
			} else if ($data['headers'] == 'yes') {
				$this->my_headers = TRUE;
			}	
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
    						if ($this->my_headers == TRUE) {
        						$result = getData($this->rowHeader, $this->colHeader, $this->theData, $this->table_name, $result);
        						$temp = array_shift($result);
        					} else {
            					$temp = array_keys($result[0]);
            				}
    						if ($this->tableId != ' ') {
        						$id_string = 'id="'.$this->tableId.'" ';
    						} else {
         						$id_string = '';
         					}
							if (! $this->vertical_position) {
								if ($this->display_inline) {
									$renderer->doc .= '<table '.$id_string.'class="'.$this->table_class.'" style="display:inline">';
								} else {
									$renderer->doc .= '<table '.$id_string.'class="'.$this->table_class.'">';
								}
								if ($this->title != '')
								    $renderer->doc .= '<caption class="sqlplugin__title">'.$this->title.'</caption><tbody>';
								$renderer->doc .= '<tr>';
								foreach ($temp as $header) {
									$renderer->doc .= '<th class="row0">';
									if ($this->wikitext_enabled) {
										$renderer->nest(p_get_instructions($header));
									} else {
										$renderer->cdata($header);
									}
									$renderer->doc .= '</th>';
								}
								$renderer->doc .= "</tr>\n";
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
									$renderer->doc .= "</tr>\n";
								}
								$renderer->doc .= '</tbody></table>';
							} else {
								foreach ($result as $row) {
									$renderer->doc .= '<table '.$id_string.'class="'.$this->table_class.'">';
								    if ($this->title != '')
								        $renderer->doc .= '<caption class="sqlplugin__title">'.$this->title.'</caption><tbody>';
									foreach ($row as $name => $cell) {
										$renderer->doc .= '<tr>';
										$renderer->doc .= "<th class='row0'>$name</th>";
										$renderer->doc .= '<td>';
										if ($this->wikitext_enabled) {
											$renderer->nest(p_get_instructions($cell));
										} else {
											$renderer->cdata($cell);
										}
										$renderer->doc .= '</td>';
										$renderer->doc .= "</tr>\n";
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
    
    function getEnumSetValues($table, $field){
        //
        //Returns an array of enumeration values from the field of the table
        //
        // Get the enumeration values from the database
        $query = "SHOW COLUMNS FROM `$table` LIKE '$field'";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        //
        // Parse out the extra text from the result 
        if(stripos(".".$row[1],"enum(") > 0) 
            $row[1]=str_replace("enum('","",$row[1]);
        else 
            $row[1]=str_replace("set('","",$row[1]);
        $row[1]=str_replace("','","\n",$row[1]);
        $row[1]=str_replace("')","",$row[1]);
        $ar = explode("\n",$row[1]);
        for ($i=0;$i<count($ar);$i++) 
            $arOut[str_replace("''","'",$ar[$i])]=str_replace("''","'",$ar[$i]);
        return $arOut;
    } 
   
    function getData ($rowElement, $colElement, $dataElement, $table, $oldArray) {
        //
        // Returns a new array constructed from the old array and the enumeration headings
        //
        // Get the enumeration values for the row and column headings
        $rowVal = getEnumSetValues($table, $rowElement);
        $colVal = getEnumSetValues($table, $colElement);
        //
        // Start a new array and fill in the column headings
        $newArray = array();
        $newArray[0][1] = $rowElement;     // start with row heading
        foreach ($colVal as $colHeader) {
            $newArray[0][] = $colHeader;
        }
        $i=0;
        $numRowsOld = count($oldArray);
        //
        // Fill in the row headings
        foreach ($rowVal as $rowHeader) {
            $i++;
            $newArray[$i]['header'] = $rowHeader;
            $valIndex = 0;
            foreach ($colVal as $colHeader) {
                $valIndex++;
                $newArray[$i][$valIndex] = '';
                //
                // Fill in the data when the cells match the row and column headings 
                foreach ($oldArray as $key => $row) {
                    if (($row[$rowElement] == $rowHeader) AND ($row[$colElement] == $colHeader)) 
                        $newArray[$i][$valIndex] = $oldArray[$key][$dataElement];
                }
            }
        }
        return $newArray;
    }
}