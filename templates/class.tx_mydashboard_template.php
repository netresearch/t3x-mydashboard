<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Tim Lochmueller <webmaster@fruit-lab.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class tx_mydashboard_template {

	/* Private Vars for handling the output */
	private $title = false;
	private $icon = false;
	private $dashboardKey = '';
	private $widgetKey = '';
	private $widgetConfig = array();
	private $widgetDefaultConfig = array();
	private $jsFiles = array();


	/* DEPRECATED start */
	public function setDeaultConfig($config){ $this->setDefaultConfig($config); }
	public function createNewRecord($table, $pageID){ return false; }
	/* DEPRECATED end */
	
	
	/*
	 * Init the Base functions for a Widget
	 */
	public function init(){
		// No commands at the moment
	} # function - init	
	
	
	/*
	 * Register a JS File to the system
	 *
	 * @parm String $relFileName The Script File Name
	 */
	public function registerJSFile($relFileName){
		if(!is_array($this->jsFiles)) $this->jsFiles = array();
		$this->jsFiles[] = $relFileName;
	} # function - registerJSFile
	
	
	/*
	 * Render the Footer Menu
	 */
	public function renderFooterMenu($data){
		$data = array(
			'title' => 'Test',
			'http_url' => '',
			'js_function' => '',
			'icon_path' => ''
		);
		return 'Impliment soon';
	} # function - renderFooterMenu
	
	
	/*
	 * Get the JS Files
	 */
	public function getJSFiles(){
		return $this->jsFiles;
	} # function - getJSFiles
	
	
	/*
	 * Set the Dashboard Key (is used by the Widget Mgm Class)
	 *
	 * @parm String $key The Dashboard Key
	 */
	public function setDashboardKey($key){
		$this->dashboardKey = $key;
	} # function - setDashboardKey
	
	
	/*
	 * Get the Dashboard Key (can used by widgets)
	 *
	 * @return string The Dashboard Key
	 */
	public function getDashboardKey(){
		return $this->dashboardKey;
	} # function - setDashboardKey
	
	
	/*
	 * Set the Widget Key (is used by the Widget Mgm Class)
	 *
	 * @parm String $key The Widget Key
	 */
	public function setWidgetKey($key){
		$this->widgetKey = $key;
	} # function - setWidgetKey
	
	
	/*
	 * Get The Widget Key
	 *
	 * @return String The Widget Key of this Widget
	 */
	public function getWidgetKey(){
		return $this->widgetKey;
	} # function - getWidgetKey
	
	
	/*
	 * Add a Language File to the Label parsing
	 *
	 * @parm String $file A valid Filename
	 * @return boolean (always true)
	 */
	public function addLanguageFile($file){
		global $LANG;
		$LANG->includeLLFile($file);
		return true;
	} # function - addLanguageFile
	
	
	/*
	 * Get a Label bei the given key in the defined language
	 *
	 * @parm String $key the Label Key
	 * Lreturn String The Label
	 */
	public function getInternalLabel($key){
		global $LANG;
		
		$label = trim($LANG->getLL($key));
		if($label == '')
			$label = $key;
		
		return $label;
	} # function - getInternalLabel
	
	
	/*
	 * Get the Widget Configuration for the AJAX request
	 *
	 * @return String
	 */
	public function getConfig(){
		
		// Build the Form
		$c .= '<div class="conf-form"><form action="#" id="'.$this->dashboardKey.'_confform" method="post" onsubmit="sendConfForm(\''.$this->dashboardKey.'\'); return false;"><table>';
		
		foreach($this->widgetDefaultConfig as $key => $conf){
			$c .= '<tr><td><label>'.$this->getInternalLabel($key).'</label></td><td>';
			
			switch($conf['type']){
				case 'int':
				case 'string':
					$c .= '<input type="input" name="'.$key.'" value="'.$this->getConfigVar($key).'" />';
					break;
				case 'select':
					$c .= '<select name="'.$key.'">';
					foreach ($conf['options'] as $optionKey => $value) {
						$c .= '<option '
							. ($this->getConfigVar($key) === $optionKey ? 'selected="selected"':'')
							. ' value="'.htmlspecialchars($optionKey).'">' . htmlspecialchars($value) . '</option>';
					}
					$c .= '</select>';
					break;
				case 'boolean':
					$c .= '<input type="radio" name="'.$key.'" value="true" '.($this->getConfigVar($key)?'checked="checked" ':'').' /> On
					<input type="radio" name="'.$key.'" value="false" '.(!$this->getConfigVar($key)?'checked="checked" ':'').' /> Off
					
					';
					break;
				case 'password':
					$c .= '<input type="password" name="'.$key.'" value="'.$this->getConfigVar($key).'" />';
					break;
				default:
					$c .= 'error';
					break;			
			} # switch
			
			$c .= '</td></tr>';

		} # foreach
		
		$c .= '<tr><td>&nbsp;</td><td>
		<input type="button" value="update" onclick="sendConfForm(\''.$this->dashboardKey.'\'); return false;" /></td></tr>
		</table></form></div>';
	
		return '<h3>Configuration</h3>'.$c;
	} # function - getConfig	
	
	
	/*
	 * Set the User defined configuration vars
	 *
	 * @parm Array $conf The configs
	 */
	public function setConfigVars($conf){		
		$this->widgetConfig = $conf;
	} # function - setConfigVars
	

	/*
	 * Set the default configuration
	 *
	 * @parm Array $config The default configuration
	 */
	public function setDefaultConfig($config){
		$this->widgetDefaultConfig = $config;
	} # function - setDefaultConfig
	
	
	/*
	 * Check if the Widget have a Configuration
	 *
	 * @return boolean
	 */
	public function isConfig(){
		return !($this->widgetDefaultConfig == array());
	} # function - isConfig
	
	
	/*
	 * Get the Value of the Config Var
	 *
	 * @parm String $name The name of the config var
	 * @return String The value
	 */
	public function getConfigVar($name){
		$value = false;
		if(isset($this->widgetConfig[$name]))
			$value = $this->widgetConfig[$name];
		else if(isset($this->widgetDefaultConfig[$name]['default']))
			$value = $this->widgetDefaultConfig[$name]['default'];
		
		if($value === 'false') return false;
		if($value === 'true') return true;
		return $value;
	} # function - getConfigVar
	
	
	/*
	 * Show a List of Database Records
	 *
	 * @parm String $headline The Headline of the list
	 * @parm Array $rows The Datarecords from e.g. a Database
	 * @parm String $title One or more titles Comma separated
	 * @parm Array $options Same Options for more functions
	 */
	public function showDatabaseListByArray($headline, $rows, $title, $options = array()){
		$content = '<h4 style="position: relative">'.$headline.$this->createNewInlineRecord($options).'</h4>';
		
		$list = '<ul class="db-listing">';
		$items = false;
		$titles = explode(',', $title);
		foreach($rows as $row){
			
			$list .= '<li>'.$row[$titles[0]];
			if(sizeof($titles) > 1){
				$list .= '&nbsp;<i>(';
				for($i = 1; $i < sizeof($titles); $i++)
					$list .= (($i > 1)?' - ':'').$this->renderField($titles[$i], $row[$titles[$i]]);
				$list .= ')</i>';
			} # if
			
			$list .= '</li>';

			$items = true;
		} # foreach
		
		$list .= '</ul>';
		
		if($items)
			$content .= $list;
			
		// Return The Content
		return $content;	
	} # function - showDatabaseListByArray
	
	
	/*
	 * Create the "New Inline Records Icon"
	 *
	 * @parm Array $data An Option Array
	 * @return String The Content
	 */
	private function createNewInlineRecord($data){
		if(!is_array($data) || !isset($data['table']) || !isset($data['pageID'])) return '';
		
		global $BACK_PATH;
		$new_url = $BACK_PATH.'alt_doc.php?returnUrl='.$BACK_PATH.TYPO3_MOD_PATH.'&amp;edit['.$data['table'].']['.$data['pageID'].']=new';
		return '<a href="'.$new_url.'" style=" position: absolute; top: 0px; right: 0px;"><img src="'.$BACK_PATH.'sysext/t3skin/icons/gfx/new_page.gif" alt="new Record" title="new Record" /></a>';
	} # function - createNewInlineRecord

	
	/*
	 * Show a List of Database Records
	 *
	 * @parm String $headline The Headline of the list
	 * @parm Resource $res The SQL Resource
	 * @parm String $title One or more titles Comma separated
	 * @parm Array $options Same Options for more functions
	 */
	public function showDatabaseList($headline, $res, $title, $options = array()){
		$rows = array();
		
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
			$rows[] = $row;
		
		return $this->showDatabaseListByArray($headline, $rows, $title, $options);
	} # function - showDatabaseList

	
	/*
	 * Render a field in the Output of the default list function
	 *
	 * @parm String $name the field name
	 * @parm String $value the field content
	 * @return String the manipulation of the string
	 */
	private function renderField($name, $value){
		switch(trim($name)){
			case 'tstamp':
			case 'crdate':
			case 'lastlogin':
				return date('d.m.y - H:i', $value);
			default:
				return htmlspecialchars($value);
		} # switch
	} # function - renderField
	
	
	/*
	 * Build up a Widget Menu in selectbox style
	 * Use this function for different views
	 * example in the userstats Widget
	 *
	 * @parm Array $items The Items of the Menü
	 * @return String the renderd menu
	 */
	public function buildSelectMenu($items){
	
		// Load the last Status if the User reload the Widget manually
		global $BE_USER;
		$ses_key = 'last_status_'.$this->dashboardKey;
		if(isset($_REQUEST['value']))
			$BE_USER->setAndSaveSessionData($ses_key, $_REQUEST['value']);
		elseif(trim($BE_USER->getSessionData($ses_key)) != '')	
			$_REQUEST['value'] = $BE_USER->getSessionData($ses_key);
			
		// Build up the Menu
		$c = '<form action="#" method="post">Zeige: <select onchange="ajaxWidgetParmsValue(\''.$this->dashboardKey.'\', \'refresh\', this.value)">';
		foreach($items as $key => $value)
			$c .= '<option value="'.$key.'"'.(($_REQUEST['value'] == $key)?' selected="selected"':'').'>'.$value.'</option>';
		return $c.'</select></form>';
	} # function - buildSelectMenu 
	
	
	/*
	 * Set the title of the Widget
	 *
	 * @parm String $title Widget title
	 */
	public function setTitle($title){
		$this->title = $title;
	} # function - setTitle
	
	
	/*
	 * Set the Icon of the Widget e.g. $this->setIcon(t3lib_extMgm::extRelPath('tt_news').'/ext_icon.gif');
	 *
	 * @parm String $iconPath the relative Path to the IconFile
	 */
	public function setIcon($iconPath){
		global $BACK_PATH;
		$this->icon = $BACK_PATH.$iconPath;
	} # function - setIcon
	
	
	/*
	 * get the Widget title
	 */
	public function getTitle(){
		if(!$this->title) $this->setTitle('[ no title ]');
		return $this->title;
	} # function - getTitle
	
	
	/*
	 * Get the Icon path
	 */
	public function getIcon(){
		if(!$this->icon) $this->setIcon(t3lib_extMgm::extRelPath('mydashboard').'templates/tx_mydashboard_widget_default.png');
		return $this->icon;
	} # function - getIcon


} # class - tx_mydashboard_template

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/templates/class.tx_mydashboard_template.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/templates/class.tx_mydashboard_template.php']);
} # if
?>