<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Page extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/page');
	}

	public function getName()
	{
		return $this->getPageTitle();
	}

	public function saveMultiupload($post, $magazine, $files_ids, $allowed_extensions)
	{
		$page_dir = Mage::helper('flashmagazine')->getDir('page');
		$magazine_page_dir = $page_dir.DS.$magazine->getMagazineImgsubfolder();
		$page_counter = 1;
		foreach($files_ids as $files_id) {
			$this->unsetData();

			$uploader = new Mageplace_Flashmagazine_Model_File_Uploader($files_id);
			$uploader->setAllowedExtensions($allowed_extensions);
			$uploader->setAllowRenameFiles(true);
			$uploader->setFilesDispersion(true);
			$uploader->setFilesUploadMode(empty($post['delete_files']) ? 'copy' : 'rename');

			$result = $uploader->save($magazine_page_dir);

			if(!empty($result['path']) && !empty($result['file'])) {
				try {
					$size = $magazine->getResolutionWidth().'x'.$magazine->getResolutionHeight();
					$imageModel = Mage::getModel('flashmagazine/product_image');
					$imageModel->setSuffix('-'.$size)
						->setSize($size)
						->setBaseFile($result['path'].$result['file'])
						->resize()
						->saveFile();
				} catch( Exception $e ) {
					$this->_getSession()->addError($e->getMessage());
					$this->_redirect('*/*/');
					return;
				}
			}

			try {
				$this->setMultiupload(true)
					->setData('page_magazine_id', $magazine->getId())
					->setData('page_title', $post['page_title'].' '.$page_counter)
					->setData('page_type', 'Image')
					//TODO: page_sort_order - set a max number of all pages for current book, but not a counter
					->setData('page_sort_order', $page_counter)
					->setData('page_image', str_replace($page_dir.DS, '', $imageModel->getNewFile()))
					->setData('page_zoom_image', str_replace($page_dir.DS, '', $imageModel->getBaseFile()))
					->save();
			} catch( Exception $e ) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/');
				return;
			}

			$page_counter++;
		}

		$this->setData('saved_pages', $page_counter-1);

		return $this;
	}
}
