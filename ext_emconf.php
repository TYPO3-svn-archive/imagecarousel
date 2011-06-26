<?php

########################################################################
# Extension Manager/Repository config file for ext "imagecarousel".
#
# Auto generated 27-06-2011 01:10
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Image Carousel',
	'description' => 'Show images as an jCarousel or Cloud-Carousel, manage the images, captions and hrefs in backend. Add media from DAM and DAM-Category. Use t3jquery for better integration of other jQuery extensions.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.8.0',
	'dependencies' => 'cms,jftcaforms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_imagecarousel',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Juergen Furrer',
	'author_email' => 'juergen.furrer@gmail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.0-5.3.99',
			'typo3' => '4.3.0-4.5.99',
			'jftcaforms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:70:{s:26:"class.tx_imagecarousel.php";s:4:"930b";s:21:"ext_conf_template.txt";s:4:"2a45";s:12:"ext_icon.gif";s:4:"53c5";s:17:"ext_localconf.php";s:4:"e674";s:14:"ext_tables.php";s:4:"751e";s:14:"ext_tables.sql";s:4:"ccc3";s:13:"locallang.xml";s:4:"aa1b";s:26:"locallang_csh_flexform.xml";s:4:"85e7";s:16:"locallang_db.xml";s:4:"3881";s:12:"mode_dam.gif";s:4:"999b";s:15:"mode_damcat.gif";s:4:"2596";s:15:"mode_folder.gif";s:4:"9d05";s:12:"mode_rte.gif";s:4:"2ded";s:15:"mode_upload.gif";s:4:"fecd";s:12:"t3jquery.txt";s:4:"13b0";s:24:"compat/flashmessages.css";s:4:"4e2c";s:20:"compat/gfx/error.png";s:4:"e4dd";s:26:"compat/gfx/information.png";s:4:"3750";s:21:"compat/gfx/notice.png";s:4:"a882";s:17:"compat/gfx/ok.png";s:4:"8bfe";s:22:"compat/gfx/warning.png";s:4:"c847";s:14:"doc/manual.sxw";s:4:"0bbf";s:38:"lib/class.tx_imagecarousel_TCAform.php";s:4:"c06c";s:41:"lib/class.tx_imagecarousel_cms_layout.php";s:4:"714d";s:44:"lib/class.tx_imagecarousel_itemsProcFunc.php";s:4:"9d39";s:38:"lib/class.tx_imagecarousel_tceFunc.php";s:4:"2321";s:42:"lib/class.tx_imagecarousel_tsparserext.php";s:4:"2105";s:15:"pi1/ce_icon.gif";s:4:"370d";s:14:"pi1/ce_wiz.gif";s:4:"c197";s:34:"pi1/class.tx_imagecarousel_pi1.php";s:4:"792a";s:42:"pi1/class.tx_imagecarousel_pi1_wizicon.php";s:4:"6ed3";s:13:"pi1/clear.gif";s:4:"cc11";s:19:"pi1/flexform_ds.xml";s:4:"10cf";s:17:"pi1/locallang.xml";s:4:"8469";s:15:"pi2/ce_icon.gif";s:4:"53c5";s:14:"pi2/ce_wiz.gif";s:4:"848e";s:34:"pi2/class.tx_imagecarousel_pi2.php";s:4:"c66b";s:42:"pi2/class.tx_imagecarousel_pi2_wizicon.php";s:4:"899d";s:13:"pi2/clear.gif";s:4:"cc11";s:19:"pi2/flexform_ds.xml";s:4:"73ca";s:17:"pi2/locallang.xml";s:4:"8469";s:26:"pi2/images/rotate-left.png";s:4:"2e9d";s:27:"pi2/images/rotate-right.png";s:4:"28c5";s:18:"res/chgallery.html";s:4:"7869";s:36:"res/jquery/js/imagecarousel-1.4.1.js";s:4:"0617";s:33:"res/jquery/js/jquery-1.6.1.min.js";s:4:"a34f";s:37:"res/jquery/js/jquery.captify-1.1.3.js";s:4:"9cf6";s:41:"res/jquery/js/jquery.captify-1.1.3.min.js";s:4:"9045";s:44:"res/jquery/js/jquery.cloudcarousel-1.0.5a.js";s:4:"4aa8";s:48:"res/jquery/js/jquery.cloudcarousel-1.0.5a.min.js";s:4:"13c9";s:34:"res/jquery/js/jquery.easing-1.3.js";s:4:"6516";s:39:"res/jquery/js/jquery.jcarousel-0.2.8.js";s:4:"a4c3";s:43:"res/jquery/js/jquery.jcarousel-0.2.8.min.js";s:4:"e21f";s:44:"res/jquery/js/jquery.mousewheel-3.0.4.min.js";s:4:"e2b7";s:25:"res/skins/ie7/credits.txt";s:4:"978b";s:33:"res/skins/ie7/next-horizontal.gif";s:4:"2e31";s:31:"res/skins/ie7/next-vertical.gif";s:4:"9b7d";s:33:"res/skins/ie7/prev-horizontal.gif";s:4:"0730";s:31:"res/skins/ie7/prev-vertical.gif";s:4:"fda9";s:22:"res/skins/ie7/skin.css";s:4:"342d";s:27:"res/skins/tango/credits.txt";s:4:"b1a7";s:35:"res/skins/tango/next-horizontal.png";s:4:"f457";s:33:"res/skins/tango/next-vertical.png";s:4:"75f3";s:35:"res/skins/tango/prev-horizontal.png";s:4:"09bd";s:33:"res/skins/tango/prev-vertical.png";s:4:"17bc";s:24:"res/skins/tango/skin.css";s:4:"b7fe";s:20:"static/constants.txt";s:4:"b4a5";s:16:"static/setup.txt";s:4:"7b49";s:34:"static/cloudcarousel/constants.txt";s:4:"883d";s:30:"static/cloudcarousel/setup.txt";s:4:"e8a0";}',
	'suggests' => array(
	),
);

?>