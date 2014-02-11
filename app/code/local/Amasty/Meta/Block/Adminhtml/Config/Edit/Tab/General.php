<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
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

		$fldCond->addField('category_id',
			'select',
			array(
				'label'  => $hlp->__('Category is'),
				'name'   => 'category_id',
				'values' => $hlp->getTree(),
			)
		);

		if (! Mage::app()->isSingleStoreMode()) {
			$fldCond->addField('store_id',
				'select',
				array(
					'label'  => $hlp->__('Show In'),
					'name'   => 'store_id',
					'values' => Mage::getSingleton('ammeta/system_store')->getStoreValuesForForm(true),
				)
			);
		}

		//set form values
		$form->setValues(Mage::registry('ammeta_config')->getData());

		return parent::_prepareForm();
	}
}