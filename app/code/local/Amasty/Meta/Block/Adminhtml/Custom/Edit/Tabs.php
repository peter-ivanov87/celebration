<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Custom_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('customTabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('ammeta')->__('Template Configuration'));
	}

	protected function _beforeToHtml()
	{
		$name = Mage::helper('ammeta')->__('General');
		$this->addTab('general', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_custom_edit_tab_general')
						->setTitle($name)->toHtml(),
			)
		);

		$name = Mage::helper('ammeta')->__('Page Content');
		$this->addTab('content', array(
				'label'   => $name,
				'content' => $this->getLayout()->createBlock('ammeta/adminhtml_custom_edit_tab_content')
						->setTitle($name)->toHtml(),
			)
		);
		return parent::_beforeToHtml();
	}
}