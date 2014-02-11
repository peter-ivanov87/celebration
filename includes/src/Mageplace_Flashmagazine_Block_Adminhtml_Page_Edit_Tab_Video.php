<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Video extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Video
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_page');

		$form = new Varien_Data_Form();

		$fieldset_video = $form->addFieldset('video_fieldset',
			array(
				'legend'	=> $this->__('Video'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_video->addType('fileext', Mage::getConfig()->getBlockClassName('flashmagazine/form_element_fileext'));

		$fieldset_video->addField('page_video',
			'fileext',
			array(
				'name'		=> 'page_video',
				'label'		=> $this->__('Page video'),
				'title'		=> basename($model->getData('page_video')),
				'note'		=> $this->__('Select flv files'),
			)
		);
		if($model->getData('page_video')) {
			$model->setData('page_video', Mage::helper('flashmagazine')->getPathUrl('video').'/'.$model->getData('page_video'));
		}

		$fieldset_video->addField('page_v_align',
			'select',
			array(
				'name'		=> 'page_v_align',
				'label'		=> $this->__('Video vertical align'),
				'title'		=> $this->__('Video vertical align'),
				'options'	=> array (
					'Top'		=> $this->__('Top'),
					'Middle'	=> $this->__('Middle'),
					'Bottom'	=> $this->__('Bottom')
				)
			)
		);

		$fieldset_video->addField('page_h_align',
			'select',
			array(
				'name'		=> 'page_h_align',
				'label'		=> $this->__('Video horizontal align'),
				'title'		=> $this->__('Video horizontal align'),
				'options'	=> array (
					'Left'		=> $this->__('Left'),
					'Center'	=> $this->__('Center'),
					'Right'		=> $this->__('Right')
				)
			)
		);

		$fieldset_video->addField('page_video_wdt',
			'text',
			array(
				'name'		=> 'page_video_wdt',
				'label'		=> $this->__('Video width'),
				'title'		=> $this->__('Video width'),
				'class'		=> 'validate-digits',
				'style'		=> 'width: 30px !important;',
			)
		);

		$fieldset_video->addField('page_video_hgt',
			'text',
			array(
				'name'		=> 'page_video_hgt',
				'label'		=> $this->__('Video height'),
				'title'		=> $this->__('Video height'),
				'class'		=> 'validate-digits',
				'style'		=> 'width: 30px !important;',
			)
		);

		$form->setHtmlIdPrefix('page_video_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
