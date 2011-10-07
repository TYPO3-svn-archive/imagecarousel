#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_imagecarousel_mode varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_damimages varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_damcategories varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_images text,
	tx_imagecarousel_hrefs text,
	tx_imagecarousel_captions text,
	tx_imagecarousel_stoprecursion tinyint(3) DEFAULT '0' NOT NULL,
	tx_imagecarousel_skin varchar(20) DEFAULT '' NOT NULL
);



#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (
	tx_imagecarousel_mode varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_damimages varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_damcategories varchar(20) DEFAULT '' NOT NULL,
	tx_imagecarousel_images text,
	tx_imagecarousel_hrefs text,
	tx_imagecarousel_captions text,
	tx_imagecarousel_stoprecursion tinyint(3) DEFAULT '0' NOT NULL,
	tx_imagecarousel_skin varchar(20) DEFAULT '' NOT NULL
);



#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_imagecarousel_activate tinyint(3) DEFAULT '0' NOT NULL
);



#
# Table structure for table 'tx_dam'
#
CREATE TABLE tx_dam (
	tx_jfdam_link varchar(255) DEFAULT '' NOT NULL
);