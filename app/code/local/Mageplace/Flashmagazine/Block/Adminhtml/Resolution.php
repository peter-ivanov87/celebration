<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Resolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor for Adminhtml Resolution Block
	 */
	public function __construct()
	{
		$this->_blockGroup = 'flashmagazine';
		$this->_controller = 'adminhtml_resolution';

		$this->_addButtonLabel = $this->__('Add New Resolution');
		
		parent::__construct();
	}

	public function getHeaderText()
	{
		return $this->__('Manage Resolutions');
	}
	
    /**
     * Returns the CSS class for the header
     * 
     * Usually 'icon-head' and a more precise class is returned. We return
     * only an empty string to avoid spacing on the left of the header as we
     * don't have an icon.
     * 
     * @return string
     */
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
