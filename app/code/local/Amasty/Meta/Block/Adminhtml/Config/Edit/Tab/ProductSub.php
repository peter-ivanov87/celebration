<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tab_ProductSub extends Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Product
{
	protected function _prepareForm()
	{
		$this->_title      = Mage::helper('ammeta')->__('Products In Nested Categories');
		$this->_fieldsetId = 'sub_products';
		$this->_prefix     = 'sub_';

		return parent::_prepareForm();
	}
}