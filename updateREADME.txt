License  
 * @license   GPL 2 (http://www.gnu.org/licenses/gpl.html)
License sorttable.js
 * @license   X11 (http://www.kryogenix.org/code/browser/sorttable/)

Changes to Requirements
  Added Keywords Plugin - only required if using table sorting
  format: {{keywords>sqlSort}}
  this will add javascript to the page for column sorting

Changes to Syntax (Added the following Options).  All added options are optional
  id='string'                                 used to specify a table id. Added to allow Tableplot plugin to graph the table
  title='string'                              you can add a table caption
  class= 'inline', 'sortable', or 'hidden'    default is inline. sortable allows table sorting, hidden will hide the table (this is useful to remove redundancy if using Tableplot plugin to graph the table)
  headers= 'no' or 'yes'                      deafult is 'no'. Set to 'yes' if using database enumeration fields for table headings
  row='string'                                when headers is yes this specifies database field to use for the row heading 
  col='string'                                when headers is yes this specifies database field to use for the column heading
  table='string'                              when headers is yes this specifies database table that has the row and column fields to use
  theData='string'                            when headers is yes this specifies database field to use for the table data

This update should be backwards compatible to all existing wiki uses of the sql plugin. 

The update includes a css file and an images folder that provides table formatting when table sorting is selected.

The update includes a javascript file for table sorting. The file is in both originalsource and a minified version. The minified version is called in the action plugin when the keywords plugin is used to denote sqlSort. 

The update includes an example forlder containing an SQL file to create a database table of US waste data and a wiki.txt file that can be added to your wiki page to try the plugin update.  The example includes use of the tableplot plugin to graph the data. You and remove that part of the example if only interested in the table. However note that at the bottom the keywords plugin is used to identify the use of tableplot and sql. If not using tableplot or table sorting then the whole keywords plugin can also be removed.


