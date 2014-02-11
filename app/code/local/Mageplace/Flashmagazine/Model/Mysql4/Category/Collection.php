<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/category');
	}
		
	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('category_id', 'category_name');
	}
	
	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('category_id', 'category_name');
	}

	public function addIsActiveFilter()
	{
		$this->addFilter('is_active', 1);
		return $this;
	}
}
