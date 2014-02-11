<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Mysql4_Magazine extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_productId;

	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/magazine', 'magazine_id');
	}

	public function setProductId($product_id)
	{
		$this->_productId = $product_id;
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param Mage_Core_Model_Abstract $object Current magazine
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if(!$object->getId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		$object->setMagazineTitle(Mage::helper('flashmagazine')->cleanText($object->getMagazineTitle()));

		$object->setMagazineImgsub(is_null($object->getMagazineImgsub()) ? 0 : 1);
		if(!is_null($object->getMagazineImgsub())) {
			if(!$object->getMagazineImgsubfolder()) {
				$object->setMagazineImgsubfolder(strtolower($object->getMagazineTitle()));
			}

			$object->setMagazineImgsubfolder(preg_replace('/[^a-z0-9\_]/i', '_', $object->getMagazineImgsubfolder()));
		} else {
			$object->setMagazineImgsubfolder('');
		}

		if(!empty($_FILES)) {
			foreach($_FILES as $key=>$file) {
				$config_path = strtolower(preg_replace('/^.*\_/', '', $key));
				if($file['error']===UPLOAD_ERR_OK) {
					$uploader = new Varien_File_Uploader($key);
					if($config_path == 'pdf') {
						$uploader->setAllowedExtensions(array('pdf'));
					} else if($config_path == 'sound') {
						$uploader->setAllowedExtensions(array('mp3', 'wav'));
					} else {
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
					}
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);

					$result = $uploader->save(
						Mage::helper('flashmagazine')->getDir($config_path).DS.$object->getMagazineImgsubfolder()
					);

					if($object->getData($key)) {
						$img_file = $object->getData($key);

						if(!empty($img_file['value']) && !empty($img_file['delete'])) {
							$img_file_path = str_replace(Mage::helper('flashmagazine')->getPathUrl($config_path).'/', '', strval($img_file['value']));
							@unlink(Mage::helper('flashmagazine')->getDir($config_path).DS.$img_file_path);
						}

					}

					$object->setData($key, $object->getMagazineImgsubfolder().$result['file']);

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


		return $this;
	}

	/**
	 * Do store processing after magazine save
	 *
	 * @param Mage_Core_Model_Abstract $object Current magazine
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$condition = $this->_getWriteAdapter()->quoteInto('magazine_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('flashmagazine/magazine_store'), $condition);

		foreach((array) $object->getData('stores') as $store) {
			$this->_getWriteAdapter()
				->insert(
					$this->getTable('flashmagazine/magazine_store'),
					array(
						'magazine_id'	=> $object->getId(),
						'store_id'		=> $store
					)
				);
		}

		return $this;
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param   string $field
	 * @param   mixed $value
	 * @return  Zend_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$selest = parent::_getLoadSelect($field, $value, $object);

		$selest->join(
				array(
					'resolution_table' => $this->getTable('flashmagazine/resolution')
				),
				$this->getMainTable().'.magazine_resolution_id = resolution_table.resolution_id',
				array('resolution_width', 'resolution_height')
			)->join(
				array(
					'template_table' => $this->getTable('flashmagazine/template')
				),
				$this->getMainTable().'.magazine_template_id = template_table.template_id'
			)->join(
				array(
					'template_type_table' => $this->getTable('flashmagazine/template_type')
				),
				'template_table.template_type_id = template_type_table.type_id'
			);

		return $selest;
	}

	/**
	 * Do store processing after loading
	 *
	 * @param Mage_Core_Model_Abstract $object Current magazine
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('flashmagazine/magazine_store')
			)->where(
				'magazine_id = ?',
				$object->getId()
			);

		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$stores = array();
			foreach ($data as $row) {
				$stores[] = $row['store_id'];
			}

			$object->setData('store_id', $stores);
		}

		if($this->_productId) {
			$select = $this->_getReadAdapter()
				->select()
				->from(
					$this->getTable('flashmagazine/product_magazine')
				)->where(
					'entity_id = ?',
					$this->_productId
				)->where(
					'magazine_id = ?',
					$object->getId()
				);

			if ($data = $this->_getReadAdapter()->fetchRow($select)) {
				$object->setData('product_attached', 1);
			}
		}

		return $this;
	}


	/**
	 * Attach magazine to product
	 *
	 * @param string $id Product id
	 * @param array $magazine_ids Magazine's ids
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine
	 */
	public function attachMagazines($product_id, $magazine_ids)
	{
		if(empty($product_id) || empty($magazine_ids)) {
			return $this;
		}

		foreach((array) $magazine_ids as $magazine_id) {
			$this->_getWriteAdapter()
				->insert(
					$this->getTable('flashmagazine/product_magazine'),
					array(
						'magazine_id'	=> $magazine_id,
						'entity_id'		=> $product_id
					)
				);
		}

		return $this;
	}

	/**
	 * Detach magazine from product
	 *
	 * @param string $id Product id
	 * @param array $magazine_ids Magazine's ids
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine
	 */
	public function detachMagazines($product_id, $magazine_ids)
	{
		if(empty($product_id) || empty($magazine_ids)) {
			return $this;
		}

		foreach((array) $magazine_ids as $magazine_id) {
			$this->_getWriteAdapter()->delete(
				$this->getTable('flashmagazine/product_magazine'),
				array(
					$this->_getWriteAdapter()->quoteInto('entity_id = ?', $product_id),
					$this->_getWriteAdapter()->quoteInto('magazine_id = ?', $magazine_id)
				)
			);
		}

		return $this;
	}

	/**
	 * Retrieves magazine title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getMagazineNameById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'magazine_title')
			->where('main_table.magazine_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
