<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

$query = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/category')}` (
	`category_id`			int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_name`			varchar(255) NOT NULL,
	`category_description`	text NOT NULL,
	`creation_date`			datetime NOT NULL,
	`update_date`			datetime NOT NULL,
	`is_active`				tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Flashmagazine categories';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/template_type')}` (
	`type_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type_name`				varchar(100) NOT NULL DEFAULT '',
	`type_image`			varchar(100) DEFAULT NULL,
	PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flashmagazine templates types';

INSERT IGNORE INTO `{$this->getTable('flashmagazine/template_type')}`
	(`type_id`, `type_name`, `type_image`)
VALUES
	(1, 'style_artnuvo_one', 'style_artnuvo_one_thumbnail.jpg'),
	(2, 'style_classic_one', 'style_classic_one_thumbnail.jpg'),
	(3, 'style_classic_black', 'style_classic_black_thumbnail.jpg'),
	(4, 'style_butterfly_one', 'style_butterfly_one_thumbnail.jpg');


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/template')}` (
	`template_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`template_name`				varchar(250) NOT NULL DEFAULT '',
	`template_type_id`			int(10) unsigned NOT NULL DEFAULT '1',
	`template_background_color`	varchar(10) NOT NULL DEFAULT '',
	`template_elements_color`	varchar(10) NOT NULL DEFAULT '',
	`template_additional_color`	varchar(10) NOT NULL DEFAULT '',
	`creation_date`				datetime NOT NULL,
	`update_date`				datetime NOT NULL,
	PRIMARY KEY (`template_id`),
	CONSTRAINT `FK_FLASHMAGAZINE_TEMPLATE_TYPE_ID` FOREIGN KEY (`template_type_id`) REFERENCES `{$this->getTable('flashmagazine/template_type')}` (`type_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flashmagazine templates';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/resolution')}` (
	`resolution_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`resolution_name`			varchar(250) NOT NULL,
	`resolution_width`			int(10) unsigned NOT NULL DEFAULT '800',
	`resolution_height`			int(10) unsigned NOT NULL DEFAULT '600',
	`creation_date`				datetime NOT NULL,
	`update_date`				datetime NOT NULL,
	PRIMARY KEY (`resolution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flashmagazine resolutions';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/magazine')}` (
	`magazine_id`					int(10) unsigned NOT NULL AUTO_INCREMENT,
	`magazine_category_id`			int(10) unsigned NOT NULL DEFAULT '0',
	`magazine_template_id`			int(10) unsigned NOT NULL DEFAULT '0',
	`magazine_resolution_id`		int(10) unsigned NOT NULL DEFAULT '0',
	`magazine_title`				varchar(255) NOT NULL DEFAULT '',
	`magazine_enable_print`			tinyint(1) unsigned NOT NULL DEFAULT '1',
	`magazine_description`			text,
	`magazine_enable_pdf`			tinyint(1) NOT NULL DEFAULT '0',
	`magazine_background_pdf`		varchar(250) DEFAULT NULL,
	`magazine_enable_fullscreen`	tinyint(1) unsigned NOT NULL DEFAULT '0',
	`magazine_enable_sound`			tinyint(1) unsigned NOT NULL DEFAULT '0',
	`magazine_background_sound`		varchar(250) DEFAULT NULL,
	`magazine_flip_sound`			varchar(250) DEFAULT NULL,
	`magazine_enable_looping`		tinyint(1) unsigned NOT NULL DEFAULT '0',
	`magazine_enable_frontpage`		tinyint(1) unsigned NOT NULL DEFAULT '0',
	`magazine_view_style`			tinyint(1) unsigned NOT NULL DEFAULT '2',
	`magazine_author_image`			varchar(250) DEFAULT NULL,
	`magazine_author_email`			varchar(250) DEFAULT NULL,
	`magazine_author_description`	text,
	`magazine_author_logo`			varchar(250) DEFAULT NULL,
	`magazine_list_description`		text,
	`magazine_thumb`				varchar(250) DEFAULT NULL,
	`magazine_popup`				int(2) DEFAULT '1',
	`magazine_imgsub`				binary(1) DEFAULT '0',
	`magazine_imgsubfolder`			varchar(250) DEFAULT '',
	`magazine_sort_order`			int(11) NOT NULL DEFAULT '0',
	`magazine_hide_shadow`			tinyint(4) NOT NULL DEFAULT '0',
	`magazine_enable_back`			tinyint(1) unsigned NOT NULL DEFAULT '1',
	`creation_date`					datetime NOT NULL,
	`update_date`					datetime NOT NULL,
	`is_active`						tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`magazine_id`),
	CONSTRAINT `FK_FLASHMAGAZINE_CATEGORY_ID` FOREIGN KEY (`magazine_category_id`) REFERENCES `{$this->getTable('flashmagazine/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FLASHMAGAZINE_TEMPLATE_ID` FOREIGN KEY (`magazine_template_id`) REFERENCES `{$this->getTable('flashmagazine/template')}` (`template_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FLASHMAGAZINE_RESOLUTION_ID` FOREIGN KEY (`magazine_resolution_id`) REFERENCES `{$this->getTable('flashmagazine/resolution')}` (`resolution_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flashmagazine magazines';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/magazine_store')}` (
	`magazine_id`			int(10) unsigned NOT NULL,
	`store_id`				smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`magazine_id`,`store_id`),
	CONSTRAINT `FK_FLASHMAGAZINE_MAGAZINE_STORE_MAGAZINE_ID` FOREIGN KEY (`magazine_id`) REFERENCES `{$this->getTable('flashmagazine/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FLASHMAGAZINE_MAGAZINE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Stores and Magazines Relations';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/page')}` (
	`page_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`page_magazine_id`		int(10) unsigned unsigned NOT NULL DEFAULT '0',
	`page_title`			varchar(255) DEFAULT NULL,
	`page_type`				varchar(10) DEFAULT NULL,
	`page_sound`			varchar(250) DEFAULT NULL,
	`page_sort_order`		int(11) DEFAULT '0',
	`page_image`			varchar(250) DEFAULT NULL,
	`page_zoom_image`		varchar(250) DEFAULT NULL,
	`page_video`			varchar(250) DEFAULT NULL,
	`page_v_align`			varchar(20) DEFAULT NULL,
	`page_h_align`			varchar(20) DEFAULT NULL,
	`page_video_wdt`		varchar(4) DEFAULT NULL,
	`page_video_hgt`		varchar(4) DEFAULT NULL,
	`page_text`				text,
	`creation_date`			datetime NOT NULL,
	`update_date`			datetime NOT NULL,
	`is_active`				tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`page_id`),
	CONSTRAINT `FK_FLASHMAGAZINE_PAGE_MAGAZINE_ID` FOREIGN KEY (`page_magazine_id`) REFERENCES `{$this->getTable('flashmagazine/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazines Pages';


CREATE TABLE IF NOT EXISTS `{$this->getTable('flashmagazine/product_magazine')}` (
	`magazine_id`			int(10) unsigned NOT NULL,
	`entity_id`				int(10) unsigned NOT NULL,
	PRIMARY KEY (`magazine_id`,`entity_id`),
	CONSTRAINT `FK_FLASHMAGAZINE_PRODUCT_MAGAZINE_MAGAZINE_ID` FOREIGN KEY (`magazine_id`) REFERENCES `{$this->getTable('flashmagazine/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FLASHMAGAZINE_PRODUCT_MAGAZINE_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Products and Magazines Relations';


";

$installer = $this;
$installer->startSetup();
$installer->run($query);
$installer->endSetup();