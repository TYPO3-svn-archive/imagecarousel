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
require_once(t3lib_extMgm::extPath('imagecarousel').'lib/class.tx_imagecarousel_pagerenderer.php');

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
	public $images = array();
	public $hrefs = array();
	public $captions = array();
	public $description = array();
	protected $lConf = array();
	protected $templatePart = null;
	protected $contentKey = null;
	protected $piFlexForm = array();
	protected $imageDir = 'uploads/tx_imagecarousel/';
	protected $type = 'normal';
	protected $pagerenderer = NULL;

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

		// set the system language
		$this->sys_language_uid = $GLOBALS['TSFE']->sys_language_content;

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
					$this->setDataDam(false, 'tt_content', $this->cObj->data['uid']);
					break;
				}
				case "dam_catedit" : {
					$this->setDataDam(true, 'tt_content', $this->cObj->data['uid']);
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
		} else {
			$this->type = 'header';
			// It's the header
			$used_page = array();
			$pageID    = false;
			$skinChanged = false;
			foreach ($GLOBALS['TSFE']->rootLine as $page) {
				if (! $pageID) {
					if ($skinChanged === false && trim($page['tx_imagecarousel_skin']) && ! $this->conf['disableRecursion']) {
						$this->conf['skin'] = $page['tx_imagecarousel_skin'];
						$skinChanged = true;
					}
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
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_imagecarousel_images, tx_imagecarousel_hrefs, tx_imagecarousel_captions, tx_imagecarousel_skin','pages_language_overlay','pid='.intval($pageID).' AND sys_language_uid='.$this->sys_language_uid,'','',1);
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if (trim($used_page['tx_imagecarousel_skin'])) {
						$this->conf['skin'] = $row['tx_imagecarousel_skin'];
					}
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
	protected function setDataDam($fromCategory=false, $table='tt_content', $uid=0)
	{
		// clear the imageDir
		$this->imageDir = '';
		// get all fields for captions
		$damCaptionFields = t3lib_div::trimExplode(',', $this->conf['damCaptionFields'], true);
		$damDescFields    = t3lib_div::trimExplode(',', $this->conf['damDescFields'], true);
		$damHrefFields    = t3lib_div::trimExplode(',', $this->conf['damHrefFields'], true);
		$fieldsArray = array_merge(
			$damCaptionFields,
			$damDescFields,
			$damHrefFields
		);
		$fields = NULL;
		if (count($fieldsArray) > 0) {
			foreach ($fieldsArray as $field) {
				$fields .= ',tx_dam.' . $field;
			}
		}
		if ($fromCategory === true) {
			// Get the images from dam category
			$damcategories = $this->getDamcats($this->lConf['damcategories']);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				tx_dam_db::getMetaInfoFieldList() . $fields,
				'tx_dam',
				'tx_dam_mm_cat',
				'tx_dam_cat',
				" AND tx_dam_cat.uid IN (".implode(",", $damcategories).")",
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
				$table,
				$uid,
				'imagecarousel',
				'tx_dam_mm_ref',
				tx_dam_db::getMetaInfoFieldList() . $fields,
				'',
				'',
				'tx_dam_mm_ref.sorting_foreign'
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
				$absFileName = t3lib_div::getFileAbsFileName($row['file_path'] . $row['file_name']);
				$size = @getimagesize($absFileName);
				if (preg_match("/^image\//i", $size['mime'])) {
					// set the data
					$this->images[] = $row['file_path'] . $row['file_name'];$
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
						if (isset($this->conf['damCaptionObject'])) {
							foreach ($damCaptionFields as $damCaptionField) {
								if (isset($row[$damCaptionField])) {
									$GLOBALS['TSFE']->register['dam_'.$damCaptionField] = $row[$damCaptionField];
								}
							}
							$caption = trim($this->cObj->cObjGetSingle($this->conf['damCaptionObject'], $this->conf['damCaptionObject.']));
							// Unset the registered values
							foreach ($damCaptionFields as $damCaptionField) {
								unset($GLOBALS['TSFE']->register['dam_'.$damCaptionField]);
							}
						} else {
							// the old way
							foreach ($damCaptionFields as $damCaptionField) {
								if (! isset($caption) && trim($row[$damCaptionField])) {
									$caption = $row[$damCaptionField];
									break;
								}
							}
						}
					}
					$this->captions[] = $caption;

					// set the description
					$description = '';
					unset($description);
					if (count($damDescFields) > 0) {
						if (isset($this->conf['damDescObject'])) {
							foreach ($damDescFields as $damDescField) {
								if (isset($row[$damDescField])) {
									$GLOBALS['TSFE']->register['dam_'.$damDescField] = $row[$damDescField];
								}
							}
							$description = trim($this->cObj->cObjGetSingle($this->conf['damDescObject'], $this->conf['damDescObject.']));
							// Unset the registered values
							foreach ($damDescFields as $damDescField) {
								unset($GLOBALS['TSFE']->register['dam_'.$damDescField]);
							}
						} else {
							// the old way
							foreach ($damDescFields as $damDescField) {
								if (! isset($description) && trim($row[$damDescField])) {
									$description = $row[$damDescField];
									break;
								}
							}
						}
					}
					$this->description[] = $description;
				}
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

		// add CSS file for skin
		$skin_class = null;
		if ($this->conf['skin']) {
			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagecarousel']);
			$this->pagerenderer->addCssFile("{$confArr['skinFolder']}/{$this->conf['skin']}/skin.css");
			$skin_class = "jcarousel-skin-{$this->conf['skin']}";
		}

		// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} else {
			$this->pagerenderer->addJsFile($this->conf['jQueryLibrary'], true);
			$this->pagerenderer->addJsFile($this->conf['jQueryEasing']);
		}

		// define the js files
		$this->pagerenderer->addJsFile($this->conf['jQueryCarousel']);
		$this->pagerenderer->addJsFile($this->conf['jsScript']);

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
			$this->pagerenderer->addJsFile($this->conf['jQueryCaptify']);
			$jQueryCaptify = "\n	jQuery('#{$this->getContentKey()} img.captify').captify(".(count($captions) ? "{\n		".implode(",\n		", $captions)."\n	}" : "").");";
		}

		$this->pagerenderer->addJS(
$jQueryNoConflict . "
jQuery(document).ready(function() { {$random_script}
	jQuery('#{$this->getContentKey()}-outer').css('display', 'block');
	jQuery('#{$this->getContentKey()}').jcarousel(".(count($options) ? "{\n		".implode(",\n		", $options)."\n	}" : "").");{$jQueryCaptify}
});
");

		if (is_numeric($this->conf['carouselwidth'])) {
			$this->pagerenderer->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-horizontal,
#c{$this->cObj->data['uid']} .jcarousel-container-horizontal {
	width: {$this->conf['carouselwidth']}px;".($this->conf['carouselheight'] ? "\n	height: {$this->conf['carouselheight']}px;" : "")."
}
");
		}
		if (is_numeric($this->conf['carouselheight'])) {
			$this->pagerenderer->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-vertical,
#c{$this->cObj->data['uid']} .jcarousel-container-vertical {
	height: {$this->conf['carouselheight']}px;".($this->conf['carouselwidth'] ? "\n	width: {$this->conf['carouselwidth']}px;" : "")."
}
");
		}
		$this->pagerenderer->addCSS("
#{$this->getContentKey()}-outer {
	display: none;
}
#c{$this->cObj->data['uid']} .jcarousel-item {
	width: {$css_width};
	height: {$css_height};
}");

		// Add the ressources
		$this->pagerenderer->addResources();

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
	* Set the piFlexform data
	*
	* @return void
	*/
	protected function setFlexFormData()
	{
		if (! count($this->piFlexForm)) {
			$this->pi_initPIflexForm();
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
		}
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
		$this->setFlexFormData();
		if (! isset($this->piFlexForm['data'])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform Data not set", $this->extKey, 1);
			}
			return null;
		}
		if (! isset($this->piFlexForm['data'][$sheet])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform sheet '{$sheet}' not defined", $this->extKey, 1);
			}
			return null;
		}
		if (! isset($this->piFlexForm['data'][$sheet]['lDEF'][$name])) {
			if ($devlog === true) {
				t3lib_div::devLog("Flexform Data [{$sheet}][{$name}] does not exist", $this->extKey, 1);
			}
			return null;
		}
		if (isset($this->piFlexForm['data'][$sheet]['lDEF'][$name]['vDEF'])) {
			return $this->pi_getFFvalue($this->piFlexForm, $name, $sheet);
		} else {
			return $this->piFlexForm['data'][$sheet]['lDEF'][$name];
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php']);
}

?>