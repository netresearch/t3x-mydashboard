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

class tx_mydashboard_sysnotepad extends tx_mydashboard_template implements tx_mydashboard_widgetinterface {

	/*
	 * initial  the Widget
	 */
	function init(){
	
		// Check if sys_notepad is active
		if(!t3lib_extMgm::isLoaded('sys_notepad')) return false;
		
		// Init Parent
		parent::init();
		
		// Set title & icon
		$this->setTitle($this->getInternalLabel('notepad'));
		$this->setIcon(t3lib_extMgm::extRelPath('sys_notepad').'/ext_icon.gif');
		
		return true;
	} # function - init
	
	/*
	 * Get the Widget Content
	 */
	function getContent(){

		// Build up the Notepadoutput
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('note','sys_notepad','sys_notepad.cruser_id='.intval($GLOBALS['BE_USER']->user['uid']));
		$c .= $this->showDatabaseList($this->getInternalLabel('notes').':',$res,'note');
		
		return $c;	
	} # function - getContent

} # class - tx_mydashboard_sysnotepad

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_sysnotepad.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_sysnotepad.php']);
} # if
?>