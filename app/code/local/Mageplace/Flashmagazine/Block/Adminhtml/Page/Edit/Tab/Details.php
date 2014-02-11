<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Details
	 */
	protected function _prepareForm()
	{
		$model			= Mage::registry('flashmagazine_page');
		$magazine_model	= Mage::registry('flashmagazine_magazine');

		$form = new Varien_Data_Form();

		$fieldset_details = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Page Details'),
				'class'		=> 'fieldset-wide'
			)
		);

		if(!$model->getId()) {
			$fieldset_details->addField('page_magazine_id',
				'select',
				array(
					'name'		=> 'page_magazine_id',
					'label'		=> $this->__('Page Book'),
					'title'		=> $this->__('Page Book'),
					'required'	=> true,
					'values'	=> $this->_getMagazinesValuesForForm()
				)
			);
		} else {
			$fieldset_details->addField('page_magazine_title',
				'note',
				array(
					'text'		=> '<h3>'.$magazine_model->getName().'<h3>',
					'label'		=> $this->__('Page Book'),
				)
			);

			$fieldset_details->addField('page_id',
				'hidden',
				array(
					'name' => 'page_id'
				)
			);

			$fieldset_details->addField('page_magazine_id',
				'hidden',
				array(
					'name' => 'page_magazine_id'
				)
			);
		}

		$fieldset_details->addField('page_title',
			'text',
			array(
				'name'		=> 'page_title',
				'label'		=> $this->__('Page Title'),
				'title'		=> $this->__('Page Title'),
				'required'	=> true,
			)
		);


		$fieldset_details->addField('page_type',
			'select',
			array(
				'name'		=> 'page_type',
				'label'		=> $this->__('Page Content'),
				'title'		=> $this->__('Page Content'),
				'required'	=> true,
				'options'	=> array (
						'Image'	=> $this->__('Image'),
						'Video'	=> $this->__('Video'),
						'Text'	=> $this->__('Text')
				)
			)
		);

		$fieldset_details->addField('is_active',
			'select',
			array(
				'name'		=> 'is_active',
				'label'		=> $this->__('Page Status'),
				'title'		=> $this->__('Page Status'),
				'required'	=> true,
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_details->addField('page_sort_order',
			'text',
			array(
				'name'		=> 'page_sort_order',
				'label'		=> $this->__('Page Position'),
				'title'		=> $this->__('Page Position'),
				'class'		=> 'validate-digits',
				'style'		=> 'width: 30px !important;',
			)
		);

		$form->setHtmlIdPrefix('page_details_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Helper function to load magazines collection
	 */
	protected function _getMagazinesValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/magazine_collection')->toOptionArray();
	}
}
