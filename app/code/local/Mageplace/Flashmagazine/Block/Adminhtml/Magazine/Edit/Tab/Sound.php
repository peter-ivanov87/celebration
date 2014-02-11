<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Sound extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Sound
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_magazine');

		$form = new Varien_Data_Form();

		$fieldset_sound = $form->addFieldset('sound_fieldset',
			array(
				'legend'	=> $this->__('Sound options'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_sound->addField('magazine_enable_sound',
			'select',
			array(
				'name'		=> 'magazine_enable_sound',
				'label'		=> $this->__('Enable sound'),
				'title'		=> $this->__('Enable sound'),
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_sound->addType('fileext', Mage::getConfig()->getBlockClassName('flashmagazine/form_element_fileext'));

		$fieldset_sound->addField('magazine_background_sound',
			'fileext',
			array(
				'name'		=> 'magazine_background_sound',
				'label'		=> $this->__('Background sound'),
				'title'		=> basename($model->getData('magazine_background_sound')),
				'note'		=> $this->__('Select mp3 or wav files'),
			)
		);
		if($model->getData('magazine_background_sound')) {
			$model->setData('magazine_background_sound', Mage::helper('flashmagazine')->getPathUrl('sound').'/'.$model->getData('magazine_background_sound'));
		}

		$fieldset_sound->addField('magazine_flip_sound',
			'fileext',
			array(
				'name'		=> 'magazine_flip_sound',
				'label'		=> $this->__('Flip sound'),
				'title'		=> basename($model->getData('magazine_flip_sound')),
				'note'		=> $this->__('Select mp3 or wav files'),
			)
		);
		if($model->getData('magazine_flip_sound')) {
			$model->setData('magazine_flip_sound', Mage::helper('flashmagazine')->getPathUrl('sound').'/'.$model->getData('magazine_flip_sound'));
		}

		$form->setHtmlIdPrefix('magazine_sound_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
