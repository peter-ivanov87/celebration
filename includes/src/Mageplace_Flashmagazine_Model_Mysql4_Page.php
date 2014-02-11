<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/page', 'page_id');
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param Mage_Core_Model_Abstract $object Current page
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Page
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if(!$object->getId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		$object->setPageTitle(Mage::helper('flashmagazine')->cleanText($object->getPageTitle()));

		if(!empty($_FILES) && !$object->getMultiupload()) {
			$magazine_model = Mage::getModel('flashmagazine/magazine')->load($object->getPageMagazineId());

			foreach($_FILES as $key=>$file) {
				$config_path = strtolower(preg_replace('/^.*\_/', '', $key));
				$config_path = $config_path == 'image' ? 'page' : $config_path;
				if($file['error']===UPLOAD_ERR_OK) {
					$uploader = new Varien_File_Uploader($key);
					if($config_path == 'video') {
						$uploader->setAllowedExtensions(array('flv'));
					} else if($config_path == 'sound') {
						$uploader->setAllowedExtensions(array('mp3', 'wav'));
					} else {
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
					}
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);

					$result = $uploader->save(
						Mage::helper('flashmagazine')->getDir($config_path).DS.$magazine_model->getMagazineImgsubfolder()
					);

					if($object->getData($key)) {
						$img_file = $object->getData($key);

						if(!empty($img_file['value']) && !empty($img_file['delete'])) {
							$img_file_path = str_replace(Mage::helper('flashmagazine')->getPathUrl($config_path).'/', '', strval($img_file['value']));
							@unlink(Mage::helper('flashmagazine')->getDir($config_path).DS.$img_file_path);
						}
					}

					$object->setData($key, $magazine_model->getMagazineImgsubfolder().$result['file']);

				} else if($file['error']===UPLOAD_ERR_NO_FILE) {
					$img_file = $object->getData($key);

					if(!empty($img_file['value'])) {
						$img_file_path = str_replace(Mage::helper('flashmagazine')->getPathUrl($config_path).'/', '', strval($img_file['value']));

						if(empty($img_file['delete'])) {
							$img_file = $img_file_path;
						} else {
							@unlink(Mage::helper('flashmagazine')->getDir($config_path).DS.$img_file_path);
							$img_file = '';
						}

					} else {
						$img_file = '';
					}


					$object->setData($key, $img_file);
				}
			}
		}

		switch($object->getPageType()) {
			case 'Image':
				if(!$object->getData('page_image')) {
					throw new Exception(Mage::helper('flashmagazine')->__('Please select an image file.'), 0);
				}
			break;

			case 'Video':
				if(!$object->getData('page_video')) {
					throw new Exception(Mage::helper('flashmagazine')->__('Please select a video file.'), 0);
				}
			break;

			case 'Text':
				if(!$object->getData('page_text')) {
					throw new Exception(Mage::helper('flashmagazine')->__('Please enter a page text.'), 0);
				}
			break;

			default:
				throw new Exception(Mage::helper('flashmagazine')->__('Please select a page content.'), 0);
		}

		return $this;
	}

	/**
	 * Retrieves category title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getPageNameById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'page_title')
			->where('main_table.page_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
