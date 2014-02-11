<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Custom_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);

		/* @var $hlp Amasty_Meta_Helper_Data */
		$hlp = Mage::helper('ammeta');

		$fldCond = $form->addFieldset(
			'attr',
			array('legend' => Mage::helper('ammeta')->__('Content'))
		);

		$fldCond->addField('custom_meta_title',
			'text',
			array(
				'label' => $hlp->__('Title'),
				'name'  => 'custom_meta_title'
			)
		);

		$fldCond->addField('custom_meta_description',
			'textarea',
			array(
				'label' => $hlp->__('Meta Description'),
				'name'  => 'custom_meta_description'
			)
		);

		$fldCond->addField('custom_meta_keywords',
			'textarea',
			array(
				'label' => $hlp->__('Keywords'),
				'name'  => 'custom_meta_keywords'
			)
		);

		$fldCond->addField('custom_canonical_url',
			'text',
			array(
				'label' => $hlp->__('Canonical Url'),
				'name'  => 'custom_canonical_url'
			)
		);

		$fldCond->addField('custom_robots',
			'select',
			array(
				'label'  => $hlp->__('Robots'),
				'name'   => 'custom_robots',
				'values' => $hlp->getRobotOptions()
			)
		);

		$fldCond->addField('custom_h1_tag',
			'text',
			array(
				'label' => $hlp->__('H1 Tag'),
				'name'  => 'custom_h1_tag',
				'note'  => $hlp->__('This value will override any H1 tag even it is not empty')
			)
		);

		/*$fldCond->addField('custom_in_page_text',
			'textarea',
			array(
				'label'  => $hlp->__('In Page Text'),
				'name'   => 'custom_in_page_text'
			)
		);*/


		//set form values
		$form->setValues(Mage::registry('ammeta_config')->getData());

		return parent::_prepareForm();
	}
}