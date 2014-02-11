<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Adminhtml_FlashmagazineController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Redirect to flashmagazine category index page.
	 */
	public function indexAction()
	{
		$this->_redirect('*/flashmagazine_category/index');
	}

	/**
	 * Get magazine grid for attach to product
	 */
	public function productAction()
	{
		echo $this->getLayout()->createBlock('flashmagazine/adminhtml_catalog_product_edit_tab_magazine_grid')->toHtml();
	}

	public function attachAction()
	{
		$product_id			= (int)$this->getRequest()->getParam('product_id');
		$magazine_id		= (array)$this->getRequest()->getParam('magazine_id');
		$product_attached	= (int)$this->getRequest()->getParam('product_attached');

		$message = $this->_attach($product_id, $magazine_id, $product_attached);

		die($message);
	}

	public function massAttachAction()
	{
		$product_id = $this->getRequest()->getParam('id');
		$attachtableIds = $this->getRequest()->getParam('attachtable');
		if (!is_array($attachtableIds)) {
			$message = $this->__('Please select book(s)');
		} else {
			$message = $this->_attach($product_id, $attachtableIds, 0);
		}

		die($message);
	}

	public function massDetachAction()
	{
		$product_id = $this->getRequest()->getParam('id');
		$attachtableIds = $this->getRequest()->getParam('attachtable');
		if (!is_array($attachtableIds)) {
			$message = $this->__('Please select book(s)');
		} else {
			$message = $this->_attach($product_id, $attachtableIds, 1);
		}

		die($message);
	}

	protected function _attach($product_id, $magazine_ids, $action_type)
	{
		if($action_type) {
			$method_name = 'detachMagazines';
			$message = $this->__('Total of %d record(s) were detached', count($magazine_ids));
		} else {
			$method_name = 'attachMagazines';
			$message = $this->__('Total of %d record(s) were attached', count($magazine_ids));
		}

		try {
			Mage::getResourceModel('flashmagazine/magazine')->$method_name($product_id, $magazine_ids);
		} catch(Exception $e) {
			$message = $e->getMessage();
		}
	}

	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to attach book to a product
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/flashmagazine/attach_magazine');
	}
}
