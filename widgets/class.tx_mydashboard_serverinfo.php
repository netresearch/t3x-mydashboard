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

class tx_mydashboard_serverinfo extends tx_mydashboard_template implements tx_mydashboard_widgetinterface {

	function getContent(){
		return '<font color="red">No Content yet</font>';
	}

	function init(){
		$ico = t3lib_extMgm::extRelPath('mydashboard').'widgets/icon/tx_mydashboard_serverinfo.png';
		$this->setIcon($ico);
		$this->setTitle('Server Info');
		
		return true;
	
	}


} # class - tx_mydashboard_serverinfo

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_serverinfo.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_serverinfo.php']);
} # if
?>