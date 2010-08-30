<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Page module hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['imagecarousel_pi1']['imagecarousel'] = 'EXT:imagecarousel/lib/class.tx_imagecarousel_cms_layout.php:tx_imagecarousel_cms_layout->getExtensionSummary';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['imagecarousel_pi2']['imagecarousel'] = 'EXT:imagecarousel/lib/class.tx_imagecarousel_cms_layout.php:tx_imagecarousel_cms_layout->getExtensionSummary';

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_imagecarousel_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_imagecarousel_pi2.php', '_pi2', 'list_type', 1);
?>