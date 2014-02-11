<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Model_Mysql4_Template_Type extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/template_type', 'type_id');
	}

	/**
	 * Retrieves template type title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getTypeNameById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'type_name')
			->where('main_table.type_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
