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

/**
 * Class that renders fields for the extensionmanager configuration
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_imagecarousel
 */
class tx_imagecarousel_tsparserext
{
	/**
	 * Return the dropdown with all skins for constant editor
	 * 
	 * @param array $params
	 * @param object $tsObj
	 */
	public function getSkins(&$params, &$tsObj)
	{
		$itemsProcFunc = t3lib_div::makeInstance('tx_imagecarousel_itemsProcFunc');
		$config = $itemsProcFunc->getSkins(array('items'=> array()), array());
		$items = $config['items'];

		$raname = substr(md5($params['fieldName']), 0, 10);
		$aname = '\'' . $raname . '\'';
		$fN = $params['fieldName'];

		$p_field = '';
		foreach ($items as $var) {
			$label = $var[0];
			$value = isset($var[1]) ? $var[1] : $var[0];
			$sel = '';
			if ($value == $params['value']) {
				$sel = ' selected';
			}
			$p_field .= '<option value="' . htmlspecialchars($value) . '"' . $sel . '>' . $GLOBALS['LANG']->sL($label) . '</option>';
		}
		$p_field = '<select id="' . $fN . '" name="' . $fN . '" onChange="uFormUrl(' . $aname . ')">' . $p_field . '</select>';

		return $p_field;
	}

	/**
	 * Shows the update Message
	 *
	 * @return	string
	 */
	public function displayMessage(&$params, &$tsObj)
	{
		$out = '';

		if (t3lib_div::int_from_ver(TYPO3_version) < 4003000) {
			// 4.3.0 comes with flashmessages styles. For older versions we include the needed styles here
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('imagecarousel');
			$out .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'compat/flashmessages.css" media="screen" />';
		}

		$checkConfig = null;
		if ($this->checkConfig() === false) {
			$checkConfig .= '
	<div class="typo3-message message-warning">
		<div class="message-header">' . $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:extmng.updatermsgHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL('LLL:EXT:imagecarousel/locallang.xml:extmng.updatermsg') . '
		</div>
	</div>';
		}

		$out .= '
<div style="position:absolute;top:10px;right:10px; width:300px;">
	' . $checkConfig . '
</div>';

		return $out;
	}

	/**
	 * Check the config for a gifen feature
	 * 
	 * @return boolean
	 */
	public function checkConfig()
	{
		$confDefault = array(
			'skinFolder',
			'useSelectInsteadCheckbox',
		);
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagecarousel']);
		foreach ($confDefault as $val) {
			if (! isset($confArr[$val]) && ! isset($_POST['data'][$val])) {
				return false;
			}
		}
		return true;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_tsparserext.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/lib/class.tx_imagecarousel_tsparserext.php']);
}
?>