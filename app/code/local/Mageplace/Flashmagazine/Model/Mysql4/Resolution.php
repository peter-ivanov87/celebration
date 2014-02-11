<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Model_Mysql4_Resolution extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/resolution', 'resolution_id');
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param Mage_Core_Model_Abstract $object Current resolution
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Resolution
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if(!$object->getId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		$object->setResolutionName(Mage::helper('flashmagazine')->cleanText($object->getResolutionName()));
		
		return $this;
	}

	/**
	 * Retrieves category title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getResolutionNameById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'resolution_name')
			->where('main_table.resolution_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
