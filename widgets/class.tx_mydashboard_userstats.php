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

class tx_mydashboard_userstats extends tx_mydashboard_template implements tx_mydashboard_widgetinterface {


	/*
	 * initial  the Widget
	 */
	function init(){
	
		// Init Parent
		parent::init();
		
		// Build config
		$config = array(
			'item_limit' => array(
				'default' => 10,
				'type' => 'int',
			),
			'user_timeout_min' => array(
				'default' => 60,
				'type' => 'int',
			),
		);
		
		// Add Language File
		$this->addLanguageFile(t3lib_div::getFileAbsFileName('EXT:mydashboard/widgets/labels.xml'));
		
		// Set the Default config
		$this->setDefaultConfig($config);
		
		// Set title & icon
		$this->setTitle('Users Stats');
		$this->setIcon(t3lib_extMgm::extRelPath('mydashboard').'widgets/icon/tx_mydashboard_userstats.gif');
		
		// required
		return true;
	} # function - init


	/*
	 * Print the Content
	 */
	public function getContent(){
		
		// Build the Option Menu
		$options = array(
			'online' => 'Online User',
			'last' => 'Last Login',
			'last_create' => 'Last Create',
			'never' => 'Never Login'
		);
		
		// Get the Menu
		$c = $this->buildSelectMenu($options);
		
		// internal config
		$conf = array();
		$conf['timeout'] = 60*((int)$this->getConfigVar('user_timeout_min'));
		$conf['limit'] = (int)$this->getConfigVar('item_limit');
		
		// run the database quers		
		switch($_REQUEST['value']){
			case 'last':
			
				// Display FE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','lastlogin > 0','','lastlogin DESC',$conf['limit']);
				$c .= $this->showDatabaseList('FE User:',$res,'username,lastlogin');
				
				// Display BE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_users','lastlogin > 0','','lastlogin DESC',$conf['limit']);
				$c .= $this->showDatabaseList('BE User:',$res,'username,lastlogin');
				
				break;
			case 'last_create':
			
				// Display FE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','1=1','crdate DESC','',$conf['limit']);
				$c .= $this->showDatabaseList('FE User:',$res,'username,crdate');
				
				// Display BE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_users','1=1','crdate DESC','',$conf['limit']);
				$c .= $this->showDatabaseList('BE User:',$res,'username,crdate');
				
				break;
			case 'never':
			
				// Display FE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','lastlogin = 0','','crdate',$conf['limit']);
				$c .= $this->showDatabaseList('FE User:',$res,'username');
				
				// Display BE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_users','lastlogin = 0','','crdate',$conf['limit']);
				$c .= $this->showDatabaseList('BE User:',$res,'username');
				
				break;
			default:
			
				// Display FE Users
				$query = 'SELECT DISTINCT ses_userid, username, name FROM fe_sessions LEFT JOIN fe_users ON (ses_userid=uid) WHERE ses_tstamp+'.$conf['timeout'].' > unix_timestamp() OR is_online+'.$conf['timeout'].' > unix_timestamp() ORDER BY ses_tstamp LIMIT '.$conf['limit'];
				$res = $GLOBALS['TYPO3_DB']->sql_query($query);
				$c .= $this->showDatabaseList('FE User:',$res,'username');
				
				// Display BE Users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','be_sessions,be_users','be_sessions.ses_userid=be_users.uid AND ses_tstamp+'.$conf['timeout'].' > unix_timestamp()','username','ses_tstamp',$conf['limit']);
				$c .= $this->showDatabaseList('BE User:',$res,'username');
				
				break;
		} # switch
		
		return $c;	
	} # function - getContent


} # class - tx_mydashboard_userstats

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_userstats.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_userstats.php']);
} # if
?>