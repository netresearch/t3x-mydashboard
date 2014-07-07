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

class tx_mydashboard_widgetmgm {

	/* Private Attributes */
	private $widgets = array();
	private $widgetsKeys = array();
	private $baseWidgets = array();
	private $dashboardwidgets = array();
	private $userConf = array();
	private $user = false;
	
	
	/*
	 * Construct the Widget Managment
	 */
	function __construct(){
		$this->preloadAllWidgets();
	} # function - __construct
	
	
	/*
	 * Preload the Hook data an collect it in a internal Array
	 *
	 * @return boolean
	 */
	private function preloadAllWidgets(){
		global $TYPO3_CONF_VARS;

		if(!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['ext/mydashboard/class.tx_mydashboard_widgetmgm.php']['addWidget'])) 
			return false;
			
		foreach($TYPO3_CONF_VARS['SC_OPTIONS']['ext/mydashboard/class.tx_mydashboard_widgetmgm.php']['addWidget'] as $widgetKey => $classRef)
			$this->widgetsKeys[$widgetKey] = $classRef;
		
		return true;  	
	} # function - preloadAllWidgets
	
	
	/*
	 * Load a Widget with the given WidgetKey
	 *
	 * @parm String $key Widget key
	 * @return Object The Widget
	 */
	public function loadWidget($key){
		if(!isset($this->widgetsKeys[$key])) return false;
		if(isset($this->baseWidgets[$key])) return $this->baseWidgets[$key];
		
		$this->baseWidgets[$key] = &t3lib_div::getUserObj($this->widgetsKeys[$key]);
		
		if(is_object($this->baseWidgets[$key]) && !in_array('tx_mydashboard_widgetinterface', class_implements($this->baseWidgets[$key]))){
			echo 'Error: The Widget "<b>'.$key.'</b>" have to implement the interface "tx_mydashboard_widgetinterface"!<br />';
			unset($this->baseWidgets[$key]);
			return false;
		} # if

		$this->baseWidgets[$key]->setWidgetKey($key);
	
		return $this->baseWidgets[$key];
	} # function - loadWidget
	
	
	/*
	 * Get Alle Widgets (For Configuration)
	 *
	 * @return Array Widget Objects
	 */
	function getAllWidgets(){
		$widgets = array();
		
		foreach($this->widgetsKeys as $key => $value){
			$widget = $this->loadWidget($key);
			if(!$widget->init()) continue;
			$widgets[] = $widget;
		} # foreach
		
		return $widgets;
	} # function - getAllWidgets
	
	
	/*
	 * Get a widget with the user configuration
	 *
	 * @parm String $dashboardKey The Dashboard Key
	 * @return Object/Boolean The Widget
	 */
	public function getWidget($dashboardKey){
		if(!is_array($this->userConf['items'][$dashboardKey])) return false;
		
		$data = $this->userConf['items'][$dashboardKey];
		
		$widget = $this->loadWidget($data['widgetkey']);
		if(!$widget) return false;
		if(is_array($data['config']))
			$widget->setConfigVars($data['config']);
		$widget->setDashboardKey($dashboardKey);
		if(!$widget->init()) return false;
		
			
		return $widget;
	} # function - getWidget
	
	
	/*
	 * Helper function to Render a Widget Icon
	 *
	 * @parm Object Widget Object
	 * @return String The HTML for the WIdget Icon
	 */
	public function renderIcon(&$widget){
		return '<img src="'.$widget->getIcon().'" width="16" height="16" alt="'.$widget->getTitle().'" title="'.$widget->getTitle().'" />';
	} # function - renderIcon
	
	
	/*
	 * Return the User data
	 *
	 * @parm String $field The Fielname (Optional)
	 * @return Array/String the Data
	 */
	public function getUserData($field = false){
		if($field)
			return $this->userData[$field];
		return $this->userData;
	} # function - getUserData
	
	
	/*
	 * Set the widget configuration
	 *
	 * @parm String $dashKey The Dashboard Widget Key
	 * @parm Array the configuration
	 * @return boolean (Write the User conf only on true)
	 */
	public function setWidgetConf($dashKey, $conf){
		foreach($conf as $key => $val)
			$conf[$key] = htmlspecialchars($val);
			
		if(!isset($this->userConf['items'][$dashKey])) return false;
		if(!is_array($conf)) return false;
		
		if(!is_array($this->userConf['items'][$dashKey]['config'])) $this->userConf['items'][$dashKey]['config'] = array();
		
		$this->userConf['items'][$dashKey]['config'] = array_merge(
			$this->userConf['items'][$dashKey]['config'],
			$conf
		);
		
		return true;
	} # function - setWidgetConf
	
	
	/*
	 * Return the User Conf
	 *
	 * @parm String $field The Fielname (Optional)
	 */
	public function getUserConf($field = false){
		if($field)
			return $this->userConf[$field];
		return $this->userConf;
	} # function - getUserConf
	
	
	/*
	 * Set the User Conf
	 *
	 * @parm String $field The Fielname
	 * @parm String $value The Value
	 */
	public function setUserConf($field, $value){
		if(!isset($this->userConf[$field])) return false;
		$this->userConf[$field] = $value;
		return true;
	} # function - getUserConf
	
	
	/* 
	 * Set the new Order of the Dashboard Widgets
	 *
	 * @parm array $order The multi Array with the new Order
	 */
	public function setNewOrder($order){
		
		//if(count($this->userConf['position'], COUNT_RECURSIVE) != count($order, COUNT_RECURSIVE))
		//	return false;
		
		ksort($order);
		$this->userConf['position'] = $order;
		
		return true;
	} # function - setNewOrder
	
	
	/*
	 * Add a new Widget to the Dashboard
	 *
	 * @parm string $key The Widget Key
	 * @parm int $pos The position in the dashboard
	 */
	public function addWidget($key, $pos = 0){
		if(!$this->loadWidget($key)) return false;
		
		$dashboardKey = str_replace('_', '', $key).substr(md5($key.time()), 0, 15);
		
		$this->userConf['items'][$dashboardKey] = array(
			'widgetkey' => $key,
			'config' => array()
		);
		$this->userConf['position'][$pos][] = $dashboardKey;
		return true;
	} # function - addWidget
	
	
	/*
	 * Remove the Widget with the $dashboardKey from the Dashboard
	 *
	 * @parm string $dashboardKey The DashboardKey
	 */
	public function removeWidget($dashboardKey){
		if(isset($this->userConf['items'][$dashboardKey]))
			unset($this->userConf['items'][$dashboardKey]);
			
		if(!is_array($this->userConf['position'])) return false;
		
		foreach($this->userConf['position'] as $key => $value){
			if($index = array_search($dashboardKey, $value)){
				unset($value[$index]);
				$this->userConf['position'][$key] = $value;
			} # if
		} # foreach
		
		return true;
	} # function - removeWidget
	
	
	/*
	 * Get the default Conf Object and return the configuration
	 */
	private function getDefaultConf(){
		require_once(t3lib_extMgm::extPath('mydashboard').'class.tx_mydashboard_widgetmgm_defaultconf.php');
		$obj = t3lib_div::makeInstance('tx_mydashboard_widgetmgm_defaultconf');
		return $obj->getConf();
	} # function - getDefaultConf
	
	
	/*
	 * Safe the Widget configuration in the user record
	 *
	 * @parm int $userID The ID of the user Record
	 */
	public function safeUserConf($userID = false){
		if(!is_array($this->userConf))
			return false;	
		if(!$userID)
			$userID = $this->user;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users','uid='.intval($userID),array('tx_mydashboard_config' => serialize($this->userConf)));
	} # function - safeUserConf


	/*
	 * Load the Widget conf from the user
	 *
	 * @parm int $userID The User ID of the User
	 */
	public function loadUserConf($userID){
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_users','uid='.intval($userID),'','',1);
		if(!$GLOBALS['TYPO3_DB']->sql_num_rows($res)) return false;
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		$conf = @unserialize($row['tx_mydashboard_config']);
		
		$this->user = $userID;
		$this->userData = $row;
		$this->userConf = (is_array($conf))?$conf:$this->getDefaultConf();
		
		return true;
	} # function - loadUserConf	


} # class - tx_mydashboard_widgetmgm

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/class.tx_mydashboard_widgetmgm.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/class.tx_mydashboard_widgetmgm.php']);
} # if
?>