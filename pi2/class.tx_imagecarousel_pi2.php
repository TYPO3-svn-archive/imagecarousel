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
	public $prefixId      = 'tx_imagecarousel_pi2';
	public $scriptRelPath = 'pi2/class.tx_imagecarousel_pi2.php';
	public $extKey        = 'imagecarousel';
	public $pi_checkCHash = true;

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

			$this->lConf['mode']          = $this->getFlexformData('general', 'mode');
			$this->lConf['images']        = $this->getFlexformData('general', 'images', ($this->lConf['mode'] == 'upload'));
			$this->lConf['hrefs']         = $this->getFlexformData('general', 'hrefs', ($this->lConf['mode'] == 'upload'));
			$this->lConf['captions']      = $this->getFlexformData('general', 'captions', ($this->lConf['mode'] == 'upload'));
			$this->lConf['damimages']     = $this->getFlexformData('general', 'damimages', ($this->lConf['mode'] == 'dam'));
			$this->lConf['damcategories'] = $this->getFlexformData('general', 'damcategories', ($this->lConf['mode'] == 'dam_catedit'));

			$imagesRTE = $this->getFlexformData('general', 'imagesRTE', ($this->lConf['mode'] == 'uploadRTE'));
			$this->lConf['imagesRTE'] = array();
			if (isset($imagesRTE['el']) && count($imagesRTE['el']) > 0) {
				foreach ($imagesRTE['el'] as $elKey => $el) {
					if (is_numeric($elKey)) {
						$this->lConf['imagesRTE'][] = array(
							"image"       => $el['data']['el']['image']['vDEF'],
							"href"        => $el['data']['el']['href']['vDEF'],
							"caption"     => $el['data']['el']['caption']['vDEF'],
							"description" => $this->pi_RTEcssText($el['data']['el']['description']['vDEF']),
						);
					}
				}
			}

			$this->lConf['imagewidth']      = $this->getFlexformData('control', 'imagewidth');
			$this->lConf['imageheight']     = $this->getFlexformData('control', 'imageheight');
			$this->lConf['carouselwidth']   = $this->getFlexformData('control', 'carouselwidth');
			$this->lConf['carouselheight']  = $this->getFlexformData('control', 'carouselheight');
			$this->lConf['minScale']        = $this->getFlexformData('control', 'minScale');
			$this->lConf['xRadius']         = $this->getFlexformData('control', 'xRadius');
			$this->lConf['yRadius']         = $this->getFlexformData('control', 'yRadius');
			$this->lConf['xPos']            = $this->getFlexformData('control', 'xPos');
			$this->lConf['yPos']            = $this->getFlexformData('control', 'yPos');

			$this->lConf['buttonLeft']      = $this->getFlexformData('buttons', 'buttonLeft');
			$this->lConf['buttonRight']     = $this->getFlexformData('buttons', 'buttonRight');
			$this->lConf['titleBox']        = $this->getFlexformData('buttons', 'titleBox');
			$this->lConf['altBox']          = $this->getFlexformData('buttons', 'altBox');

			$this->lConf['reflHeight']      = $this->getFlexformData('reflection', 'reflHeight');
			$this->lConf['reflGap']         = $this->getFlexformData('reflection', 'reflGap');
			$this->lConf['reflOpacity']     = $this->getFlexformData('reflection', 'reflOpacity');

			$this->lConf['speed']           = $this->getFlexformData('movement', 'speed');
			$this->lConf['FPS']             = $this->getFlexformData('movement', 'FPS');
			$this->lConf['autoRotate']      = $this->getFlexformData('movement', 'autoRotate');
			$this->lConf['autoRotateDelay'] = $this->getFlexformData('movement', 'autoRotateDelay');
			$this->lConf['mouseWheel']      = $this->getFlexformData('movement', 'mouseWheel');
			$this->lConf['bringToFront']    = $this->getFlexformData('movement', 'bringToFront');

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
				case "uploadRTE" : {
					$this->setDataUploadRTE();
					break;
				}
				case "dam" : {
					$this->setDataDam(false, 'tt_content', $this->cObj->data['uid']);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(true, 'tt_content', $this->cObj->data['uid']);
					break;
				}
			}

			// overwrite the config
			if ($this->lConf['imagewidth']) {
				$this->conf['imagewidth'] = $this->lConf['imagewidth'];
			}
			if ($this->lConf['imageheight']) {
				$this->conf['imageheight'] = $this->lConf['imageheight'];
			}
			if ($this->lConf['carouselwidth'] > 0) {
				$this->conf['carouselwidth'] = $this->lConf['carouselwidth'];
			}
			if ($this->lConf['carouselheight'] > 0) {
				$this->conf['carouselheight'] = $this->lConf['carouselheight'];
			}
			// for the Cloud-Carousel
			if ($this->lConf['minScale'] > 0) {
				$this->conf['minScale'] = $this->lConf['minScale'];
			}
			if ($this->lConf['xPos'] > 0) {
				$this->conf['xPos'] = $this->lConf['xPos'];
			}
			if ($this->lConf['yPos'] > 0) {
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
			if ($this->lConf['speed'] > 0) {
				$this->conf['speed'] = $this->lConf['speed'];
			}
			if ($this->lConf['FPS'] > 0) {
				$this->conf['FPS'] = $this->lConf['FPS'];
			}
			if ($this->lConf['autoRotate']) {
				$this->conf['autoRotate'] = $this->lConf['autoRotate'];
			}
			if ($this->lConf['autoRotateDelay'] > 0) {
				$this->conf['autoRotateDelay'] = $this->lConf['autoRotateDelay'];
			}
			// Will be overridden, if not "from TS"
			if ($this->lConf['mouseWheel'] < 2) {
				$this->conf['mouseWheel'] = $this->lConf['mouseWheel'];
			}
			if ($this->lConf['bringToFront'] < 2) {
				$this->conf['bringToFront'] = $this->lConf['bringToFront'];
			}
			if ($this->lConf['buttonLeft'] < 2) {
				$this->conf['buttonLeft'] = $this->lConf['buttonLeft'];
			}
			if ($this->lConf['buttonRight'] < 2) {
				$this->conf['buttonRight'] = $this->lConf['buttonRight'];
			}
			if ($this->lConf['titleBox'] < 2) {
				$this->conf['titleBox'] = $this->lConf['titleBox'];
			}
			if ($this->lConf['altBox'] < 2) {
				$this->conf['altBox'] = $this->lConf['altBox'];
			}
		} else {
			$this->type = 'header';
			// It's the header
			$used_page = array();
			$pageID    = false;
			foreach ($GLOBALS['TSFE']->rootLine as $page) {
				if (! $pageID) {
					if (
						(($page['tx_imagecarousel_mode'] == 'upload' || ! $page['tx_imagecarousel_mode']) && trim($page['tx_imagecarousel_images']) != '') ||
						($page['tx_imagecarousel_mode'] == 'dam'         && trim($page['tx_imagecarousel_damimages']) != '') ||
						($page['tx_imagecarousel_mode'] == 'dam_catedit' && trim($page['tx_imagecarousel_damcategories']) != '') ||
						$this->conf['disableRecursion'] ||
						$page['tx_imagecarousel_stoprecursion']
					) {
						$used_page = $page;
						$pageID    = $used_page['uid'];
						$this->lConf['mode']          = $used_page['tx_imagecarousel_mode'];
						$this->lConf['damcategories'] = $used_page['tx_imagecarousel_damcategories'];
					}
				}
			}
			if ($pageID) {
				if ($this->sys_language_uid) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_imagecarousel_images, tx_imagecarousel_hrefs, tx_imagecarousel_captions','pages_language_overlay','pid='.intval($pageID).' AND sys_language_uid='.$this->sys_language_uid,'','',1);
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				}
				// define the images
				switch ($this->lConf['mode']) {
					case "" : {}
					case "folder" : {}
					case "upload" : {
						$this->images   = t3lib_div::trimExplode(',',     $used_page['tx_imagecarousel_images']);
						$this->hrefs    = t3lib_div::trimExplode(chr(10), $used_page['tx_imagecarousel_hrefs']);
						$this->captions = t3lib_div::trimExplode(chr(10), $used_page['tx_imagecarousel_captions']);
						// Language overlay
						if ($this->sys_language_uid) {
							if (trim($row['tx_imagecarousel_images']) != '') {
								$this->images   = t3lib_div::trimExplode(',',     $row['tx_imagecarousel_images']);
								$this->hrefs    = t3lib_div::trimExplode(chr(10), $row['tx_imagecarousel_hrefs']);
								$this->captions = t3lib_div::trimExplode(chr(10), $row['tx_imagecarousel_captions']);
							}
						}
						break;
					}
					case "dam" : {
						$this->setDataDam(false, 'pages', $pageID);
						break;
					}
					case "dam_catedit" : {
						$this->setDataDam(true, 'pages', $pageID);
						break;
					}
				}
			}
		}

		return $this->parseTemplate();
	}

	/**
	 * Parse all images into the template
	 * @param $data
	 * @return string
	 */
	public function parseTemplate($dir='', $onlyJS=false)
	{
		$this->pagerenderer = t3lib_div::makeInstance('tx_imagecarousel_pagerenderer');
		$this->pagerenderer->setConf($this->conf);

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
		$this->pagerenderer->addJsFile($this->conf['jQueryCloudCarousel']);

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

		// checks if t3jquery is loaded
		if (T3JQUERY === TRUE) {
			tx_t3jquery::addJqJS();
			if ($this->conf['mouseWheel'] && t3lib_div::int_from_ver($this->pagerenderer->getExtensionVersion('t3jquery')) <= 1010003) {
				$this->pagerenderer->addJsFile($this->conf['jQueryMouseWheel']);
			}
		} else {
			if ($this->conf['mouseWheel']) {
				$this->pagerenderer->addJsFile($this->conf['jQueryMouseWheel']);
			}
		}

		$this->pagerenderer->addJS(
$jQueryNoConflict . "
jQuery(document).ready(function() {
	jQuery('#{$this->getContentKey()}').CloudCarousel(".(count($options) ? "{\n		".implode(",\n		", $options)."\n	}" : "").");
});");
		$this->pagerenderer->addCSS("
#{$this->getContentKey()} {
	width: {$this->conf['carouselwidth']}px;
	height: {$this->conf['carouselheight']}px;
	overflow: scroll;
	display: hidden
}");

		// Add the ressources
		$this->pagerenderer->addResources();

		if ($onlyJS === true) {
			return true;
		}

		$return_string = NULL;
		$images = NULL;
		$descriptions = NULL;
		$navigation = NULL;
		$markerArray = array(
			'BUTTON_LEFT'  => NULL,
			'BUTTON_RIGHT' => NULL,
			'TITLE_BOX'    => NULL,
			'ALT_BOX'      => NULL,
			'DESCRIPTIONS' => NULL,
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
				$descriptions .= trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['description'], $this->conf['carousel.'][$this->type.'.']['description.']));
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
			$markerArray['DESCRIPTIONS'] = $this->cObj->stdWrap($descriptions, $this->conf['carousel.'][$this->type.'.']['descriptionWrap.']);

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