<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Mysql4_Page_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/page');
	}

	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('page_id', 'page_name');
	}

	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('page_id', 'page_name');
	}

	/**
	 * Add Filter by template type
	 *
	 * @param int|Mageplace_Flashmagazine_Model_Magazine|Mageplace_Flashmagazine_Model_Page $magazine Magazine to be filtered
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine_Collection
	 */
	public function addMagazineFilter($magazine)
	{
		if ($magazine instanceof Mageplace_Flashmagazine_Model_Magazine) {
			$magazine = $magazine->getId();
		} else if ($magazine instanceof Mageplace_Flashmagazine_Model_Page) {
			$magazine = $magazine->getMagazineId();
		}

		$magazine = (int)$magazine;

		$select = $this->getSelect()
			->join(
					array(
						'magazine_table' => $this->getTable('flashmagazine/magazine')
					),
					'main_table.page_magazine_id = magazine_table.magazine_id',
					array()
				)
			->where(
				'magazine_table.magazine_id IN (?)',
				array (
					0,
					$magazine
				)
			);

		return $this;
	}

	/**
	 * Add Filter by active state
	 *
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Magazine_Collection
	 */
	public function addIsActiveFilter()
	{
		return $this->addFilter('main_table.is_active', 1);
	}

	public function setOrderByPosition($direction = parent::SORT_ORDER_ASC)
	{
		return $this->addOrder('main_table.page_sort_order', $direction);
	}
}
