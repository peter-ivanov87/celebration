<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Adminhtml_Flashmagazine_CategoryController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Mageplace_Flashmagazine_Adminhtml_Flashmagazine_CategoryController
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'flashmagazine';

		$this->loadLayout()
			->_setActiveMenu('flashmagazine/category')
			->_title($this->__('Flash Flipping Book'))
			->_addBreadcrumb($this->__('Flash Flipping Book'), $this->__('Flash Flipping Book'));

		return $this;
	}

	/**
	 * Displays the categories overview grid.
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		} 

		$this->_initAction()
			->_title($this->__('Manage Categories'))
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_category'))
			->renderLayout();
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('flashmagazine/adminhtml_category_grid')->toHtml()
		);
	} 

	/**
	 * Displays the new category form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new category form or the edit category form.
	 */
	public function editAction()
	{
		$model = Mage::getModel('flashmagazine/category');

		$id = $this->getRequest()->getParam('category_id');
		if($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError($this->__('This category does not exist'));
				$this->_redirect('*/*/');
				return;
			}
		}

		$data = $this->_getSession()->getFormData(true);
		if(!empty($data)) {
			$model->setData($data);
		}

		Mage::register('flashmagazine_category', $model);

		$title = $id ? $this->__('Edit Category') : $this->__('New Category');
		$this->_initAction()
			->_title($title)
			->_addBreadcrumb($title, $title)
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_category_edit'))
			->renderLayout();
	}

	/**
	 * Action that does the actual saving process and redirects back to overview
	 */
	public function saveAction()
	{
		if($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('flashmagazine/category');
			$model->setData($data);

			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Category was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array ('category_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array ('category_id' => $this->getRequest()->getParam('category_id')));
				return;
			}
		}

		$this->_redirect('*/*/');
	}

	/**
	 * Action that does the actual delete process and redirects back to overview
	 */
	public function deleteAction()
	{
		if($id = $this->getRequest()->getParam('category_id')) {
			try {
				$model = Mage::getModel('flashmagazine/category');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Category was successfully deleted'));
				$this->_redirect('*/*/');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('category_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Category to delete'));

		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('categorytable');
		if (!is_array($ids)) {
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('flashmagazine/category')->load($id);
					$item->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', count($ids))
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/index');
	}  

	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to edit categories
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/flashmagazine/category');
	}
}
