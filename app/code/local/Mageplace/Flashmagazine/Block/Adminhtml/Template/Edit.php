<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the category edit form
	 */
	public function __construct()
	{
		$this->_objectId = 'template_id';
		$this->_blockGroup = 'flashmagazine';
		$this->_controller = 'adminhtml_template';
		
		parent::__construct();

		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save Template'));
		$this->_updateButton('delete', 'label', $this->__('Delete Template'));
		
		$this->_addButton('saveandcontinue',
			array(
				'label'		=> $this->__('Save and continue edit'), 
				'onclick'	=> 'saveAndContinueEdit()', 
				'class'		=> 'save'
			),
			-100
		);
		
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	
	public function getHeaderText()
	{
		if (Mage::registry('flashmagazine_template')->getId()) {
			return $this->__("Edit Template '%s'", $this->htmlEscape(Mage::registry('flashmagazine_template')->getName()));
		} else {
			return $this->__('New Template');
		}
	}

	public function getHeaderCssClass()
	{
		return '';
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $action
	 * @return bool
	 */
	protected function _isAllowedAction($action)
	{
		return Mage::getSingleton('admin/session')->isAllowed('flashmagazine/template/' . $action);
	}
}
