<?php

    
	include_once('../lib/Zend/Feed/Reader.php');
    define('MAGENTOROOT', '../');
    require_once(MAGENTOROOT.'app/Mage.php');
     
     
     
	$feedLinks = Zend_Feed_Reader::import('http://www.celebrationtemplates.com/blog/feed/rss/');


    
    echo "This job is finished";
?>

