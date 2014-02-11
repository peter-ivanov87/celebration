<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('configTabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('ammeta')->__('Template Configuration'));
	}

	protected function _beforeToHtml()
	{
		$name = Mage::helper('ammeta')->__('General');
		$this->addTab('general', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_config_edit_tab_general')
						->setTitle($name)->toHtml(),
			)
		);

		$name = Mage::helper('ammeta')->__('Current Products');
		$this->addTab('products', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_config_edit_tab_product')
						->setTitle($name)->toHtml(),
			)
		);

		$name = Mage::helper('ammeta')->__('Products in Sub Categories');
		$this->addTab('productSub', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_config_edit_tab_productSub')
						->setTitle($name)->toHtml(),
			)
		);

		$name = Mage::helper('ammeta')->__('Current Categories');
		$this->addTab('category', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_config_edit_tab_category')
						->setTitle($name)->toHtml(),
			)
		);

		$name = Mage::helper('ammeta')->__('Sub Categories');
		$this->addTab('categorySub', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_config_edit_tab_categorySub')
						->setTitle($name)->toHtml(),
			)
		);
		return parent::_beforeToHtml();
	}
}