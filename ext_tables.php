<?php
if(!defined('TYPO3_MODE'))
	die('Access denied.');
	
if(TYPO3_MODE == 'BE'){
	t3lib_extMgm::addModule('user','txmydashboardM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
} # if


$tempColumns = Array (
	'tx_mydashboard_config' => Array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:mydashboard/locallang_db.xml:be_users.tx_mydashboard_config',		
		'config' => Array ('type' => 'none')
	),
	'tx_mydashboard_selfadmin' => Array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:mydashboard/locallang_db.xml:be_users.tx_mydashboard_selfadmin',		
		'config' => Array ('type' => 'check')
	),
);


t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_mydashboard_order;;;;1-1-1, tx_mydashboard_selfadmin');
?>