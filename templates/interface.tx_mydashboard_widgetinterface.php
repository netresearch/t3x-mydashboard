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

interface tx_mydashboard_widgetinterface  {

	/*
	 * This function return the Content of the Widget
	 *
	 * @return String
	 */
	public function getContent();
	
	
	/*
	 * This function init the Widget and have to return a boolean
	 * true = All Right; false = The Widget isn´t Display in the Dashboard
	 *
	 * @return boolean
	 */
	public function init();

} # interface - tx_mydashboard_widgetinterface
?>