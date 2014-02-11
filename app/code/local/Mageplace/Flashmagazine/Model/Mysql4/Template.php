<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/template', 'template_id');
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param Mage_Core_Model_Abstract $object Current template
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Template
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if(!$object->getId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		$object->setTemplateName(Mage::helper('flashmagazine')->cleanText($object->getTemplateName()));

		return $this;
	}

	/**
	 * Retrieves category title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getTemplateNameById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'template_name')
			->where('main_table.template_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
