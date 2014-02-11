<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Adminhtml_Flashmagazine_PageController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Mageplace_Flashmagazine_Adminhtml_Flashmagazine_PageController
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'flashmagazine';

		$this->loadLayout()
			->_setActiveMenu('flashmagazine/page')
			->_title($this->__('Flash Flipping Book'))
			->_addBreadcrumb($this->__('Flash Flipping Book'), $this->__('Flash Flipping Book'));

		return $this;
	}

	/**
	 * Displays the pages overview grid.
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		} 

		$this->_initAction()
			->_title($this->__('Manage Pages'))
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_page'))
			->renderLayout();
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('flashmagazine/adminhtml_page_grid')->toHtml()
		);
	} 

	/**
	 * Displays the new page form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new page form or the edit page form.
	 */
	public function editAction()
	{
		$model = Mage::getModel('flashmagazine/page');
		$magazine_model = Mage::getModel('flashmagazine/magazine');

		$id = $this->getRequest()->getParam('page_id');
		if($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError($this->__('This page does not exist'));
				$this->_redirect('*/*/');
				return;
			}

			$magazine_model->load($model->getPageMagazineId());
		}

		$data = $this->_getSession()->getFormData(true);
		if(!empty($data)) {
			$data['page_sound']			= $model->getData('page_sound');
			$data['page_image']			= $model->getData('page_image');
			$data['page_zoom_image']	= $model->getData('page_zoom_image');
			$data['page_video']			= $model->getData('page_video');

			$model->setData($data);
		}

		Mage::register('flashmagazine_page', $model);
		Mage::register('flashmagazine_magazine', $magazine_model);

		$title = $id ? $this->__('Edit Page') : $this->__('New Page');
		$this->_initAction()
			->_title($title)
			->_addBreadcrumb($title, $title)
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit'))
			->_addLeft($this->getLayout()->createBlock('flashmagazine/adminhtml_page_edit_tabs'))
			->renderLayout();
	}

	/**
	 * Action that does the actual saving process and redirects back to overview
	 */
	public function saveAction()
	{
		if($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('flashmagazine/page');
			$model->setData($data);

			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Page was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array ('page_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array ('page_id' => $this->getRequest()->getParam('page_id')));
				return;
			}
		}

		$this->_redirect('*/*/');
	}
	
	public function enableAction()
	{
		if($id = $this->getRequest()->getParam('page_id')) {
			try {
				$model = Mage::getModel('flashmagazine/page');
				$model->load($id);				
				$model->setIsActive(!$model->getIsActive());
				$model->save();

				$this->_getSession()->addSuccess($this->__('Book was successfully enabled/disabled'));
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('page_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Book to enable/disable'));

		$this->_redirect('*/*/');
	}
	
	public function massEnableAction()
	{
		$ids = $this->getRequest()->getParam('pagetable');
		if (!is_array($ids)) {
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$model = Mage::getModel('flashmagazine/page');
					$model->load($id);				
					$model->setIsActive(!$model->getIsActive());
					$model->save();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('flashmagazine')->__('Total of %d record(s) were enabled/disabled', count($ids))
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/index');
	}  

	/**
	 * Action that does the actual delete process and redirects back to overview
	 */
	public function deleteAction()
	{
		if($id = $this->getRequest()->getParam('page_id')) {
			try {
				$model = Mage::getModel('flashmagazine/page');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Page was successfully deleted'));
				$this->_redirect('*/*/');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('page_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Page to delete'));

		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('pagetable');
		if (!is_array($ids)) {
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('flashmagazine/page')->load($id);
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
	 * @return boolean True if user is allowed to edit pages
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/flashmagazine/page');
	}
}
