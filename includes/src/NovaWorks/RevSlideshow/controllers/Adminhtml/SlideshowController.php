<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Adminhtml_SlideshowController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('surgethemes/revslideshow');
		$this->renderLayout();
	}
	
	/**
	 * Display the slideshow grid
	 *
	 */
	public function gridAction()
	{
		$this->getResponse()
			->setBody($this->getLayout()->createBlock('revslideshow/adminhtml_slideshow_grid')->toHtml());
	}
	
	/**
	 * Forward to the edit action so the user can add a new slideshow
	 *
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	/**
	 * Display the edit/add form
	 *
	 */
	public function editAction()
	{
		$slideshow = $this->_initSlideshowModel();
		$this->loadLayout();
		
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$titles = array('RevSlideshow');
			
			if ($slideshow) {
				array_unshift($titles, $slideshow->getTitle());
			}
			else {
				array_unshift($titles, 'Create a Slideshow');
			}

			$headBlock->setTitle(implode(' - ', $titles));
		}

		$this->renderLayout();
	}
	
	/**
	 * Save the slideshow
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('slideshow')) {
			$slideshow = Mage::getModel('revslideshow/slideshow')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				
				$this->_handleImageUpload($slideshow);
				
				$slideshow->save();
				$this->_getSession()->addSuccess($this->__('Slideshow was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $slideshow->getId()) {
				$this->_redirect('*/*/edit', array('id' => $slideshow->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save'));
		}
		
		$this->_redirect('*/*');
	}
	/**
	 * Manage Slide Captions
	 *
	 */
	public function slidecaptionsAction()
	{
		$slideshow = $this->_initSlideshowModel();
		$this->loadLayout();
		
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$titles = array('Slide Captions');
			
			if ($slideshow) {
				array_unshift($titles, $slideshow->getTitle());
			}

			$headBlock->setTitle(implode(' - ', $titles));
		}
		$this->renderLayout();
	}
	public function savecaptionsAction()
	{
		$json = $this->getRequest()->getPost('json');
		if ($json) {
			try {				

			    $connection = Mage::getSingleton('core/resource')
			    ->getConnection('core_write');
			    $connection->beginTransaction();
			    $fields = array();
			    $fields['json'] = $json;
			    $where = $connection->quoteInto('slideshow_id =?', $this->getRequest()->getParam('id'));
			    $connection->update('revslideshow_slideshow', $fields, $where);
			    $connection->commit();
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $slideshow->getId()) {
				$this->_redirect('*/*/edit', array('id' => $slideshow->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save'));
		}
		
		$this->_redirect('*/*');
	}
	/**
	 * Upload an image and assign it to the model
	 *
	 * @param NovaWorks_RevSlideshow_Model_Slideshow $slideshow
	 * @param string $field = 'image'
	 */
	protected function _handleImageUpload(NovaWorks_RevSlideshow_Model_Slideshow $slideshow, $field = 'image')
	{
		$data = $slideshow->getData($field);

		if (isset($data['value'])) {
			$slideshow->setData($field, $data['value']);
		}

		if (isset($data['delete']) && $data['delete'] == '1') {
			$slideshow->setData($field, '');
		}

		if ($filename = Mage::helper('revslideshow/image')->uploadImage($field)) {
			$slideshow->setData($field, $filename);
		}
	}
	
	/**
	 * Delete a revslideshow slideshow
	 *
	 */
	public function deleteAction()
	{
		if ($slideshowId = $this->getRequest()->getParam('id')) {
			$slideshow = Mage::getModel('revslideshow/slideshow')->load($slideshowId);
			
			if ($slideshow->getId()) {
				try {
					$slideshow->delete();
					$this->_getSession()->addSuccess($this->__('The slideshow was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/*');
	}
	
	/**
	 * Delete multiple revslideshow slideshows in one go
	 *
	 */
	public function massDeleteAction()
	{
		$slideshowIds = $this->getRequest()->getParam('slideshow');

		if (!is_array($slideshowIds)) {
			$this->_getSession()->addError($this->__('Please select some slideshows.'));
		}
		else {
			if (!empty($slideshowIds)) {
				try {
					foreach ($slideshowIds as $slideshowId) {
						$slideshow = Mage::getSingleton('revslideshow/slideshow')->load($slideshowId);
	
						Mage::dispatchEvent('revslideshow_controller_slideshow_delete', array('revslideshow_slideshow' => $slideshow));
	
						$slideshow->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($slideshowIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/*');
	}
	
	/**
	 * Initialise the slideshow model
	 *
	 * @return null|NovaWorks_RevSlideshow_Model_Slideshow
	 */
	protected function _initSlideshowModel()
	{
		if ($slideshowId = $this->getRequest()->getParam('id')) {
			$slideshow = Mage::getModel('revslideshow/slideshow')->load($slideshowId);
			
			if ($slideshow->getId()) {
				Mage::register('revslideshow_slideshow', $slideshow);
			}
		}
		
		return Mage::registry('revslideshow_slideshow');
	}
	public function ajaxuploadthumbAction(){
		$path = str_replace("/",DS, Mage::getBaseDir("media").DS)."/revslideshow/thumb/";
		$output  = Mage::getBaseUrl('media')."revslideshow/thumb/";
		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		{
		$name = $_FILES['photoimg']['name'];
		$size = $_FILES['photoimg']['size'];
		if(strlen($name))
		{
		list($txt, $ext) = explode(".", $name);
		if(in_array($ext,$valid_formats))
		{
		if($size<(1024*1024)) // Image size max 1 MB
		{
		$actual_image_name = time().".".$ext;
		$tmp = $_FILES['photoimg']['tmp_name'];
		if(move_uploaded_file($tmp, $path.$actual_image_name))
		{
		echo "<img src='".$output.$actual_image_name."'>";
		}
		else
		echo "failed";
		}
		else
		echo "Image file size max 1 MB";
		}
		else
		echo "Invalid file format..";
		}
		else
		echo "Please select image..!";
		exit;
		}
	}
}