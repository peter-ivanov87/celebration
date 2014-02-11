<?php
/**
* @author Amasty
* @copyright Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
*/
$this->startSetup();

$this->run("

CREATE TABLE `{$this->getTable('ammeta/config')}` (
  `config_id`        mediumint(9) NOT NULL auto_increment,

  `category_id`      mediumint(9) NOT NULL,
  `apply_for_child`  tinyint(1) NOT NULL,

  `stores` varchar(255) NOT NULL,
  
  `title`              varchar(255) NOT NULL,
  `keywords`           text NOT NULL,
  `description`        text NOT NULL,
  `short_description`  text NOT NULL,
  `full_description`   text NOT NULL,

  PRIMARY KEY  (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
");


$this->endSetup();