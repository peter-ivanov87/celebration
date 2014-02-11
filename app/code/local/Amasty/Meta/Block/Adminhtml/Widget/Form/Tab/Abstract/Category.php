<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Category extends Amasty_Meta_Block_Adminhtml_Widget_Form_Tab_Abstract_Tab
{
	protected function _addFieldsToFieldset(Varien_Data_Form_Element_Fieldset $fieldSet)
	{
		$hlp = Mage::helper('ammeta');

		$fieldSet->addField(
			$this->_prefix . 'cat_meta_title',
			'text',
			array(
				'label' => $hlp->__('Title'),
				'name'  => $this->_prefix . 'cat_meta_title',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_meta_description',
			'textarea',
			array(
				'label' => $hlp->__('Meta Description'),
				'name'  => $this->_prefix . 'cat_meta_description',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_meta_keywords',
			'textarea',
			array(
				'label' => $hlp->__('Keywords'),
				'name'  => $this->_prefix . 'cat_meta_keywords',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_h1_tag',
			'text',
			array(
				'label' => $hlp->__('H1 Tag'),
				'name'  => $this->_prefix . 'cat_h1_tag',
				'note'  => $hlp->__('This value will override any H1 tag even it is not empty')
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_description',
			'textarea',
			array(
				'label' => $hlp->__('Description'),
				'name'  => $this->_prefix . 'cat_description',
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_image_alt',
			'text',
			array(
				'label' => $hlp->__('Image Alt'),
				'name'  => $this->_prefix . 'cat_image_alt',
				'note'	=> $hlp->__('Please, make sure that category image is wrapped into tag with class \'category-image\' and image has %s attribute', 'alt')
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_image_title',
			'text',
			array(
				'label' => $hlp->__('Image Title'),
				'name'  => $this->_prefix . 'cat_image_title',
				'note'	=> $hlp->__('Please, make sure that category image is wrapped into tag with class \'category-image\' and image has %s attribute', 'title')
			)
		);

		$fieldSet->addField(
			$this->_prefix . 'cat_after_product_text',
			'textarea',
			array(
				'label' => $hlp->__('Text after Product List'),
				'name'  => $this->_prefix . 'cat_after_product_text',
			)
		);
		
	}
}