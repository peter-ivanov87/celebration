<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Page_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();

		$this->setId('page_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Page information'));
	}

	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();

		$this->addTab(
			'details_section',
			array(
				'label'		=> $this->__('Page Details'),
				'title'		=> $this->__('Page Details'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit_tab_details')->toHtml(),
				'active'	=> true,
			)
		);

		$this->addTab(
			'image_section',
			array(
				'label'		=> $this->__('Image'),
				'title'		=> $this->__('Image'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit_tab_image')->toHtml(),
			)
		);

		$this->addTab(
			'video_section',
			array(
				'label'		=> $this->__('Video'),
				'title'		=> $this->__('Video'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit_tab_video')->toHtml(),
			)
		);

		$this->addTab(
			'text_section',
			array(
				'label'		=> $this->__('Text'),
				'title'		=> $this->__('Text'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit_tab_text')->toHtml(),
			)
		);

		return $return;
	}
}
