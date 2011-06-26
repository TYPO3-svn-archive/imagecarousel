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

require_once(PATH_tslib.'class.tslib_pibase.php');

if (t3lib_extMgm::isLoaded('t3jquery')) {
	require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
}

/**
 * Plugin 'Image carousel' for the 'imagecarousel' extension.
 *
 * @author	Juergen Furrer <juergen.furrer@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_imagecarousel
 */
class tx_imagecarousel_pi1 extends tslib_pibase
{
	public $prefixId      = 'tx_imagecarousel_pi1';
	public $scriptRelPath = 'pi1/class.tx_imagecarousel_pi1.php';
	public $extKey        = 'imagecarousel';
	public $pi_checkCHash = true;
	public $lConf = array();
	public $templatePart = null;
	public $contentKey = null;
	public $jsFiles = array();
	public $js = array();
	public $cssFiles = array();
	public $css = array();
	public $images = array();
	public $hrefs = array();
	public $captions = array();
	public $description = array();
	public $imageDir = 'uploads/tx_imagecarousel/';
	public $type = 'normal';

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

		if ($this->cObj->data['list_type'] == $this->extKey.'_pi1') {
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
						);
					}
				}
			}

			$this->lConf['skin']               = $this->getFlexformData('control', 'skin');
			$this->lConf['vertical']           = $this->getFlexformData('control', 'vertical');
			$this->lConf['rtl']                = $this->getFlexformData('control', 'rtl');
			$this->lConf['random']             = $this->getFlexformData('control', 'random');
			$this->lConf['externalcontrol']    = $this->getFlexformData('control', 'externalcontrol');
			$this->lConf['hidenextbutton']     = $this->getFlexformData('control', 'hidenextbutton');
			$this->lConf['hidepreviousbutton'] = $this->getFlexformData('control', 'hidepreviousbutton');
			$this->lConf['imagewidth']         = $this->getFlexformData('control', 'imagewidth');
			$this->lConf['imageheight']        = $this->getFlexformData('control', 'imageheight');
			$this->lConf['carouselwidth']      = $this->getFlexformData('control', 'carouselwidth');
			$this->lConf['carouselheight']     = $this->getFlexformData('control', 'carouselheight');

			$this->lConf['showCaption']        = $this->getFlexformData('captions', 'showCaption');
			$this->lConf['animation']          = $this->getFlexformData('captions', 'animation');
			$this->lConf['position']           = $this->getFlexformData('captions', 'position');
			$this->lConf['speedOver']          = $this->getFlexformData('captions', 'speedOver');
			$this->lConf['speedOut']           = $this->getFlexformData('captions', 'speedOut');
			$this->lConf['hideDelay']          = $this->getFlexformData('captions', 'hideDelay');
			$this->lConf['spanWidth']          = $this->getFlexformData('captions', 'spanWidth');

			$this->lConf['auto']               = $this->getFlexformData('movement', 'auto');
			$this->lConf['stoponmouseover']    = $this->getFlexformData('movement', 'stoponmouseover');
			$this->lConf['transition']         = $this->getFlexformData('movement', 'transition');
			$this->lConf['transitiondir']      = $this->getFlexformData('movement', 'transitiondir');
			$this->lConf['transitionduration'] = $this->getFlexformData('movement', 'transitionduration');
			$this->lConf['scroll']             = $this->getFlexformData('movement', 'scroll');
			$this->lConf['wrap']               = $this->getFlexformData('movement', 'wrap');

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
					$this->setDataDam(false);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(true);
					break;
				}
			}

			// overwrite the config
			if ($this->lConf['skin']) {
				$this->conf['skin'] = $this->lConf['skin'];
			}
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
			if ($this->lConf['auto'] > 0) {
				$this->conf['auto'] = $this->lConf['auto'];
			}
			if ($this->lConf['transition']) {
				$this->conf['transition'] = $this->lConf['transition'];
			}
			if ($this->lConf['transitiondir']) {
				$this->conf['transitiondir'] = $this->lConf['transitiondir'];
			}
			if ($this->lConf['transitionduration'] > 0) {
				$this->conf['transitionduration'] = $this->lConf['transitionduration'];
			}
			if ($this->lConf['wrap']) {
				$this->conf['movewrap'] = $this->lConf['wrap'];
			}
			if ($this->lConf['scroll'] > 0) {
				$this->conf['scroll'] = $this->lConf['scroll'];
			}
			// Will be overridden, if not "from TS"
			if ($this->lConf['random'] < 2) {
				$this->conf['random'] = $this->lConf['random'];
			}
			if ($this->lConf['vertical'] < 2) {
				$this->conf['vertical'] = $this->lConf['vertical'];
			}
			if ($this->lConf['rtl'] < 2) {
				$this->conf['rtl'] = $this->lConf['rtl'];
			}
			if ($this->lConf['stoponmouseover'] < 2) {
				$this->conf['stoponmouseover'] = $this->lConf['stoponmouseover'];
			}
			if ($this->lConf['externalcontrol'] < 2) {
				$this->conf['externalcontrol'] = $this->lConf['externalcontrol'];
			}
			if ($this->lConf['hidenextbutton'] < 2) {
				$this->conf['hidenextbutton'] = $this->lConf['hidenextbutton'];
			}
			if ($this->lConf['hidepreviousbutton'] < 2) {
				$this->conf['hidepreviousbutton'] = $this->lConf['hidepreviousbutton'];
			}
			// Caption
			if ($this->lConf['showCaption'] < 2) {
				$this->conf['showCaption'] = $this->lConf['showCaption'];
			}
			if ($this->lConf['animation']) {
				$this->conf['animation'] = $this->lConf['animation'];
			}
			if ($this->lConf['position']) {
				$this->conf['position'] = $this->lConf['position'];
			}
			if ($this->lConf['speedOver']) {
				$this->conf['speedOver'] = $this->lConf['speedOver'];
			}
			if ($this->lConf['speedOut']) {
				$this->conf['speedOut'] = $this->lConf['speedOut'];
			}
			if ($this->lConf['hideDelay']) {
				$this->conf['hideDelay'] = $this->lConf['hideDelay'];
			}
			if ($this->lConf['spanWidth']) {
				$this->conf['spanWidth'] = $this->lConf['spanWidth'];
			}

			return $this->parseTemplate();
		}
	}

	/**
	 * Set the contentKey
	 * 
	 * @param string $contentKey
	 */
	public function setContentKey($contentKey=null)
	{
		$this->contentKey = ($contentKey == null ? $this->extKey : $contentKey);
	}

	/**
	 * Get the contentKey
	 * 
	 * @return string
	 */
	public function getContentKey()
	{
		return $this->contentKey;
	}

	/**
	 * Set the Information of the images if mode = upload
	 * 
	 * @return boolean
	 */
	protected function setDataUpload()
	{
		if ($this->lConf['images']) {
			// define the images
			if ($this->lConf['images']) {
				$this->images = t3lib_div::trimExplode(',', $this->lConf['images']);
			}
			// define the hrefs
			if ($this->lConf['hrefs']) {
				$this->hrefs = t3lib_div::trimExplode(chr(10), $this->lConf['hrefs']);
			}
			// define the captions
			if ($this->lConf['captions']) {
				$this->captions = t3lib_div::trimExplode(chr(10), $this->lConf['captions']);
			}
			return true;
		}
		return false;
	}

	/**
	 * Set the information of the images if mode = uploadRTE
	 */
	protected function setDataUploadRTE()
	{
		if (count($this->lConf['imagesRTE']) > 0) {
			foreach ($this->lConf['imagesRTE'] as $key => $image) {
				$this->images[]      = $image['image'];
				$this->hrefs[]       = $image['href'];
				$this->captions[]    = $image['caption'];
				$this->description[] = $image['description'];
			}
		}
	}

	/**
	 * Set the Information of the images if mode = dam
	 * 
	 * @param boolean $fromCategory
	 * @return boolean
	 */
	protected function setDataDam($fromCategory=false)
	{
		// clear the imageDir
		$this->imageDir = '';
		// get all fields for captions
		$damCaptionFields = t3lib_div::trimExplode(',', $this->conf['damCaptionFields'], true);
		$damDescFields    = t3lib_div::trimExplode(',', $this->conf['damDescFields'], true);
		$damHrefFields    = t3lib_div::trimExplode(',', $this->conf['damHrefFields'], true);
		$fields  = (count($damCaptionFields) > 0 ? ','.implode(',tx_dam.', $damCaptionFields) : '');
		$fields .= (count($damDescFields) > 0    ? ','.implode(',tx_dam.', $damDescFields)    : '');
		$fields .= (count($damHrefFields) > 0    ? ','.implode(',tx_dam.', $damHrefFields)    : '');
		if ($fromCategory === true) {
			// Get the images from dam category
			$damcategories = $this->getDamcats($this->lConf['damcategories']);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				tx_dam_db::getMetaInfoFieldList() . $fields,
				'tx_dam',
				'tx_dam_mm_cat',
				'tx_dam_cat',
				" AND tx_dam_cat.uid IN (".implode(",", $damcategories).") AND tx_dam.file_mime_type='image' AND tx_dam.sys_language_uid=" . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->sys_language_uid, 'tx_dam'),
				'',
				'tx_dam.sorting',
				''
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$images['rows'][] = $row;
			}
		} else {
			// Get the images from dam
			$images = tx_dam_db::getReferencedFiles(
				'tt_content',
				$this->cObj->data['uid'],
				'imagecarousel',
				'tx_dam_mm_ref',
				tx_dam_db::getMetaInfoFieldList() . $fields,
				"tx_dam.file_mime_type = 'image'"
			);
		}
		if (count($images['rows']) > 0) {
			// overlay the translation
			$conf = array(
				'sys_language_uid' => $this->sys_language_uid,
				'lovl_mode' => ''
			);
			// add image
			foreach ($images['rows'] as $key => $row) {
				$row = tx_dam_db::getRecordOverlay('tx_dam', $row, $conf);
				// set the data
				$this->images[] = $row['file_path'].$row['file_name'];$
				// set the href
				$href = '';
				unset($href);
				if (count($damHrefFields) > 0) {
					foreach ($damHrefFields as $damHrefField) {
						if (! isset($href) && trim($row[$damHrefField])) {
							$href = $row[$damHrefField];
							break;
						}
					}
				}
				$this->hrefs[] = $href;
				// set the caption
				$caption = '';
				unset($caption);
				if (count($damCaptionFields) > 0) {
					foreach ($damCaptionFields as $damCaptionField) {
						if (! isset($caption) && trim($row[$damCaptionField])) {
							$caption = $row[$damCaptionField];
							break;
						}
					}
				}
				$this->captions[] = $caption;
				// set the description
				$description = '';
				unset($description);
				if (count($damDescFields) > 0) {
					foreach ($damDescFields as $damDescField) {
						if (! isset($description) && trim($row[$damDescField])) {
							$description = $row[$damDescField];
							break;
						}
					}
				}
				$this->description[] = $description;
			}
		}
		return true;
	}

	/**
	 * Return all DAM categories including subcategories
	 * 
	 * @param string $dam_cat
	 * @return	array
	 */
	protected function getDamcats($dam_cat='')
	{
		$damCats = t3lib_div::trimExplode(",", $dam_cat, true);
		if (count($damCats) < 1) {
			return array();
		} else {
			// select subcategories
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, parent_id',
				'tx_dam_cat',
				'parent_id IN ('.implode(",", $damCats).') '.$this->cObj->enableFields('tx_dam_cat'),
				'',
				'parent_id',
				''
			);
			$subcats = array();
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$damCats[] = $row['uid'];
			}
		}
		return $damCats;
	}

	/**
	 * Parse all images into the template
	 * 
	 * @param string $dir
	 * @param boolean $onlyJS
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

		// add CSS file for skin
		$skin_class = null;
		if ($this->conf['skin']) {
			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagecarousel']);
			$this->addCssFile("{$confArr['skinFolder']}/{$this->conf['skin']}/skin.css");
			$skin_class = "jcarousel-skin-{$this->conf['skin']}";
		}

		// define the js files
		$this->addJsFile($this->conf['jQueryCarousel']);
		$this->addJsFile($this->conf['jsScript']);

		// get the options from config
		$options = array();
		$options[] = "size: ".count($this->images);
		if ($this->conf['vertical']) {
			// turn off the externalcontrol
			$this->conf['externalcontrol'] = 0;
			$options[] = "vertical: true";
		}
		if ($this->conf['auto'] > 0) {
			$options[] = "auto: ".($this->conf['auto']/1000);
		}
		if (in_array($this->conf['transition'], array('linear', 'swing'))) {
			$options[] = "easing: '{$this->conf['transition']}'";
		} elseif ($this->conf['transitiondir'] && $this->conf['transition']) {
			$options[] = "easing: 'ease{$this->conf['transitiondir']}{$this->conf['transition']}'";
		}
		if ($this->conf['transitionduration'] > 0) {
			$options[] = "animation: {$this->conf['transitionduration']}";
		}
		if (in_array($this->conf['movewrap'], array("first", "last", "both", "circular"))) {
			if (! ($this->conf['externalcontrol'] && $this->conf['movewrap'] == "circular")) {
				$options[] = "wrap: '{$this->conf['movewrap']}'";
			}
		}
		if ($this->conf['rtl']) {
			$options[] = "rtl: true";
		}
		if ($this->conf['scroll'] > 0) {
			if ($this->conf['scroll'] > count($this->images)) {
				$this->conf['scroll'] = count($this->images);
			}
			$options[] = "scroll: {$this->conf['scroll']}";
		}
		if (! $this->conf['externalcontrol']) {
			if ($this->conf['movewrap'] == "circular") {
				$options[] = "itemVisibleInCallback:  {onBeforeAnimation: imagecarousel.itemVisibleInCallback}";
				$options[] = "itemVisibleOutCallback: {onAfterAnimation:  imagecarousel.itemVisibleOutCallback}";
			}
		}
		// init Callback
		$initCallback[] = "imagecarousel.initCallback('#{$this->getContentKey()}',carousel,state)";
		if ($this->conf['stoponmouseover'] == 1) {
			$initCallback[] = "imagecarousel.initCallbackMouseover(carousel,state)";
		}
		$options[] = "initCallback: function(carousel,state){".implode(";", $initCallback).";}";
		// hide buttons
		if ($this->conf['hidenextbutton']) {
			$options[] = "buttonNextHTML: null";
		}
		if ($this->conf['hidepreviousbutton']) {
			$options[] = "buttonPrevHTML: null";
		}
		// fallback for childElem
		if (! $this->conf['carousel.'][$this->type.'.']['childElem']) {
			$this->conf['carousel.'][$this->type.'.']['childElem'] = 'li';
		}
		$random_script = null;
		if ($this->conf['random']) {
			$random_script = "\n	imagecarousel.randomize('#{$this->getContentKey()}','{$this->conf['carousel.'][$this->type.'.']['childElem']}');";
		}
		// caption
		$jQueryCaptify = null;
		if ($this->conf['showCaption']) {
			$captions = array();
			if ($this->conf['animation']) {
				$captions[] = "animation: " . t3lib_div::quoteJSvalue($this->conf['animation']);
			}
			if ($this->conf['position']) {
				$captions[] = "position: " . t3lib_div::quoteJSvalue($this->conf['position']);
			}
			if ($this->conf['speedOver']) {
				$captions[] = "speedOver: " . t3lib_div::quoteJSvalue($this->conf['speedOver']);
			}
			if ($this->conf['speedOut']) {
				$captions[] = "speedOut: " . t3lib_div::quoteJSvalue($this->conf['speedOut']);
			}
			if ($this->conf['hideDelay']) {
				$captions[] = "hideDelay: " . t3lib_div::quoteJSvalue($this->conf['hideDelay']);
			}
			if ($this->conf['prefix']) {
				$captions[] = "prefix: " . t3lib_div::quoteJSvalue($this->conf['prefix']);
			}
			if ($this->conf['opacity']) {
				$captions[] = "opacity: " . t3lib_div::quoteJSvalue($this->conf['opacity']);
			}
			if ($this->conf['className']) {
				$captions[] = "className: " . t3lib_div::quoteJSvalue($this->conf['className']);
			}
			if ($this->conf['spanWidth']) {
				$captions[] = "spanWidth: " . t3lib_div::quoteJSvalue($this->conf['spanWidth']);
			}
			$this->addJsFile($this->conf['jQueryCaptify']);
			$jQueryCaptify = "\n	jQuery('#{$this->getContentKey()} img.captify').captify(".(count($captions) ? "{\n		".implode(",\n		", $captions)."\n	}" : "").");";
		}

		$this->addJS(
$jQueryNoConflict . "
jQuery(document).ready(function() { {$random_script}
	jQuery('#{$this->getContentKey()}-outer').css('display', 'block');
	jQuery('#{$this->getContentKey()}').jcarousel(".(count($options) ? "{\n		".implode(",\n		", $options)."\n	}" : "").");{$jQueryCaptify}
});
");

		if (is_numeric($this->conf['carouselwidth'])) {
			$this->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-horizontal,
#c{$this->cObj->data['uid']} .jcarousel-container-horizontal {
	width: {$this->conf['carouselwidth']}px;".($this->conf['carouselheight'] ? "\n	height: {$this->conf['carouselheight']}px;" : "")."
}
");
		}
		if (is_numeric($this->conf['carouselheight'])) {
			$this->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-vertical,
#c{$this->cObj->data['uid']} .jcarousel-container-vertical {
	height: {$this->conf['carouselheight']}px;".($this->conf['carouselwidth'] ? "\n	width: {$this->conf['carouselwidth']}px;" : "")."
}
");
		}
		$this->addCSS("
#{$this->getContentKey()}-outer {
	display: none;
}
#c{$this->cObj->data['uid']} .jcarousel-item {
	width: {$css_width};
	height: {$css_height};
}");

		// Add the ressources
		$this->addResources();

		if ($onlyJS === true) {
			return true;
		}

		$return_string = null;
		$images = null;
		$navigation = null;
		$markerArray = array();
		$GLOBALS['TSFE']->register['key'] = $this->getContentKey();
		$GLOBALS['TSFE']->register['class'] = $skin_class;
		$GLOBALS['TSFE']->register['imagewidth']  = $this->conf['imagewidth'];
		$GLOBALS['TSFE']->register['imageheight'] = $this->conf['imageheight'];
		$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = 0;
		if (count($this->images) > 0) {
			foreach ($this->images as $key => $image_name) {
				$image = null;
				$imgConf = $this->conf['carousel.'][$this->type.'.']['image.'];
				$totalImagePath = $this->imageDir . $image_name;
				$GLOBALS['TSFE']->register['file']        = $totalImagePath;
				$GLOBALS['TSFE']->register['href']        = $this->hrefs[$key];
				$GLOBALS['TSFE']->register['caption']     = $this->captions[$key];
				$GLOBALS['TSFE']->register['description'] = $this->description[$key];
				$GLOBALS['TSFE']->register['CURRENT_ID']  = $GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] + 1;
				if ($this->hrefs[$key]) {
					$imgConf['imageLinkWrap.'] = $imgConf['imageHrefWrap.'];
				}
				$image = $this->cObj->IMAGE($imgConf);
				$images .= $this->cObj->typolink($image, $imgConf['imageLinkWrap.']);
				// create the navigation
				if ($this->conf['externalcontrol']) {
					$navigation .= trim($this->cObj->cObjGetSingle($this->conf['carousel.'][$this->type.'.']['navigation'], $this->conf['carousel.'][$this->type.'.']['navigation.']));
				}
				$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] ++;
			}
			$markerArray['NAVIGATION'] = $this->cObj->stdWrap($navigation, $this->conf['carousel.'][$this->type.'.']['navigationWrap.']);
			// the stdWrap
			$images = $this->cObj->stdWrap($images, $this->conf['carousel.'][$this->type.'.']['stdWrap.']);
			$return_string = $this->cObj->substituteMarkerArray($images, $markerArray, '###|###', 0);
		}
		return $this->pi_wrapInBaseClass($return_string);
	}

	/**
	 * Include all defined resources (JS / CSS)
	 *
	 * @return void
	 */
	public function addResources()
	{
		// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} else {
			$this->addJsFile($this->conf['jQueryLibrary'], true);
			$this->addJsFile($this->conf['jQueryEasing']);
		}
		if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
		}
		// Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		if ($GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter']) {
			$allJsInFooter = true;
		} else {
			$allJsInFooter = false;
		}
		// add all defined JS files
		if (count($this->jsFiles) > 0) {
			foreach ($this->jsFiles as $jsToLoad) {
				if (T3JQUERY === true) {
					$conf = array(
						'jsfile' => $jsToLoad,
						'tofooter' => ($this->conf['jsInFooter'] || $allJsInFooter),
						'jsminify' => $this->conf['jsMinify'],
					);
					tx_t3jquery::addJS('', $conf);
				} else {
					$file = $this->getPath($jsToLoad);
					if ($file) {
						if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
							if ($allJsInFooter) {
								$pagerender->addJsFooterFile($file, 'text/javascript', $this->conf['jsMinify']);
							} else {
								$pagerender->addJsFile($file, 'text/javascript', $this->conf['jsMinify']);
							}
						} else {
							$temp_file = '<script type="text/javascript" src="'.$file.'"></script>';
							if ($allJsInFooter) {
								$GLOBALS['TSFE']->additionalFooterData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							} else {
								$GLOBALS['TSFE']->additionalHeaderData['jsFile_'.$this->extKey.'_'.$file] = $temp_file;
							}
						}
					} else {
						t3lib_div::devLog("'{$jsToLoad}' does not exists!", $this->extKey, 2);
					}
				}
			}
		}
		// add all defined JS script
		if (count($this->js) > 0) {
			foreach ($this->js as $jsToPut) {
				$temp_js .= $jsToPut;
			}
			$conf = array();
			$conf['jsdata'] = $temp_js;
			if (T3JQUERY === true && t3lib_div::int_from_ver($this->getExtensionVersion('t3jquery')) >= 1002000) {
				$conf['tofooter'] = ($this->conf['jsInFooter'] || $allJsInFooter);
				$conf['jsminify'] = $this->conf['jsMinify'];
				$conf['jsinline'] = $this->conf['jsInline'];
				tx_t3jquery::addJS('', $conf);
			} else {
				// Add script only once
				$hash = md5($temp_js);
				if ($this->conf['jsInline']) {
					$GLOBALS['TSFE']->inlineJS[$hash] = $temp_css;
				} elseif (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$pagerender->addJsFooterInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					} else {
						$pagerender->addJsInlineCode($hash, $temp_js, $this->conf['jsMinify']);
					}
				} else {
					if ($this->conf['jsMinify']) {
						$temp_js = t3lib_div::minifyJavaScript($temp_js);
					}
					if ($this->conf['jsInFooter'] || $allJsInFooter) {
						$GLOBALS['TSFE']->additionalFooterData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['js_'.$this->extKey.'_'.$hash] = t3lib_div::wrapJS($temp_js, true);
					}
				}
			}
		}
		// add all defined CSS files
		if (count($this->cssFiles) > 0) {
			foreach ($this->cssFiles as $cssToLoad) {
				// Add script only once
				$file = $this->getPath($cssToLoad);
				if ($file) {
					if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
						$pagerender->addCssFile($file, 'stylesheet', 'all', '', $this->conf['cssMinify']);
					} else {
						$GLOBALS['TSFE']->additionalHeaderData['cssFile_'.$this->extKey.'_'.$file] = '<link rel="stylesheet" type="text/css" href="'.$file.'" media="all" />'.chr(10);
					}
				} else {
					t3lib_div::devLog("'{$cssToLoad}' does not exists!", $this->extKey, 2);
				}
			}
		}
		// add all defined CSS Script
		if (count($this->css) > 0) {
			foreach ($this->css as $cssToPut) {
				$temp_css .= $cssToPut;
			}
			$hash = md5($temp_css);
			if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
				$pagerender->addCssInlineBlock($hash, $temp_css, $this->conf['cssMinify']);
			} else {
				// addCssInlineBlock
				$GLOBALS['TSFE']->additionalCSS['css_'.$this->extKey.'_'.$hash] .= $temp_css;
			}
		}
	}

	/**
	 * Return the webbased path
	 * 
	 * @param string $path
	 * @return string
	 */
	protected function getPath($path="")
	{
		return $GLOBALS['TSFE']->tmpl->getFileName($path);
	}

	/**
	 * Add additional JS file
	 * 
	 * @param string $script
	 * @param boolean $first
	 * @return void
	 */
	protected function addJsFile($script="", $first=false)
	{
		$script = t3lib_div::fixWindowsFilePath($script);
		if ($this->getPath($script) && ! in_array($script, $this->jsFiles)) {
			if ($first === true) {
				$this->jsFiles = array_merge(array($script), $this->jsFiles);
			} else {
				$this->jsFiles[] = $script;
			}
		}
	}

	/**
	 * Add JS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addJS($script="")
	{
		if (! in_array($script, $this->js)) {
			$this->js[] = $script;
		}
	}

	/**
	 * Add additional CSS file
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addCssFile($script="")
	{
		$script = t3lib_div::fixWindowsFilePath($script);
		if ($this->getPath($script) && ! in_array($script, $this->cssFiles)) {
			$this->cssFiles[] = $script;
		}
	}

	/**
	 * Add CSS to header
	 * 
	 * @param string $script
	 * @return void
	 */
	protected function addCSS($script="")
	{
		if (! in_array($script, $this->css)) {
			$this->css[] = $script;
		}
	}

	/**
	 * Returns the version of an extension (in 4.4 its possible to this with t3lib_extMgm::getExtensionVersion)
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function getExtensionVersion($key)
	{
		if (! t3lib_extMgm::isLoaded($key)) {
			return '';
		}
		$_EXTKEY = $key;
		include(t3lib_extMgm::extPath($key) . 'ext_emconf.php');
		return $EM_CONF[$key]['version'];
	}

	/**
	 * Extract the requested information from flexform
	 * 
	 * @param string $sheet
	 * @param string $name
	 * @param boolean $devlog
	 * @return string
	 */
	protected function getFlexformData($sheet='', $name='', $devlog=true)
	{
		$this->pi_initPIflexForm();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		if (! isset($piFlexForm['data'])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform Data not set", $this->extKey, 1);
			}
			return null;
		}
		if (! isset($piFlexForm['data'][$sheet])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform sheet '{$sheet}' not defined", $this->extKey, 1);
			}
			return null;
		}
		if (! isset($piFlexForm['data'][$sheet]['lDEF'][$name])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform Data [{$sheet}][{$name}] does not exist", $this->extKey, 1);
			}
			return null;
		}
		if (isset($piFlexForm['data'][$sheet]['lDEF'][$name]['vDEF'])) {
			return $this->pi_getFFvalue($piFlexForm, $name, $sheet);
		} else {
			return $piFlexForm['data'][$sheet]['lDEF'][$name];
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php']);
}

?>