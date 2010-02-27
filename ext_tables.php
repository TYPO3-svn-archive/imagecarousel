<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Content
$tempColumns = Array (
	"tx_imagecarousel_activate" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:imagecarousel/locallang_db.xml:tt_content.tx_imagecarousel_activate",
		"config" => Array (
			"type" => "check",
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns,1);
$GLOBALS['TCA']['tt_content']['palettes']['7']['showitem'] .= ',tx_imagecarousel_activate';

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform,image_zoom';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:imagecarousel/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_imagecarousel_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_imagecarousel_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Image Carousel');

require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_imagecarousel_itemsProcFunc.php');

?>