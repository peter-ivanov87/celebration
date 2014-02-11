<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preperation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$form->setId('edit_form');
		$form->setAction($this->getSaveUrl());
		$form->setMethod('post');
		$form->setEnctype('multipart/form-data');
		$form->setUseContainer(true);

		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
