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

class tx_mydashboard_rssfeed extends tx_mydashboard_template implements tx_mydashboard_widgetinterface {


	/*
	 * initial  the Widget
	 */
	function init(){
	
		// Init Parent
		parent::init();
		
		// Build config
		$config = array(
			'item_limit' => array(
				'default' => 6,
				'type' => 'int',
			),
			'feed_title' => array(
				'default' => 'Any Feed',
				'type' => 'string',
			),
			'feed_url' => array(
				'default' => '',
				'type' => 'string',
			),
			'cache_time_h' => array(
				'default' => 12,
				'type' => 'int',
			),
		);
		
		// Add Language File
		$this->addLanguageFile(t3lib_div::getFileAbsFileName('EXT:mydashboard/widgets/labels.xml'));
		
		// Set the Default config
		$this->setDefaultConfig($config);
		
		// Set title & icon
		$this->setIcon(t3lib_extMgm::extRelPath('mydashboard').'widgets/icon/tx_mydashboard_rssfeed.png');
		$this->setTitle('RSS: '.$this->getConfigVar('feed_title'));
		
		// required
		return true;
	} # function - __construct


	/*
	 * Get the Content
	 */
	function getContent(){
		$url = $this->getConfigVar('feed_url');
		if($url == '') return 'Enter a RSS Feed in this Widget configuration.';
		
		// Cache File path
		$cacheFile = PATH_site.'typo3temp/mydashboard'.substr(md5($url.$this->getConfigVar('feed_title')), 0, 15).'.cache';
		
		// Update rules
		$updateRSS = (isset($_POST['ajax']) && isset($_POST['action']) && $_POST['action'] == 'refresh');
		
		// Load a Cache File
		if(file_exists($cacheFile) && !$updateRSS){		
			$fileinfo = fileatime($cacheFile);
			if($fileinfo > (time()-(int)$this->getConfigVar('cache_time_h'))){
				$data = $this->loadRSSData($cacheFile);
				return $this->renderRSSArray($data);		
			} # if
		} # if
		
		// Load the rss2array class and fetch the RSS Feed
		require_once(t3lib_extMgm::extPath('mydashboard', 'widgets/helper/rss2array.php'));
		$rss_array = rss2array($url);
		
		// Safe the Feed in a Cache File
		$this->safeRSSData($cacheFile, $rss_array);
		
		return $this->renderRSSArray($rss_array);
	} # function - getContent


	/*
	 * Render the RSS Array
	 *
	 * @parm Array $array The RSS Array
	 */
	private function renderRSSArray($array){	
		if(!is_array($array) || !isset($array['items'])) return 'RSS fetch or render error!';
		
		$content = '<ul class="rssfeed">';
		$i = 0;
		foreach($array['items'] as $item){
		
			// Limit for the Items
			if($i >= (int)$this->getConfigVar('item_limit')) break;
			
			// The internal Key
			$key = 'k'.substr(md5(implode('',$item)),0,10);
			
			// The Content
			$content .= '<li><h3 onclick="new Effect.toggle($(\''.$key.'\'),\'appear\')">'.$item['title'].'</h3><span id="'.$key.'" style="display: none;" class="content">'.$item['description'].'<span class="more"><a href="'.$item['link'].'" target="_blank">'.$this->getInternalLabel('more').'</a></span></span></li>';
			
			// Increment the counter
			$i++;
			
		} # for
		
		// Return the rendered RSS array
		return $content.'</ul>';
	} # function - renderRSSArray
	
	
	/*
	 * Load the RSS Data from the Filesystem
	 *
	 * @parm String $filename the cache filename
	 */
	private function loadRSSData($fileName){
		if ($fd = @fopen ($fileName, 'r')) {
			$length = filesize ($fileName);
			if ($length > 0)
				$out = fread ($fd, $length);
			else
				return false;
			fclose ($fd);
			return unserialize($out);
		} # if
		return false;
	} # function - loadRSSData
	
	
	/*
	 * Safe the RSS Data in the Filesystem
	 *
	 * @parm String $fileName the cache filename
	 * @parm Array $rssArray the RSS Items in a array
	 */
	private function safeRSSData($fileName, $rssArray){
		if(file_exists($fileName))
			unlink($fileName);
		t3lib_div::writeFileToTypo3tempDir($fileName,serialize($rssArray));
	} # function - safeRSSData


} # class - tx_mydashboard_rssfeed

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_rssfeed.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mydashboard/widgets/class.tx_mydashboard_rssfeed.php']);
} # if
?>