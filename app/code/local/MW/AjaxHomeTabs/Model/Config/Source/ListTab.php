<?php
class MW_AjaxHomeTabs_Model_Config_Source_ListTab{
	public function toOptionArray(){
        return array(
			array('value'=>'topfeature', 'label'=>Mage::helper('adminhtml')->__('Featured')),
            array('value'=>'topnewest', 'label'=>Mage::helper('adminhtml')->__('New Products')),
            array('value'=>'topbestsell', 'label'=>Mage::helper('adminhtml')->__('Top Sellers')),
            array('value'=>'toprate', 'label'=>Mage::helper('adminhtml')->__('Top Rated')),
            array('value'=>'topreview', 'label'=>Mage::helper('adminhtml')->__('Top Reviewed')),
			array('value'=>'topwish', 'label'=>Mage::helper('adminhtml')->__('Top Wishlist')),
			array('value'=>'custom1', 'label'=>Mage::helper('adminhtml')->__('Custom 1')),
			array('value'=>'custom2', 'label'=>Mage::helper('adminhtml')->__('Custom 2'))
        );
	}
}