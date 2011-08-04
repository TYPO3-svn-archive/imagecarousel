<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (PATH_t3lib . 'class.t3lib_page.php');

 /**
 * 'itemsProcFunc' for the 'imagecarousel' extension.
 * 
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_imagecarousel
 */
class tx_imagecarousel_itemsProcFunc
{
	/**
	 * Get defined Skin for drupdown
	 * @return array
	 */
	function getSkins($config, $item)
	{
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagecarousel']);
		if (! is_dir(t3lib_div::getFileAbsFileName($confArr['skinFolder']))) {
			// if the defined folder does not exist, define the default folder
			$confArr['skinFolder'] = "EXT:imagecarousel/res/skins/";
		}
		$items = t3lib_div::get_dirs(t3lib_div::getFileAbsFileName($confArr['skinFolder']));
		if (count($items) > 0) {
			$optionList = array();
			foreach ($items as $key => $item) {
				$item = trim($item);
				if (! preg_match('/^\./', $item)) {
					$optionList[] = array(
						$item,
						$item
					);
				}
			}
			$config['items'] = array_merge($config['items'], $optionList);
		}
		return $config;
	}


	/**
	 * Get all modes for image selection
	 * @return array
	 */
	function getModes($config, $item)
	{
		$optionList = array();
		$optionList[] = array(
			$GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang_db.xml:tt_content.pi_flexform.mode.I.upload'),
			"upload",
			"EXT:imagecarousel/mode_upload.gif"
		);
		$optionList[] = array(
			$GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang_db.xml:tt_content.pi_flexform.mode.I.rte'),
			"uploadRTE",
			"EXT:imagecarousel/mode_rte.gif"
		);
		if (t3lib_extMgm::isLoaded("dam")) {
			$optionList[] = array(
				$GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang_db.xml:tt_content.pi_flexform.mode.I.dam'),
				"dam",
				"EXT:imagecarousel/mode_dam.gif"
			);
			if (t3lib_extMgm::isLoaded("dam_catedit")) {
				$optionList[] = array(
					$GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang_db.xml:tt_content.pi_flexform.mode.I.dam_catedit'),
					"dam_catedit",
					"EXT:imagecarousel/mode_damcat.gif"
				);
			}
		}
		$config['items'] = array_merge($config['items'], $optionList);
		return $config;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_itemsProcFunc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_itemsProcFunc.php']);
}
?>