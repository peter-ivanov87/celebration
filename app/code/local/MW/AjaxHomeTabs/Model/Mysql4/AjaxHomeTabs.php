<?php

class MW_AjaxHomeTabs_Model_Mysql4_AjaxHomeTabs extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ajaxhometabs_id refers to the key field in your database table.
        $this->_init('ajaxhometabs/ajaxhometabs', 'ajaxhometabs_id');
    }
}