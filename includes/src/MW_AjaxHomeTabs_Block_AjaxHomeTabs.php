<?php
class MW_AjaxHomeTabs_Block_AjaxHomeTabs extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAjaxHomeTabs()     
     { 
        if (!$this->hasData('ajaxhometabs')) {
            $this->setData('ajaxhometabs', Mage::registry('ajaxhometabs'));
        }
        return $this->getData('ajaxhometabs');
        
    }
}