<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tab_CategorySub extends Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Category
{
	protected function _prepareForm()
	{
		$this->_title      = Mage::helper('ammeta')->__('Categories in nested ones');
		$this->_fieldsetId = 'sub_categories';
		$this->_prefix     = 'sub_';

		return parent::_prepareForm();
	}
}