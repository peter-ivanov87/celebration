<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout()
	{
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			/* @var $headBlock Mage_Page_Block_Html_Head */
			$headBlock->addItem('js_css', 'colorpicker/colorPicker.css')
				->addJs('scriptaculous/scriptaculous.js')
				->addJs('colorpicker/yahoo.color.js')
				->addJs('colorpicker/colorPicker.js');
		}
		
		return parent::_prepareLayout();
	}
	
	/**
	 * Preperation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Template_Edit_Form
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_template');
		
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Template Details'), 
				'class'		=> 'fieldset-wide'
			)
		);
		
		if ($model->getId()) {
			$fieldset->addField('template_id',
				'hidden',
				array(
					'name' => 'template_id'
				)
			);
		}
		
		$fieldset->addField('template_name',
			'text',
			array(
				'name'		=> 'template_name', 
				'label'		=> $this->__('Template Name'), 
				'title'		=> $this->__('Template Name'), 
				'required'	=> true,
			)
		);
		
		$fieldset->addField('template_type_id',
			'select',
			array(
				'name'		=> 'template_type_id', 
				'label'		=> $this->__('Template Type'), 
				'title'		=> $this->__('Template Type'), 
				'required'	=> true, 
				'values'	=> $this->_getTemplateTypeValuesForForm()
			)
		);

		$fieldset->addType('colorpicker', Mage::getConfig()->getBlockClassName('flashmagazine/form_element_colorpicker'));

		$fieldset->addField('template_background_color',
			'colorpicker',
			array(
				'name'		=> 'template_background_color', 
				'label'		=> $this->__('Backgroud Color'), 
				'title'		=> $this->__('Backgroud Color'), 
			)
		);
		
		$fieldset->addField('template_elements_color',
			'colorpicker',
			array(
				'name'		=> 'template_elements_color', 
				'label'		=> $this->__('Elements color'), 
				'title'		=> $this->__('Elements color'), 
			)
		);
		
		$fieldset->addField('template_additional_color',
			'colorpicker',
			array(
				'name'		=> 'template_additional_color', 
				'label'		=> $this->__('Additional color'), 
				'title'		=> $this->__('Additional color'), 
			)
		);
		

		$form->setValues($model->getData());
/*		
		$template_elements_color = !$model->getTemplateElementsColor() ? 'FFFFFF' : $model->getTemplateElementsColor();
		$form->getElement('template_elements_color')->setValue($template_elements_color);
*/		
		$form->setUseContainer(true);
		$form->setAction($this->getSaveUrl());
		$form->setId('edit_form');
		$form->setMethod('post');
		
		$this->setForm($form);

		return parent::_prepareForm();
	}
	
	/**
	 * Helper function to load template types collection
	 *
	 */
	protected function _getTemplateTypeValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/template_type_collection')->toOptionArray();
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
