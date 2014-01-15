<?php

class MW_AjaxHomeTabs_Model_Mysql4_AjaxHomeTabs_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxhometabs/ajaxhometabs');
    }
}