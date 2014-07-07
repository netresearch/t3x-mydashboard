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

if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) < 6002000) {
	require_once(PATH_site.'typo3/interfaces/interface.backend_toolbaritem.php');
}

class tx_mydashboard_additionalToolbarIcons implements backend_toolbarItem {

	/**
	 * constructor that receives a back reference to the backend
	 *
	 * @param       TYPO3backend    TYPO3 backend object reference
	 */
	public function __construct(TYPO3backend &$backendReference = null) {
		$this->backennd = $backendReference;
		$this->_REL_PATH = t3lib_extMgm::extRelPath('mydashboard');
	}
	
	/**
	 * checks whether the user has access to this toolbar item
	 *
	 * @return  boolean  true if user has access, false if not
	 */
	public function checkAccess() {
		return true;
	}
	
	/**
	 * renders the toolbar item
	 *
 	 * @return      string  the toolbar item rendered as HTML string
	 */
	public function render() {
		
		$output = '<a href="#" onclick="top.goToModule(\'user_txmydashboardM1\');this.blur();return false;" class="toolbar-item"><img src="'.$this->_REL_PATH.'mod1/home.png" width="16" height="16" title="myDashboard" alt="" /></a>';
		
		return $output;
	}

	/**
	 * returns additional attributes for the list item in the toolbar
	 *
	 * @return      string          list item HTML attibutes
	 */
	public function getAdditionalAttributes() {
		return ' id="mydashboard-links-menu"';
	}
	
} # class - tx_mydashboard_additionalToolbarIcons

# add the Item
$TYPO3backend->addCss('#mydashboard-links-menu { width:30px; }');
$TYPO3backend->addToolbarItem('tx_mydashboard_additionalToolbarIcons', 'tx_mydashboard_additionalToolbarIcons');

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/class.tx_mydashboard_additionalToolbarIcons.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/class.tx_mydashboard_additionalToolbarIcons.php']);
} # if
?>