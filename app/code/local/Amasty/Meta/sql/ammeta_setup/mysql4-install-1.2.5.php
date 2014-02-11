<?php
/**
 * @author Amasty
 * @copyright Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */
$this->startSetup();
$connection = $this->getConnection();

$this->run("
CREATE TABLE `{$this->getTable('ammeta/config')}` (
  `config_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(9) DEFAULT NULL,
  `stores` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `custom_url` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `custom_meta_title` varchar(255) DEFAULT NULL,
  `custom_meta_keywords` text,
  `custom_meta_description` text,
  `custom_canonical_url` varchar(255) DEFAULT NULL,
  `custom_robots` smallint(1) unsigned DEFAULT '0',
  `custom_h1_tag` varchar(255) DEFAULT NULL,
  `custom_in_page_text` text,
  `cat_meta_title` varchar(255) DEFAULT NULL,
  `cat_meta_description` text,
  `cat_meta_keywords` text,
  `cat_h1_tag` varchar(255) DEFAULT NULL,
  `cat_description` text,
  `cat_image_alt` varchar(255) DEFAULT NULL,
  `cat_image_title` varchar(255) DEFAULT NULL,
  `cat_after_product_text` text,
  `sub_cat_meta_title` varchar(255) DEFAULT NULL,
  `sub_cat_meta_description` text,
  `sub_cat_meta_keywords` text,
  `sub_cat_h1_tag` varchar(255) DEFAULT NULL,
  `sub_cat_description` text,
  `sub_cat_image_alt` varchar(255) DEFAULT NULL,
  `sub_cat_image_title` varchar(255) DEFAULT NULL,
  `sub_cat_after_product_text` text,
  `product_meta_title` varchar(255) DEFAULT NULL,
  `product_meta_keyword` text,
  `product_meta_description` text,
  `product_h1_tag` varchar(255) DEFAULT NULL,
  `product_short_description` text,
  `product_description` text,
  `sub_product_meta_title` varchar(255) DEFAULT NULL,
  `sub_product_meta_keyword` text,
  `sub_product_meta_description` text,
  `sub_product_h1_tag` varchar(255) DEFAULT NULL,
  `sub_product_short_description` text,
  `sub_product_description` text,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB CHARSET=utf8;
");



$this->endSetup();