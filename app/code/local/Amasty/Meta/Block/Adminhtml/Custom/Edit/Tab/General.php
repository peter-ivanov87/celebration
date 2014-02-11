<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Custom_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);

		/* @var $hlp Amasty_Meta_Helper_Data */
		$hlp = Mage::helper('ammeta');

		$fldCond = $form->addFieldset(
			'attr',
			array('legend' => Mage::helper('ammeta')->__('General'))
		);

		$fldCond->addField('custom_url',
			'text',
			array(
				'label' => $hlp->__('Page Url'),
				'name'  => 'custom_url',
				'note'  => $hlp->__('You can use \'*\' symbol for specify url pattern')
			)
		);

		$fldCond->addField('priority',
			'text',
			array(
				'label'  => $hlp->__('Priority'),
				'name'   => 'priority',
				'values' => Mage::getSingleton('ammeta/system_store')->getStoreValuesForForm(true),
				'class'  => 'validate-digits',
				'value'  => 0
			)
		);

		if (! Mage::app()->isSingleStoreMode()) {
			$fldCond->addField('store_id',
				'select',
				array(
					'label'  => $hlp->__('Show In'),
					'name'   => 'store_id',
					'values' => Mage::getSingleton('ammeta/system_store')->getStoreValuesForForm(true)
				)
			);
		}

		//set form values
		$form->setValues(Mage::registry('ammeta_config')->getData());

		return parent::_prepareForm();
	}
}