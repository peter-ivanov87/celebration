<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();

		$this->setId('magazine_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Book information'));
	}

	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();

		$this->addTab(
			'details_section',
			array(
				'label'		=> $this->__('Book Details'),
				'title'		=> $this->__('Book Details'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit_tab_details')->toHtml(),
				'active'	=> true,
			)
		);

		$this->addTab(
			'pdf_section',
			array(
				'label'		=> $this->__('PDF options'),
				'title'		=> $this->__('PDF options'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit_tab_pdf')->toHtml(),
			)
		);

		$this->addTab(
			'sound_section',
			array(
				'label'		=> $this->__('Sound options'),
				'title'		=> $this->__('Sound options'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit_tab_sound')->toHtml(),
			)
		);

		$this->addTab(
			'frontpage_section',
			array(
				'label'		=> $this->__('Frontpage options'),
				'title'		=> $this->__('Frontpage options'),
				'content'	=> $this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit_tab_frontpage')->toHtml(),
			)
		);

		return $return;
	}
}
