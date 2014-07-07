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

require_once(t3lib_extMgm::extPath('mydashboard', 'templates/class.tx_mydashboard_template.php'));
require_once(t3lib_extMgm::extPath('mydashboard', 'templates/interface.tx_mydashboard_widgetinterface.php'));

class tx_mydashboard_latestnews extends tx_mydashboard_template implements tx_mydashboard_widgetinterface {

	/*
	 * initial  the Widget
	 */
	function init(){
	
		// Check if tt_news is active
		if(!t3lib_extMgm::isLoaded('tt_news')) return false;
		
		// Init Parent
		parent::init();
		
		// Build config
		$config = array(
			'item_limit' => array(
				'default' => 10,
				'type' => 'int',
			),
			'sysFolderID' => array(
				'default' => 0,
				'type' => 'int',
			),
		);
		
		// Set the Default config
		$this->setDefaultConfig($config);

		// Set title & icon
		$title = 'Latest News';
		if((int)$this->getConfigVar('sysFolderID')) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','pages','deleted=0 AND hidden=0 AND uid='.(int)$this->getConfigVar('sysFolderID'),'','',1);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if(sizeof($row['title']) > 0) $title .= ' - '.$row['title'];
		} # if
		
		$this->setTitle($title);
		$this->setIcon(t3lib_extMgm::extRelPath('tt_news').'/ext_icon.gif');
		
		return true;
	} # function - init
	
	/*
	 * Get the Widget Content
	 */
	function getContent(){
	
		if(!(int)$this->getConfigVar('sysFolderID')) return 'Setup the Widget. 0 is a illigal UID for tt_news Page records!';
		
		// Build up the tt_news listing
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tt_news','deleted=0 AND hidden=0 AND pid='.(int)$this->getConfigVar('sysFolderID'),'','crdate DESC',(int)$this->getConfigVar('item_limit'));
		$c .= $this->showDatabaseList('News:',$res,'title,crdate', array('table' => 'tt_news', 'pageID' => (int)$this->getConfigVar('sysFolderID')));
		
		return $c;	
	} # function - getContent

} # class - tx_mydashboard_latestnews

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_latestnews.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_latestnews.php']);
} # if
?>