<?php
 	 
    $installer = $this;
    $installer->startSetup();
    $installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('videogallery')};
	CREATE TABLE {$this->getTable('videogallery')} (
     `videogallery_id` int(11) unsigned NOT NULL auto_increment,
     `videogallery_category` varchar(200) NOT NULL default '',
	 `videogallery_url` varchar(500) NOT NULL default '',
     `name` varchar(255) NOT NULL default '',
	 `image` varchar(255) NOT NULL default '',
	 `gallery_image` varchar(255) NOT NULL default '',
	 PRIMARY KEY (`videogallery_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;   
	");
 	$installer->endSetup();
