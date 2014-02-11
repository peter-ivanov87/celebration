<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Resolution_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the resolution edit form
	 */
	public function __construct()
	{
		$this->_objectId = 'resolution_id';
		$this->_blockGroup = 'flashmagazine';
		$this->_controller = 'adminhtml_resolution';
		
		parent::__construct();
		
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save Resolution'));
		$this->_updateButton('delete', 'label', $this->__('Delete Resolution'));
		
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
		if (Mage::registry('flashmagazine_resolution')->getId()) {
			return $this->__("Edit Resolution '%s'", $this->htmlEscape(Mage::registry('flashmagazine_resolution')->getName()));
		} else {
			return $this->__('New Resolution');
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
        return Mage::getSingleton('admin/session')->isAllowed('flashmagazine/resolution/' . $action);
    }
}
