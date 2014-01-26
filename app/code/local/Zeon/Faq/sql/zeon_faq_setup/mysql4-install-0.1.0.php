<?php
/**
 * Zeon Solutions,Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web,please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Faq
 * @copyright   Copyright (c) 2012 Zeon Solutions,Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

/* @var $installer Zeon_Faq_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("

/* Table structure for table `zeon_faq` */

DROP TABLE IF EXISTS {$this->getTable('zeon_faq/faq')};
 CREATE TABLE {$this->getTable('zeon_faq/faq')} (
    `faq_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Faq Id',
    `title` varchar(255) DEFAULT NULL COMMENT 'Title',
    `category_id` int(10) unsigned DEFAULT NULL COMMENT 'Category Id',
    `status` smallint(6) NOT NULL COMMENT 'Status',
    `is_most_frequently` varchar(255) DEFAULT NULL COMMENT 'Is Most Frequently',
    `description` text NOT NULL COMMENT 'Description',
    `sort_order` smallint(6) DEFAULT NULL COMMENT 'Sort Order',
    `creation_time` datetime DEFAULT NULL COMMENT 'Creation Time',
    `update_time` datetime DEFAULT NULL COMMENT 'Update Time',
    PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zeon Faq';

/*Table structure for table `zeon_faq_store` */

DROP TABLE IF EXISTS {$this->getTable('zeon_faq/store')};
CREATE TABLE {$this->getTable('zeon_faq/store')} (
    `faq_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Faq Id',
    `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
    PRIMARY KEY (`faq_id`,`store_id`),
    KEY `IDX_ZEON_FAQ_STORE_FAQ_ID` (`faq_id`),
    KEY `IDX_ZEON_FAQ_STORE_STORE_ID` (`store_id`),
    CONSTRAINT `FK_ZEON_FAQ_STORE_FAQ_ID_ZEON_FAQ_FAQ_ID` FOREIGN KEY (`faq_id`) REFERENCES {$this->getTable('zeon_faq/faq')} (`faq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ZEON_FAQ_STORE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zeon Faq Store';

/*Table structure for table `zeon_faq_category` */

DROP TABLE IF EXISTS {$this->getTable('zeon_faq/category')};
CREATE TABLE {$this->getTable('zeon_faq/category')} (
    `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Category Id',
    `identifier` varchar(255) DEFAULT NULL COMMENT 'Identifier',
    `title` varchar(255) DEFAULT NULL COMMENT 'Title',
    `sort_order` smallint(6) DEFAULT NULL COMMENT 'Sort Order',
    `status` smallint(6) NOT NULL COMMENT 'Status',
    `creation_time` datetime DEFAULT NULL COMMENT 'Creation Time',
    `update_time` datetime DEFAULT NULL COMMENT 'Update Time',
    PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zeon Faq Category';

");

$installer->endSetup();