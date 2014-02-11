<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Multiupload extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'flashmagazine';
		$this->_controller = 'adminhtml';
		$this->_mode = 'multiupload';

		parent::__construct();

		$this->_removeButton('reset');
		$this->_removeButton('back');
		$this->_updateButton('save', 'label', $this->__('Create pages'));
		$this->_updateButton('save', 'id', 'save_button');


		$this->_formScripts[] = "
			function setSourceType(el) {
				var upload_package = document.getElementById('upload_package');
				var input_dir = document.getElementById('input_dir');
				var delete_files = document.getElementById('delete_files');

				if(el.options[el.selectedIndex].value == 'dir') {
					input_dir.disabled = false;
					delete_files.disabled = false;
					upload_package.disabled = true;
				} else {
					upload_package.disabled = false;
					input_dir.disabled = true;
					delete_files.disabled = true;
				}
			}
		";
	}

	public function getHeaderText()
	{
		return $this->__('Multiupload');
	}

	public function getHeaderCssClass()
	{
		return 'icon-head head-backups-control';
	}
}