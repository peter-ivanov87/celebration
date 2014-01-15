<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('novaworks_facebook_customer')} (
  `customer_id` int(10) NOT NULL,
  `fb_id` bigint(20) NOT NULL,
  UNIQUE KEY `FB_CUSTOMER` (`customer_id`,`fb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$this->getTable('novaworks_facebook_customer')}`
CHANGE `customer_id` `customer_id` INT( 10 ) UNSIGNED NOT NULL;

ALTER TABLE `{$this->getTable('novaworks_facebook_customer')}`
ADD FOREIGN KEY ( `customer_id` ) REFERENCES `{$this->getTable('customer_entity')}` (
`entity_id`
) ON DELETE CASCADE ;
");
$installer->endSetup();