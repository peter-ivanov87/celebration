<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preperation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Category_Edit_Form
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_category');

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Category Details'),
				'class'		=> 'fieldset-wide'
			)
		);

		if ($model->getId()) {
			$fieldset->addField('category_id',
				'hidden',
				array(
					'name' => 'category_id'
				)
			);
		}

		$fieldset->addField('category_name',
			'text',
			array(
				'name'		=> 'category_name',
				'label'		=> $this->__('Category Name'),
				'title'		=> $this->__('Category Name'),
				'required'	=> true,
			)
		);

	/*$fieldset->addField('is_active',
			'select',
			array(
				'name'		=> 'is_active',
				'label'		=> $this->__('Category Status'),
				'title'		=> $this->__('Category Status'),
				'required'	=> true,
				'options'	=> array (
					'1' => Mage::helper('cms')->__('Enabled'),
					'0' => Mage::helper('cms')->__('Disabled')
				)
			)
		);*/


		$fieldset->addField('category_description',
			'textarea',
			array(
				'name'		=> 'category_description',
				'label'		=> $this->__('Category Description'),
				'title'		=> $this->__('Category Description'),
			)
		);

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$form->setAction($this->getSaveUrl());
		$form->setId('edit_form');
		$form->setMethod('post');

		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
