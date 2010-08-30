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

require_once(t3lib_extMgm::extPath('imagecarousel').'pi1/class.tx_imagecarousel_pi1.php');

/**
 * Plugin 'Image carousel' for the 'imagecarousel' extension.
 *
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_imagecarousel
 */
class tx_imagecarousel_pi2 extends tx_imagecarousel_pi1
{
	var $prefixId      = 'tx_imagecarousel_pi2';
	var $scriptRelPath = 'pi2/class.tx_imagecarousel_pi2.php';
	var $extKey        = 'imagecarousel';
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf)
	{
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// define the key of the element
		$this->setContentKey();

		$pageID = false;
		if ($this->cObj->data['list_type'] == $this->extKey.'_pi2') {
			$this->type = 'normal';
			// It's a content, al data from flexform
			// Set the Flexform information
			$this->pi_initPIflexForm();
			$piFlexForm = $this->cObj->data['pi_flexform'];
			foreach ($piFlexForm['data'] as $sheet => $data) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
					}
				}
			}
			// define the key of the element
			$this->setContentKey($this->extKey . "_c" . $this->cObj->data['uid']);

			// define the images
			switch ($this->lConf['mode']) {
				case "" : {}
				case "folder" : {}
				case "upload" : {
					$this->setDataUpload();
					break;
				}
				case "dam" : {
					$this->setDataDam(false);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(true);
					break;
				}
			}

			// overwrite the config
			if ($this->lConf['imagewidth']) {
				$this->conf['imagewidth'] = $this->lConf['imagewidth'];
			}
			// 
			if ($this->lConf['imageheight']) {
				$this->conf['imageheight'] = $this->lConf['imageheight'];
			}
			// 
			if (is_numeric($this->lConf['carouselwidth'])) {
				$this->conf['carouselwidth'] = $this->lConf['carouselwidth'];
			}
			// 
			if (is_numeric($this->lConf['carouselheight'])) {
				$this->conf['carouselheight'] = $this->lConf['carouselheight'];
			}
			// for the Cloud-Carousel
			if (is_numeric($this->lConf['minScale'])) {
				$this->conf['minScale'] = $this->lConf['minScale'];
			}
			if (is_numeric($this->lConf['xPos'])) {
				$this->conf['xPos'] = $this->lConf['xPos'];
			}
			if (is_numeric($this->lConf['yPos'])) {
				$this->conf['yPos'] = $this->lConf['yPos'];
			}
			if (is_numeric($this->lConf['reflHeight'])) {
				$this->conf['reflHeight'] = $this->lConf['reflHeight'];
			}
			if (is_numeric($this->lConf['reflGap'])) {
				$this->conf['reflGap'] = $this->lConf['reflGap'];
			}
			if (is_numeric($this->lConf['reflOpacity'])) {
				$this->conf['reflOpacity'] = $this->lConf['reflOpacity'];
			}
			if (is_numeric($this->lConf['xRadius'])) {
				$this->conf['xRadius'] = $this->lConf['xRadius'];
			}
			if (is_numeric($this->lConf['yRadius'])) {
				$this->conf['yRadius'] = $this->lConf['yRadius'];
			}
			if (is_numeric($this->lConf['speed'])) {
				$this->conf['speed'] = $this->lConf['speed'];
			}
			if (is_numeric($this->lConf['FPS'])) {
				$this->conf['FPS'] = $this->lConf['FPS'];
			}
			if ($this->lConf['autoRotate']) {
				$this->conf['autoRotate'] = $this->lConf['autoRotate'];
			}
			if (is_numeric($this->lConf['autoRotateDelay'])) {
				$this->conf['autoRotateDelay'] = $this->lConf['autoRotateDelay'];
			}
			$this->conf['mouseWheel']   = $this->lConf['mouseWheel'];
			$this->conf['bringToFront'] = $this->lConf['bringToFront'];
			$this->conf['buttonLeft']   = $this->lConf['buttonLeft'];
			$this->conf['buttonRight']  = $this->lConf['buttonRight'];
			$this->conf['titleBox']     = $this->lConf['titleBox'];
			$this->conf['altBox']       = $this->lConf['altBox'];

			return $this->parseTemplate();
		}
	}

	/**
	 * Parse all images into the template
	 * @param $data
	 * @return string
	 */
	public function parseTemplate($dir='', $onlyJS=false)
	{
		
		// define the directory of images
		if ($dir == '') {
			$dir = $this->imageDir;
		}

		// define the contentKey if not exist
		if ($this->getContentKey() == '') {
			$this->setContentKey($this->extKey . '_key');
		}

		// define the jQuery mode and function
		if ($this->conf['jQueryNoConflict']) {
			$jQueryNoConflict = "jQuery.noConflict();";
		} else {
			$jQueryNoConflict = "";
		}

		preg_match("/^([0-9]*)/i", $this->conf['imagewidth'],  $reg_width);
		preg_match("/^([0-9]*)/i", $this->conf['imageheight'], $reg_height);

		$css_width  = (is_numeric($reg_width[1])  ? $reg_width[1]."px"  : $this->conf['imagewidth']);
		$css_height = (is_numeric($reg_height[1]) ? $reg_height[1]."px" : $this->conf['imageheight']);

		// define the js files
		$this->addJsFile($this->conf['jQueryCloudCarousel']);

		// get the options from config
		$options = array();
		if (is_numeric($this->conf['minScale'])) {
			$options[] = "minScale: {$this->conf['minScale']}";
		}
		if (is_numeric($this->conf['reflHeight'])) {
			$options[] = "reflHeight: {$this->conf['reflHeight']}";
		}
		if (is_numeric($this->conf['reflGap'])) {
			$options[] = "reflGap: {$this->conf['reflGap']}";
		}
		if (is_numeric($this->conf['reflOpacity'])) {
			$options[] = "reflOpacity: {$this->conf['reflOpacity']}";
		}
		if (is_numeric($this->conf['xRadius'])) {
			$options[] = "xRadius: {$this->conf['xRadius']}";
		}
		if (is_numeric($this->conf['yRadius'])) {
			$options[] = "yRadius: {$this->conf['yRadius']}";
		}
		$options[] = "xPos: ".(is_numeric($this->conf['xPos']) ? $this->conf['xPos'] : intval($this->conf['carouselwidth'] / 2));
		$options[] = "yPos: ".(is_numeric($this->conf['yPos']) ? $this->conf['yPos'] : intval($this->conf['carouselheight'] / 2) - $this->conf['reflHeight']);
		if (is_numeric($this->conf['speed'])) {
			$options[] = "speed: {$this->conf['speed']}";
		}
		if ($this->conf['FPS'] > 0) {
			$options[] = "FPS: {$this->conf['FPS']}";
		}
		if ($this->conf['autoRotate']) {
			$options[] = "autoRotate: '{$this->conf['autoRotate']}'";
		}
		if (is_numeric($this->conf['autoRotateDelay'])) {
			$options[] = "autoRotateDelay: {$this->conf['autoRotateDelay']}";
		}
		if ($this->conf['mouseWheel']) {
			$options[] = "mouseWheel: true";
			$this->addJsFile($this->conf['jQueryMouseWheel']);
		}
		if ($this->conf['bringToFront']) {
			$options[] = "bringToFront: true";
		}
		if ($this->conf['buttonLeft']) {
			$options[] = "buttonLeft: jQuery('#{$this->getContentKey()}-left')";
		}
		if ($this->conf['buttonRight']) {
			$options[] = "buttonRight: jQuery('#{$this->getContentKey()}-right')";
		}
		if ($this->conf['titleBox']) {
			$options[] = "titleBox: jQuery('#{$this->getContentKey()}-alt')";
		}
		if ($this->conf['altBox']) {
			$options[] = "altBox: jQuery('#{$this->getContentKey()}-title')";
		}

		$this->addJS(
$jQueryNoConflict . "
jQuery(document).ready(function() {
	jQuery('#{$this->getContentKey()}').CloudCarousel(".(count($options) ? "{\n		".implode(",\n		", $options)."\n	}" : "").");
});");
		$this->addCSS("
#{$this->getContentKey()} {
	width: {$this->conf['carouselwidth']}px;
	height: {$this->conf['carouselheight']}px;
	overflow: scroll;
	display: hidden
}");

		// Add the ressources
		$this->addResources();

		if ($onlyJS === true) {
			return true;
		}

		$return_string = null;
		$images = null;
		$navigation = null;
		$markerArray = array(
			'BUTTON_LEFT'  => null,
			'BUTTON_RIGHT' => null,
			'TITLE_BOX'    => null,
			'ALT_BOX'      => null,
		);
		$GLOBALS['TSFE']->register['key'] = $this->getContentKey();
		$GLOBALS['TSFE']->register['imagewidth']  = $this->conf['imagewidth'];
		$GLOBALS['TSFE']->register['imageheight'] = $this->conf['imageheight'];
		$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = 0;
		if (count($this->images) > 0) {
			foreach ($this->images as $key => $image_name) {
				$image = null;
				$imgConf = $this->conf['carousel.'][$this->type.'.']['image.'];
				$totalImagePath = $this->imageDir . $image_name;
				$GLOBALS['TSFE']->register['file']    = $totalImagePath;
				$GLOBALS['TSFE']->register['href']    = $this->hrefs[$key];
				$GLOBALS['TSFE']->register['caption'] = $this->captions[$key];
				$GLOBALS['TSFE']->register['description'] = $this->description[$key];
				$GLOBALS['TSFE']->register['CURRENT_ID'] = $GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] + 1;
				if ($this->hrefs[$key]) {
					$imgConf['imageLinkWrap.'] = $imgConf['imageHrefWrap.'];
				}
				$image = $this->cObj->IMAGE($imgConf);
				$images .= $this->cObj->typolink($image, $imgConf['imageLinkWrap.']);
				$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] ++;
			}
			if ($this->conf['buttonLeft']) {
				$buttonLeft = trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['buttonLeft'], $this->conf['carousel.'][$this->type.'.']['buttonLeft.']));
				$markerArray['BUTTON_LEFT'] = $this->cObj->stdWrap($buttonLeft,  $this->conf['carousel.'][$this->type.'.']['buttonLeftWrap.']);
			}
			if ($this->conf['buttonRight']) {
				$buttonRight = trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['buttonRight'], $this->conf['carousel.'][$this->type.'.']['buttonRight.']));
				$markerArray['BUTTON_RIGHT'] = $this->cObj->stdWrap($buttonRight, $this->conf['carousel.'][$this->type.'.']['buttonRightWrap.']);
			}
			if ($this->conf['titleBox']) {
				$titleBox = trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['titleBox'], $this->conf['carousel.'][$this->type.'.']['titleBox.']));
				$markerArray['TITLE_BOX'] = $this->cObj->stdWrap($titleBox, $this->conf['carousel.'][$this->type.'.']['titleBoxWrap.']);
			}
			if ($this->conf['altBox']) {
				$altBox = trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['altBox'], $this->conf['carousel.'][$this->type.'.']['altBox.']));
				$markerArray['ALT_BOX'] = $this->cObj->stdWrap($altBox, $this->conf['carousel.'][$this->type.'.']['altBoxWrap.']);
			}
			// the stdWrap
			$images = $this->cObj->stdWrap($images, $this->conf['carousel.'][$this->type.'.']['stdWrap.']);
			$return_string = $this->cObj->substituteMarkerArray($images, $markerArray, '###|###', 0);
		}
		return $this->pi_wrapInBaseClass($return_string);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi2/class.tx_imagecarousel_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi2/class.tx_imagecarousel_pi2.php']);
}

?>