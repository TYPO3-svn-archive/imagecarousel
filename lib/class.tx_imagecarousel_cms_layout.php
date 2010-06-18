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

/**
 * 'cms_layout' for the 'imagecarousel' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_imagecarousel
 */
class tx_imagecarousel_cms_layout
{
	/**
	 * Returns information about this extension's pi1 plugin
	 *
	 * @param  array  $params Parameters to the hook
	 * @param  object $pObj   A reference to calling object
	 * @return string Information about pi1 plugin
	 */
	function getExtensionSummary($params, &$pObj)
	{
		if ($params['row']['list_type'] == 'imagecarousel_pi1') {
			$result = null;
			$data = t3lib_div::xml2array($params['row']['pi_flexform']);
			if (is_array($data)) {
				$skin      = ($data['data']['control']['lDEF']['skin']['vDEF'] ? $data['data']['control']['lDEF']['skin']['vDEF'] : $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.from_ts'));
				$direction = ($data['data']['control']['lDEF']['vertical']['vDEF'] ? $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.vertical') : $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.horizontal'));
				$result .= sprintf($GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.style'), '<strong>'.($skin ? $skin : $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.from_ts')).'</strong>', '<strong>'.$direction.'</strong>').'<br/>';
				/*
				$mode = $data['data']['general']['lDEF']['mode']['vDEF'];
				switch ($mode) {
					case 'upload' : {
						$images = t3lib_div::trimExplode(',', $data['data']['general']['lDEF']['images']['vDEF'], true);
						foreach ($images as $image) {
							$result .= t3lib_BEfunc::getThumbNail($GLOBALS['BACK_PATH'].'thumbs.php', PATH_site.'uploads/tx_imagecarousel/'.$image, '', '');
						}
						break;
					}
					case 'dam' : {
						$result .= 'dam';
						break;
					}
					case 'dam_catedit' : {
						$result .= 'dam_catedit';
						break;
					}
				}
				$result .= '<br/>';
				*/
			} else {
				$result = $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:cms_layout.not_configured').'<br/>';
			}
		}
		return $result;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_cms_layout.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_cms_layout.php']);
}

?>