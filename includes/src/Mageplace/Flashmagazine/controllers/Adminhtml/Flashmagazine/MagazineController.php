<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Adminhtml_Flashmagazine_MagazineController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Mageplace_Flashmagazine_Adminhtml_Flashmagazine_MagazineController
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'flashmagazine';

		$this->loadLayout()
			->_setActiveMenu('flashmagazine/magazine')
			->_title($this->__('Flash Flipping Book'))
			->_addBreadcrumb($this->__('Flash Flipping Book'), $this->__('Flash Flipping Book'));

		return $this;
	}

	/**
	 * Displays the magazines overview grid.
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		} 

		$this->_initAction()
			->_title($this->__('Manage Books'))
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_magazine'))
			->renderLayout();
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_grid')->toHtml()
		);
	} 

	/**
	 * Displays the new magazine form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new magazine form or the edit magazine form.
	 */
	public function editAction()
	{
		$model = Mage::getModel('flashmagazine/magazine');

		$id = $this->getRequest()->getParam('magazine_id');
		if($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError($this->__('This book does not exist'));
				$this->_redirect('*/*/index');
				return;
			}
		}

		$data = $this->_getSession()->getFormData(true);
		if(!empty($data)) {
			$data['magazine_thumb']				= $model->getData('magazine_thumb');
			$data['magazine_background_pdf']	= $model->getData('magazine_background_pdf');
			$data['magazine_background_sound']	= $model->getData('magazine_background_sound');
			$data['magazine_flip_sound']		= $model->getData('magazine_flip_sound');
			$data['magazine_author_image']		= $model->getData('magazine_author_image');
			$data['magazine_author_logo']		= $model->getData('magazine_author_logo');

			$model->setData($data);
		}

		Mage::register('flashmagazine_magazine', $model);

		$title = $id ? $this->__('Edit Book') : $this->__('New Book');
		$this->_initAction()
			->_title($title)
			->_addBreadcrumb($title, $title)
			->_addContent($this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit'))
			->_addLeft($this->getLayout()->createBlock('flashmagazine/adminhtml_magazine_edit_tabs'))
			->renderLayout();
	}

	/**
	 * Action that does the actual saving process and redirects back to overview
	 */
	public function saveAction()
	{
		if($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('flashmagazine/magazine');
			$model->setData($data);

			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Book was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array ('magazine_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array ('magazine_id' => $this->getRequest()->getParam('magazine_id')));
				return;
			}
		}

		$this->_redirect('*/*/index');
	}

	public function enableAction()
	{
		if($id = $this->getRequest()->getParam('magazine_id')) {
			try {
				$model = Mage::getModel('flashmagazine/magazine');
				$model->load($id);				
				$model->setIsActive(!$model->getIsActive());
				$model->save();

				$this->_getSession()->addSuccess($this->__('Book was successfully enabled/disabled'));
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('magazine_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Book to enable/disable'));

		$this->_redirect('*/*/');
	}
	
	public function massEnableAction()
	{
		$ids = $this->getRequest()->getParam('magazinetable');
		if (!is_array($ids)) {
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$model = Mage::getModel('flashmagazine/magazine');
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
		if($id = $this->getRequest()->getParam('magazine_id')) {
			try {
				$model = Mage::getModel('flashmagazine/magazine');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Book was successfully deleted'));
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('magazine_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Book to delete'));

		$this->_redirect('*/*/index');
	}
	
	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('magazinetable');
		if (!is_array($ids)) {
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('flashmagazine/magazine')->load($id);
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
	 * @return boolean True if user is allowed to edit magazines
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/flashmagazine/magazine');
	}
}
