<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Image extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Image
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_page');

		$form = new Varien_Data_Form();

		$fieldset_image = $form->addFieldset('image_fieldset',
			array(
				'legend'	=> $this->__('Image'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_image->addField('page_image',
			'image',
			array(
				'name'		=> 'page_image',
				'label'		=> $this->__('Page image'),
				'note'		=> $this->__('Select gif, jpg or png files')
			)
		);
		if($model->getData('page_image')) {
			$model->setData('page_image', Mage::helper('flashmagazine')->getPathUrl('page').'/'.$model->getData('page_image'));
		}

		$fieldset_image->addField('page_zoom_image',
			'image',
			array(
				'name'		=> 'page_zoom_image',
				'label'		=> $this->__('Zoom page image'),
				'note'		=> $this->__('Select gif, jpg or png files'),
			)
		);
		if($model->getData('page_zoom_image')) {
			$model->setData('page_zoom_image', Mage::helper('flashmagazine')->getPathUrl('page').'/'.$model->getData('page_zoom_image'));
		}

		$form->setHtmlIdPrefix('page_image_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
