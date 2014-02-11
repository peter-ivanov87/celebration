<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Product extends Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Tab
{
	protected function _addFieldsToFieldset(Varien_Data_Form_Element_Fieldset $fieldSet)
	{
		$hlp = Mage::helper('ammeta');
		
		$fieldSet->addField(
			$this->_prefix . 'product_meta_title',
			'text',
			array(
				'label' => $hlp->__('Title'),
				'name'  => $this->_prefix . 'product_meta_title',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'product_meta_description',
			'textarea',
			array(
				'label' => $hlp->__('Meta Description'),
				'name'  => $this->_prefix . 'product_meta_description',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'product_meta_keyword',
			'textarea',
			array(
				'label' => $hlp->__('Keywords'),
				'name'  => $this->_prefix . 'product_meta_keyword',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'product_h1_tag',
			'text',
			array(
				'label' => $hlp->__('H1 Tag'),
				'name'  => $this->_prefix . 'product_h1_tag',
				'note'  => $hlp->__('This value will override any H1 tag even it is not empty')
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'product_short_description',
			'textarea',
			array(
				'label' => $hlp->__('Short Description'),
				'name'  => $this->_prefix . 'product_short_description',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'product_description',
			'textarea',
			array(
				'label' => $hlp->__('Full Description'),
				'name'  => $this->_prefix . 'product_description',
			)
		);
	}
	
}