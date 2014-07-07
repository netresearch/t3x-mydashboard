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

unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:mydashboard/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);


/**
 * Module 'Dashboard' for the 'mydashboard' extension.
 *
 * @author	Tim Lochmüller <webmaster@fruit-lab.de>
 * @package	TYPO3
 * @subpackage	tx_mydashboard
 */
class tx_mydashboard_module1 extends t3lib_SCbase {



	/*
	Done:
	
	
	
	Update Documentation;
	
	ToDo:
	setNewOrder im mgm überdenken um zu prüfen...
	Widgets bauen: serverstats, TYPO3 log, HTML Mailer Stats
	Widgets weitergeben: tt_news an Rupi (ext_localconf.php und Files dannach aufräumen)
	*/




	/* Page Info */
	var $pageinfo;
	
	private $jsLoaded = array();
	
	/*
	 * Initializes the Module
	 *
	 * @return	void
	 */
	function init()	{
		
		parent::init();
		
		// header("Content-Type: text/html; charset=utf-8");
		
		require_once(dirname(dirname(__FILE__)).'/class.tx_mydashboard_widgetmgm.php');
		$this->mgm = t3lib_div::makeInstance('tx_mydashboard_widgetmgm');
	} # function - init
	
	
	/*
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig() {
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('title'),
				'2' => $LANG->getLL('config')
			)
		);
		parent::menuConfig();
	} # function - menuConfig
	
	
	/*
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 */
	function main()	{
	
		/*
		Access Check via $BE_USER->modAccess($MCONF,1); in the top of this file.
		*/
		
	
		#global $BE_USER;
		#
		#// Access check!
		#$this->id = 1;
		#$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		#$access = is_array($this->pageinfo) ? 1 : 0;
		#
		#if (($this->id && $access) || $BE_USER->user['admin'])	{
			if(isset($_REQUEST['ajax']))
				$this->renderAJAX();
			else
				$this->renderContent();
		#} else {
		#	$this->content .= 'No Access to this Module';
		#} # if
		
	} # function - main
	
	
	/*
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		echo $this->content;
	} # function - printContent
	
	
	/*
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent() {
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
				$content .= $this->doc->section('',$this->showDashboard(),0,1);
				break;
			case 2:
				$content .= $this->doc->section('',$this->showConfig(),0,1);
				break;
		} # switch
		
		return $content;
	} # function - moduleContent
	
	
	/*
	 * Check if the current user have access to the configuration of the dashboard
	 *
	 * @return boolean
	 */
	function currentUserHaveConfigAccess(){
		global $BE_USER;
		if(!is_array($BE_USER->user)) return false;
		if($BE_USER->user['admin']) return true;
		if($BE_USER->user['tx_mydashboard_selfadmin']) return true;
		return false;
	} # function - currentUserHaveConfigAccess
	
	
	/*
	 * Render the AJAX Content for widgets
	 */
	function renderAJAX(){
		global $BE_USER;
		$user = $BE_USER->user['uid'];
		$this->mgm->loadUserConf($user);
		
		switch($_REQUEST['action']){
			case 'refresh':
				$widget = $this->mgm->getWidget($_REQUEST['key']);
				$this->content = $widget->getContent();
				break;
			
			case 'refresh_config':
				if(!$this->currentUserHaveConfigAccess()){
					$this->content = 'No Access!';
					return false;
				} # if
				parse_str($_REQUEST['value'], $output);
				if($this->mgm->setWidgetConf($_REQUEST['key'], $output))
					$this->mgm->safeUserConf($user);
				$widget = $this->mgm->getWidget($_REQUEST['key']);
				$this->content = $widget->getContent();
				break;
			
			case 'config':
				if(!$this->currentUserHaveConfigAccess()){
					$this->content = 'No Access!';
					return false;
				} # if
				$widget = $this->mgm->getWidget($_REQUEST['key']);
				$this->content = $widget->getConfig();
				break;
			
			case 'delete':
				if(!$this->currentUserHaveConfigAccess()){
					$this->content = 'No Access!';
					return false;
				} # if
				if($this->mgm->removeWidget($_REQUEST['key']))
					$this->mgm->safeUserConf($user);
				$this->content = '';
				break;
				
			case 'reorder':
				parse_str($_REQUEST['data'], $output);
				if($this->mgm->setNewOrder($output))
					$this->mgm->safeUserConf($user);
				break;
			
			default: 
				$this->content = 'No Action';
				break;
		} # switch
	} # function - renderAJAX
	
	
	/*
	 * Show the Dashboard
	 */
	function showDashboard(){
		global $BE_USER;
	
		// Config an preload
		$this->mgm->loadUserConf($BE_USER->user['uid']);
		$userConf = $this->mgm->getUserConf();
		$content = array('','','','');
		$rows = intval($userConf['config']['rows']);
		
		// Generate the Widgets
		for($i = 0; $i < $rows; $i++){
			
			if(!is_array($userConf['position'][$i])) $userConf['position'][$i] = array();
			
			foreach($userConf['position'][$i] as $widgetKey){
			
				// Create the Widget Object
				$widget = $this->mgm->getWidget($widgetKey);
				
				// Continue if it is no Object
				if(!is_object($widget)) continue;
				
				
				$widget_c = '<div class="widget" id="widget_'.$widgetKey.'"><h2 onmouseover="showOptions(\'widget_'.$widgetKey.'\')" onmouseout="hideOptions(\'widget_'.$widgetKey.'\');"><span class="icon">'.$this->mgm->renderIcon($widget).'</span><span class="text">'.$widget->getTitle().'</span>';
				if($this->currentUserHaveConfigAccess()){
					$widget_c .= '<a href="#" class="delete" id="widget_'.$widgetKey.'_delete" style="display: none" onclick="deleteWidget(\''.$widgetKey.'\')">Delete</a>';
					if($widget->isConfig())
						$widget_c .= '<a href="#" class="config" id="widget_'.$widgetKey.'_config" style="display: none" onclick="configWidget(\''.$widgetKey.'\')">Config</a>';
				} # if
				$widget_c .= '<a href="#" class="refresh" id="widget_'.$widgetKey.'_refresh" style="display: none" onclick="refreshWidget(\''.$widgetKey.'\')">Refresh</a>';
	
				$widget_c .= '</h2>
				<div class="content" id="widget_'.$widgetKey.'_content">'.$widget->getContent().'</div>
				</div>';
				
				foreach($widget->getJSFiles() as $file){
					if(!in_array($file, $this->jsLoaded)) {
						$this->jsLoaded[] = $file;
						$this->doc->JScode .= '<script type="text/javascript" src="'.$file.'"></script>';
					} # if
				} # foreach
				
				$content[$i] .= $widget_c;
				
			} # foreach
			
			$content[$i] = '<div id="container'.$i.'" class="container-rows-'.$rows.'">'.$content[$i].'</div>';
			
		} # for
		
		$content = '<div class="widgets">'.implode($content).'<div class="clearer"></div></div>';
		
		// Javascript
		$js = '';
		$container = array();
		$newOrder = array();
		for($i = 0; $i < intval($userConf['config']['rows']); $i++){
			if(!is_array($userConf['position'][$i])) continue;
			
			$js .= 'Sortable.create("container'.$i.'",{tag: \'div\',dropOnEmpty:true,containment:[###CONTAINER###],constraint:false, onUpdate:sendNewOrder});'."\n";
			$container[] = '"container'.$i.'"';
			$newOrder[] = 'parms = Sortable.serialize("container'.$i.'", {name: \''.$i.'\'})+"&"+parms;';
		}
		
		$js = "<script type=\"text/javascript\">\n".str_replace('###CONTAINER###', implode(',', $container), $js)."
		
		function sendNewOrder(){
			var parms = '';
			".implode("\n", $newOrder)."
			new Ajax.Request('index.php', {
				parameters: { ajax: 1, action: 'reorder', data: parms },
			});
		}
		</script>";
	
		return $content.$js;
	
	} # function - showDashboard
	
	
	/*
	 * Render the Main Content
	 */
	function renderContent(){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		require_once(t3lib_extMgm::extPath('mydashboard').'class.tx_mydashboard_completeDoc.php');
		
		// The different Content Types order by size
		$this->contentSize = array(
			'mediumDoc',
			'bigDoc',
			'tx_mydashboard_completeDoc',
		);
		
		$user = $BE_USER->user['uid'];
		$this->mgm->loadUserConf($user);
		$config = $this->mgm->getUserConf('config');
		
		// ############# Set the right Content type here
		
		$this->currentContentSize = (isset($config['layout']) && in_array($config['layout'], $this->contentSize))?$config['layout']:$this->contentSize[1];
		
		$this->doc = t3lib_div::makeInstance($this->currentContentSize);
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';
		$this->doc->form = '';
		
		// ############# Set the right Theme here
		$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('mydashboard').'mod1/css/theme-default.css';
		
		// required for the widgets
		$this->doc->loadJavascriptLib('contrib/prototype/prototype.js');
		$this->doc->loadJavascriptLib('contrib/scriptaculous/scriptaculous.js?load=builder,effects,dragdrop,controls,slider');
		$this->doc->loadJavascriptLib(t3lib_extMgm::extRelPath('mydashboard').'mod1/functions.js');
		
		
		// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
			script_ended = 0;
			function jumpToUrl(URL)	{
				document.location = URL;
			}
			</script>
		';
		$this->doc->postCode = '
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
		';
		
		$headerSection = 'Hallo <strong>';
		$headerSection .= (trim($BE_USER->user['realName']))?htmlspecialchars($BE_USER->user['realName']):'<i>No User Name</i>';
		$headerSection .= ' ('.$BE_USER->user['username'].')</strong> - <i>'.date('F j, Y, g:i a', time()).'</i>';
		$menu = ($this->currentUserHaveConfigAccess())?t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function']):'';
		
		$moduleContent = $this->moduleContent();
		
		
		$GLOBALS['LANG']->charSet = 'utf-8';		
		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content.=$this->doc->header($LANG->getLL('title'));
		$this->content.=$this->doc->spacer(5);
		$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));
		$this->content.=$this->doc->divider(5);
		
		// Render content:
		$this->content .= $moduleContent;
		
		// ShortCut
		if($BE_USER->mayMakeShortcut()) 
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));

		$this->content.=$this->doc->spacer(10);
		$this->content.=$this->doc->endPage();
	} # function - renderContent
	
	
	/* 
	 * Build up a select form item
	 */
	function buildSelect($name, $data, $current = false){
		global $LANG;
		
		$out = '<select name="'.$name.'">';
		foreach($data as $value)
			$out .= '<option value="'.$value.'"'.(($current && $current == $value)?' selected="selected"':'').'>'.$LANG->getLL($name.'_'.$value).'</option>';
		return $out.'</select>';
	} # function - buildSelect
	
	
	/*
	 * Render the Configuration module
	 */
	function showConfig(){
	
		// Init Config Page & load the User data
		global $LANG,$BE_USER;
		$user = $BE_USER->user['uid'];
		$this->mgm->loadUserConf($user);
		$config = $this->mgm->getUserConf('config');
		
		
		// Add Widget
		if(isset($_REQUEST['addWidget'])){
			if($this->mgm->addWidget($_REQUEST['addWidget'])) {
				$this->mgm->safeUserConf($user);
				$content .= '<span class="notice">Notice:</span> The Widget with the Key "'.$_REQUEST['addWidget'].'" is added to the Dashboard.<hr />';
			} # if
		} # if
		
		// Set the Dashboard as Home
		if(isset($_REQUEST['dashHome'])){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_users','uid='.intval($user),'','',1);
			if(!$GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				$content .= 'Error<hr />';
			} else {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$uc = unserialize($row['uc']);
				if(is_array($uc)){
					$uc['startModule'] = 'user_txmydashboardM1';
					$BE_USER->uc['startModule'] = 'user_txmydashboardM1';
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users', 'uid='.intval($user), array('uc' => serialize($uc)));
					$content .= '<span class="notice">Notice:</span> The Dashboard is set as startpage (current user)!<hr />';
				} # if
			} # if
		} # if
		
		// Set the Dashboard as Home
		if(isset($_REQUEST['configForm'])){
		
			// Set the new Value
			if(isset($_REQUEST['config_rows'])) $config['rows'] = intval($_REQUEST['config_rows']);
			if(isset($_REQUEST['config_layout']) && in_array($_REQUEST['config_layout'], $this->contentSize)) $config['layout'] = $_REQUEST['config_layout'];

			
			if($this->mgm->setUserConf('config', $config))
				$this->mgm->safeUserConf($user);
			$content .= '<span class="notice">Notice:</span> Config update!<hr />';
		} # if
		
		// Check if the Dashboard is the home page
		if(trim($BE_USER->uc['startModule']) != 'user_txmydashboardM1')
			$content .= '<span class="notice">Notice:</span> The Dashboard is not the startpage (current user)! Click <a style="text-decoration: underline;" href="index.php?&id=0&SET[function]=2&dashHome=1">here</a> to set the Dashboard as startpage.<hr />';
		
	
		
		
		$content .= '<h1>'.$LANG->getLL('config').'</h1>';
		
		$content .= '
		<form action="index.php" method="post">
		<input type="hidden" name="configForm" value="1" />
		<table>
			<tr>
				<td>Dashboard Layout:</td>
				<td>'.$this->buildSelect('config_layout', $this->contentSize, $config['layout']).'</td>
			</tr>
			<tr>
				<td>Widget Cols:</td>
				<td>'.$this->buildSelect('config_rows', array('2','3','4'), $config['rows']).'</td>
			</tr>
			<tr>
				<td>Dashboard Theme:</td>
				<td>'.$this->buildSelect('config_theme', array('default'), $config['theme']).'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" /></td>
			</tr>
		</table>
		</form>
		';
		
		
		$content .= '<h3>Add Widgets (always to the left row)</h3>';
		$widgets = $this->mgm->getAllWidgets();
		$content .= '<table>';
		foreach($widgets as $key => $widget)
			$content .= '<tr><td>'.$this->mgm->renderIcon($widget).'</td><td>'.$widget->getTitle().'</td><td><a style="text-decoration: underline;" href="index.php?&id=0&SET[function]=2&addWidget='.$widget->getWidgetKey().'">add Widget</a></td></tr>';
		$content .= '</table>';
		
		return $content;
	} # function - showConfig
	
	
} # class - tx_mydashboard_module1

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/mod1/index.php']);
} # if


# Get the Page
$SOBE = t3lib_div::makeInstance('tx_mydashboard_module1');
$SOBE->init();
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
$SOBE->main();
$SOBE->printContent();
?>