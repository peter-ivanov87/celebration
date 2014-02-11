<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Text extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout()
	{
		if (($headBlock = $this->getLayout()->getBlock('head')) && (Mage::getSingleton('cms/wysiwyg_config')->isEnabled())) {
			/* @var $headBlock Mage_Page_Block_Html_Head */
			$headBlock->setCanLoadTinyMce(true);
		}

		return parent::_prepareLayout();
	}

	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tab_Text
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_page');

		$form = new Varien_Data_Form();

		$fieldset_text = $form->addFieldset('text_fieldset',
			array(
				'legend'	=> $this->__('Text'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_text->addField('page_text',
			'editor',
			array(
				'name'		=> 'page_text',
				'label'		=> $this->__('Page text'),
				'title'		=> $this->__('Page text'),
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
				'note'		=> '<i style="color:red; font-weight: bold;">'.$this->__("Attention: Use only editor features.").'</i>',
			)
		);

		$form->setHtmlIdPrefix('page_text_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
