<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Frontpage extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Frontpage
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_magazine');

		$form = new Varien_Data_Form();

		$fieldset_frontpage = $form->addFieldset('frontpage_fieldset',
			array(
				'legend'	=> $this->__('Frontpage options'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_frontpage->addField('magazine_enable_frontpage',
			'select',
			array(
				'name'		=> 'magazine_enable_frontpage',
				'label'		=> $this->__('Frontpage cover'),
				'title'		=> $this->__('Frontpage cover'),
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_frontpage->addField('magazine_author_image',
			'image',
			array(
				'name'		=> 'magazine_author_image',
				'label'		=> $this->__('Author image'),
				'note'		=> $this->__('Select gif, jpg or png files')
			)
		);
		if($model->getData('magazine_author_image')) {
			$model->setData('magazine_author_image', Mage::helper('flashmagazine')->getPathUrl('image').'/'.$model->getData('magazine_author_image'));
		}

		$fieldset_frontpage->addField('magazine_author_email',
			'text',
			array(
				'name'		=> 'magazine_author_email',
				'label'		=> $this->__('Author email'),
				'title'		=> $this->__('Author email'),
			)
		);

		$fieldset_frontpage->addField('magazine_author_description',
			'editor',
			array(
				'name'		=> 'magazine_author_description',
				'label'		=> $this->__('Author details'),
				'title'		=> $this->__('Author details'),
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			)
		);

		$fieldset_frontpage->addField('magazine_author_logo',
			'image',
			array(
				'name'					=> 'magazine_author_logo',
				'label'					=> $this->__('Book logo'),
				'note'					=> $this->__('Select gif, jpg or png files'),
				'after_element_html'	=> $this->__('It should be 180 x 40 pixels'),
			)
		);
		if($model->getData('magazine_author_logo')) {
			$model->setData('magazine_author_logo', Mage::helper('flashmagazine')->getPathUrl('logo').'/'.$model->getData('magazine_author_logo'));
		}

		$form->setHtmlIdPrefix('magazine_frontpage_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
