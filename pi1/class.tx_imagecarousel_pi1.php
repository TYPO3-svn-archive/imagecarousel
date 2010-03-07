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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

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
class tx_imagecarousel_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_imagecarousel_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_imagecarousel_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'imagecarousel';	// The extension key.
	var $pi_checkCHash = true;
	var $lConf = array();
	var $templatePart = null;
	var $contentKey = null;
	var $jsFiles = array();
	var $js = array();
	var $cssFiles = array();
	var $css = array();
	var $images = array();
	var $hrefs = array();
	var $captions = array();
	var $imageDir = 'uploads/tx_imagecarousel/';
	var $type = 'normal';

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// define the key of the element
		$this->contentKey = "imagecarousel";

		$pageID = false;
		if ($this->cObj->data['list_type'] == $this->extKey.'_pi1') {
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
			$this->contentKey .= "_c" . $this->cObj->data['uid'];

			// define th images
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
			// overwrite the config
			if ($this->lConf['skin']) {
				$this->conf['skin'] = $this->lConf['skin'];
			}
			// 
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
			// 
			if ($this->lConf['auto'] > 0) {
				$this->conf['auto'] = $this->lConf['auto'];
			}
			// 
			if ($this->lConf['transition']) {
				$this->conf['transition'] = $this->lConf['transition'];
			}
			// 
			if ($this->lConf['transitiondir']) {
				$this->conf['transitiondir'] = $this->lConf['transitiondir'];
			}
			// 
			if ($this->lConf['transitionduration'] > 0) {
				$this->conf['transitionduration'] = $this->lConf['transitionduration'];
			}
			// 
			if ($this->lConf['wrap']) {
				$this->conf['movewrap'] = $this->lConf['wrap'];
			}
			// 
			if ($this->lConf['scroll'] > 0) {
				$this->conf['scroll'] = $this->lConf['scroll'];
			}
			// 
			$this->conf['vertical'] = $this->lConf['vertical'];
			$this->conf['stoponmouseover'] = $this->lConf['stoponmouseover'];

			return $this->pi_wrapInBaseClass($this->parseTemplate());
		}
	}


	/**
	 * Parse all images into the template
	 * @param $data
	 * @return string
	 */
	function parseTemplate($dir='', $onlyJS=false)
	{
		// define the directory of images
		if ($dir == '') {
			$dir = $this->imageDir;
		}

		// define the contentKey if not exist
		if ($this->contentKey == '') {
			$this->contentKey = "imagecarousel_key";
		}

		// add CSS file for skin
		if ($this->conf['skin']) {
			$this->addCssFile("{$this->conf['skinFolder']}/{$this->conf['skin']}/skin.css");
			$skin_class = "jcarousel-skin-{$this->conf['skin']}";
		}

		// define the jQuery mode and function
		if ($this->conf['jQueryNoConflict']) {
			$jQueryNoConflict = "jQuery.noConflict();";
		} else {
			$jQueryNoConflict = "";
		}

		// define the js files
		$this->addJsFile($this->conf['jQueryCarousel']);
		$this->addJsFile("EXT:imagecarousel/res/jquery/js/imagecarousel.js");

		// get the options from config
		$options = array();
		$options[] = "size: ".count($this->images);
		if ($this->conf['vertical']) {
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
			$options[] = "wrap: '{$this->conf['movewrap']}'";
		}
		if ($this->conf['scroll'] > 0) {
			if ($this->conf['scroll'] > count($this->images)) {
				$this->conf['scroll'] = count($this->images);
			}
			$options[] = "scroll: {$this->conf['scroll']}";
		}
		if ($this->conf['movewrap'] == "circular") {
			$options[] = "itemVisibleInCallback:  {onBeforeAnimation: imagecarousel.itemVisibleInCallback}";
			$options[] = "itemVisibleOutCallback: {onAfterAnimation:  imagecarousel.itemVisibleOutCallback}";
		}
		if ($this->conf['stoponmouseover'] == 1) {
			$options[] = "initCallback: imagecarousel.initCallbackMouseover";
		} else {
			$options[] = "initCallback: imagecarousel.initCallback";
		}

		$this->addJS(
$jQueryNoConflict . "
jQuery(document).ready(function() {
	jQuery('#{$this->contentKey}').jcarousel(".(count($options) ? "{\n		".implode(",\n		", $options)."\n	}" : "").");
});");

		if (is_numeric($this->conf['carouselwidth'])) {
			$this->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-horizontal {
	width: {$this->conf['carouselwidth']}px;
}
#c{$this->cObj->data['uid']} .jcarousel-container-horizontal {
	width: {$this->conf['carouselwidth']}px;
}");
		}
		if (is_numeric($this->conf['carouselheight'])) {
			$this->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-clip-vartical {
	height: {$this->conf['carouselheight']}px;
}
#c{$this->cObj->data['uid']} .jcarousel-container-vartical {
	height: {$this->conf['carouselheight']}px;
}");
		}

		preg_match("/^([0-9]*)/i", $this->conf['imagewidth'], $reg_width);
		preg_match("/^([0-9]*)/i", $this->conf['imageheight'], $reg_height);

		$this->addCSS("
#c{$this->cObj->data['uid']} .jcarousel-item {
	width: {$reg_width[1]}px;
	height: {$reg_height[1]}px;
}");

		// Add the ressources
		$this->addResources();

		if ($onlyJS === true) {
			return true;
		}

		$return_string = null;
		$images = null;
		$GLOBALS['TSFE']->register['key'] = $this->contentKey;
		$GLOBALS['TSFE']->register['class'] = $skin_class;
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
				if ($this->hrefs[$key]) {
					$imgConf['imageLinkWrap.'] = $imgConf['imageHrefWrap.'];
					$image = $this->cObj->IMAGE($imgConf);
				} else {
					$link = $this->cObj->imageLinkWrap('', $totalImagePath, $imgConf['imageLinkWrap.']);
					if ($link) {
						unset($imgConf['titleText']);
						unset($imgConf['titleText.']);
						$imgConf['emptyTitleHandling'] = 'removeAttr';
					}
					$image = $this->cObj->IMAGE($imgConf);
				}
				$images .= $this->cObj->typolink($image, $imgConf['imageLinkWrap.']);
				$GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] ++;
			}
			$return_string = $this->cObj->stdWrap($images, $this->conf['carousel.'][$this->type.'.']['stdWrap.']);
		}
		return $this->pi_wrapInBaseClass($return_string);
	}

	/**
	 * Include all defined resources (JS / CSS)
	 *
	 * @return void
	 */
	function addResources() {
		// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} else {
			$this->addJsFile($this->conf['jQueryLibrary'], true);
			$this->addJsFile("EXT:imagecarousel/res/jquery/js/jquery.easing-1.3.js");
		}
		// add all defined JS files
		if (count($this->jsFiles) > 0) {
			foreach ($this->jsFiles as $jsToLoad) {
				// Add script only once
				if (! preg_match("/".preg_quote($this->getPath($jsToLoad), "/")."/", $GLOBALS['TSFE']->additionalHeaderData['jsFile_'.$this->extKey])) {
					$GLOBALS['TSFE']->additionalHeaderData['jsFile_'.$this->extKey] .= ($this->getPath($jsToLoad) ? '<script src="'.$this->getPath($jsToLoad).'" type="text/javascript"></script>'.chr(10) :'');
				}
			}
		}
		// add all defined JS Script
		if (count($this->js) > 0) {
			foreach ($this->js as $jsToPut) {
				$temp_js .= $jsToPut;
			}
			if ($this->conf['jsInFooter']) {
				$GLOBALS['TSFE']->additionalFooterData['js_'.$this->extKey] .= t3lib_div::wrapJS($temp_js, true);
			} else {
				$GLOBALS['TSFE']->additionalHeaderData['js_'.$this->extKey] .= t3lib_div::wrapJS($temp_js, true);
			}
		}
		// add all defined CSS files
		if (count($this->cssFiles) > 0) {
			foreach ($this->cssFiles as $cssToLoad) {
				// Add script only once
				if (! preg_match("/".preg_quote($this->getPath($cssToLoad), "/")."/", $GLOBALS['TSFE']->additionalHeaderData['cssFile_'.$this->extKey])) {
					$GLOBALS['TSFE']->additionalHeaderData['cssFile_'.$this->extKey] .= ($this->getPath($cssToLoad) ? '<link rel="stylesheet" href="'.$this->getPath($cssToLoad).'" type="text/css" />'.chr(10) :'');
				}
			}
		}
		// add all defined CSS Script
		if (count($this->css) > 0) {
			foreach ($this->css as $cssToPut) {
				$temp_css .= $cssToPut;
			}
			$GLOBALS['TSFE']->additionalHeaderData['css_'.$this->extKey] .= '
<style type="text/css">
' . $temp_css . '
</style>';
		}
	}

	/**
	 * Return the webbased path
	 * 
	 * @param string $path
	 * return string
	 */
	function getPath($path="")
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
	function addJsFile($script="", $first=false)
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
	function addJS($script="")
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
	function addCssFile($script="")
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
	function addCSS($script="")
	{
		if (! in_array($script, $this->css)) {
			$this->css[] = $script;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagecarousel/pi1/class.tx_imagecarousel_pi1.php']);
}

?>