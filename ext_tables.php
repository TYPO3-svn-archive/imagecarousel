<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}



// PAGE
$tempColumns = array();
if (t3lib_extMgm::isLoaded('dam')) {
	$tempColumns['tx_imagecarousel_mode'] = array(
		'exclude' => 1,
		'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_mode',
		'config' => array(
			'type' => 'select',
			'itemsProcFunc' => 'tx_imagecarousel_itemsProcFunc->getModes',
			'size' => 1,
			'maxitems' => 1,
		)
	);
	$tempColumns['tx_imagecarousel_damimages'] = array(
		'exclude' => 1,
		'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_damimages',
		'displayCond' => 'FIELD:tx_imagecarousel_mode:=:dam',
		'config' => array(
			'type' => 'group',
			'form_type' => 'user',
			'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',
			'userProcessClass' => 'EXT:mmforeign/class.tx_mmforeign_tce.php:tx_mmforeign_tce',
			'internal_type' => 'db',
			'allowed' => 'tx_dam',
			'allowed_types' => 'gif,jpg,jpeg,png',
			'prepend_tname' => 1,
			'MM' => 'tx_dam_mm_ref',
			'MM_foreign_select' => 1,
			'MM_opposite_field' => 1,
			'MM_match_fields' => array(
				'ident' => 'imagecarousel',
			),
			'show_thumbs' => true,
			'size' => 10,
			'autoSizeMax' => 30,
			'minitems' => 0,
			'maxitems' => 1000,
		)
	);
	if (t3lib_extMgm::isLoaded("dam_catedit")) {
		$tempColumns['tx_imagecarousel_damcategories'] = array(
			'exclude' => 1,
			'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_damcategories',
			'displayCond' => 'FIELD:tx_imagecarousel_mode:=:dam_catedit',
			'config' => array(
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:tx_dam_tceFunc->getSingleField_selectTree',
				'treeViewClass' => 'EXT:dam/components/class.tx_dam_selectionCategory.php:tx_dam_selectionCategory',
				'foreign_table' => 'tx_dam_cat',
				'size' => 5,
				'autoSizeMax' => 25,
				'minitems' => 0,
				'maxitems' => 99,
			)
		);
	}
}
// Normal page fields
$tempColumns['tx_imagecarousel_images'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_images',
	'displayCond' => 'FIELD:tx_imagecarousel_mode:!IN:dam,dam_catedit',
	'config' => array(
		'type' => 'group',
		'internal_type' => 'file',
		'allowed' => 'gif,png,jpeg,jpg',
		'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
		'uploadfolder' => 'uploads/tx_imagecarousel',
		'show_thumbs' => 1,
		'size' => 6,
		'minitems' => 0,
		'maxitems' => 1000,
	)
);
$tempColumns['tx_imagecarousel_hrefs'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_hrefs',
	'displayCond' => 'FIELD:tx_imagecarousel_mode:!IN:dam,dam_catedit',
	'config' => array(
		'type' => 'text',
		'wrap' => 'OFF',
		'cols' => '48',
		'rows' => '6',
	)
);
$tempColumns['tx_imagecarousel_captions'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_captions',
	'displayCond' => 'FIELD:tx_imagecarousel_mode:!IN:dam,dam_catedit',
	'config' => array(
		'type' => 'text',
		'wrap' => 'OFF',
		'cols' => '48',
		'rows' => '6',
	)
);
$tempColumns['tx_imagecarousel_skin'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_skin',
	'config' => array(
		'type' => 'select',
		'items' => array(
			array('LLL:EXT:imagecarousel/locallang_db.xml:tt_content.pi_flexform.from_ts', ''),
		),
		'itemsProcFunc' => 'tx_imagecarousel_itemsProcFunc->getSkins',
		'size' => 1,
		'maxitems' => 1,
	)
);
$tempColumns['tx_imagecarousel_stoprecursion'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:imagecarousel/locallang_db.xml:pages.tx_imagecarousel_stoprecursion',
	'config' => array(
		'type' => 'check',
	)
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_imagecarousel_mode;;;;1-1-1, tx_imagecarousel_damimages, tx_imagecarousel_damcategories, tx_imagecarousel_images, tx_imagecarousel_hrefs, tx_imagecarousel_captions, tx_imagecarousel_skin, tx_imagecarousel_stoprecursion');

t3lib_div::loadTCA('pages_language_overlay');
t3lib_extMgm::addTCAcolumns('pages_language_overlay', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('pages_language_overlay','tx_imagecarousel_mode;;;;1-1-1, tx_imagecarousel_damimages, tx_imagecarousel_damcategories, tx_imagecarousel_images, tx_imagecarousel_hrefs, tx_imagecarousel_captions, tx_imagecarousel_skin, tx_imagecarousel_stoprecursion');

$TCA['pages']['ctrl']['requestUpdate'] .= ($TCA['pages']['ctrl']['requestUpdate'] ? ',' : ''). 'tx_imagecarousel_mode';
$TCA['pages_language_overlay']['ctrl']['requestUpdate'] .= ($TCA['pages_language_overlay']['ctrl']['requestUpdate'] ? ',' : ''). 'tx_imagecarousel_mode';



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
	t3lib_extMgm::extRelPath($_EXTKEY) . 'pi1/ce_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_imagecarousel_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_imagecarousel_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Image Carousel');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/cloudcarousel/', 'Cloud-Carousel');



t3lib_extMgm::addPlugin(array(
	'LLL:EXT:imagecarousel/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'pi2/ce_icon.gif'
),'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_imagecarousel_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_imagecarousel_pi2_wizicon.php';
}



require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_imagecarousel_itemsProcFunc.php');
require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_imagecarousel_TCAform.php');

?>