<?php
class Krishinc_Videogallery_Model_Mysql4_Videogallery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('videogallery/videogallery', 'videogallery_id');
		$this->_isPkAutoIncrement = false;
    }
}