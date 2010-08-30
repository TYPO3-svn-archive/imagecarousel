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
$TCA['tt_content']['palettes']['tx_imagecarousel'] = array(
	'showitem' => 'tx_imagecarousel_activate',
	'canNotCollapse' => 1,
);
t3lib_extMgm::addToAllTCAtypes('tt_content', '--palette--;LLL:EXT:imagecarousel/locallang_db.xml:tt_content.tx_imagecarousel_title;tx_imagecarousel', 'textpic', 'before:imagecaption');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform,image_zoom';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2'] = 'pi_flexform,image_zoom';



// Load fields for DAM
if (t3lib_extMgm::isLoaded("dam")) {
	$tempColumns = array(
		'tx_jfdam_link' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:tx_dam.tx_jfdam_link',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=600,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	);
	t3lib_div::loadTCA('tx_dam');
	t3lib_extMgm::addTCAcolumns('tx_dam', $tempColumns, 1);
	t3lib_extMgm::addToAllTCAtypes('tx_dam', '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_custom, tx_jfdam_link;;;;1-1-1');
	// add fields to index preset fields
	$TCA['tx_dam']['txdamInterface']['index_fieldList'] .= ',tx_jfdam_link';
}



t3lib_extMgm::addPlugin(array(
	'LLL:EXT:imagecarousel/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_imagecarousel_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_imagecarousel_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Image Carousel');



t3lib_extMgm::addPlugin(array(
	'LLL:EXT:imagecarousel/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_imagecarousel_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_imagecarousel_pi2_wizicon.php';
}



require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_imagecarousel_itemsProcFunc.php');

?>