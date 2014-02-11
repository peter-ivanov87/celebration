<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('ammeta/config', 'config_id');
	}

	public function ifStoreConfigExists(Amasty_Meta_Model_Config $item)
	{
		$storeId = $this->getReadConnection()->quote($item->getStoreId());

		$sql = "SELECT COUNT(`config_id`)
				FROM `{$this->getMainTable()}`
				WHERE `store_id` = $storeId";

		if ($item->getCategoryId()) {
			$categoryId = $this->getReadConnection()->quote($item->getCategoryId());

			$sql .= " AND `category_id` = $categoryId
					AND `is_custom` = false";

		} else {
			$url = $this->getReadConnection()->quote($item->getCustomUrl());

			$sql .= " AND `custom_url` = $url
					AND `is_custom` = true";
		}

		if ($item->getId()) {
			$id = $this->getReadConnection()->quote($item->getId());
			$sql .= " AND `{$this->getIdFieldName()}` <> $id";
		}

		return $this->getReadConnection()->fetchOne($sql) > 0;
	}

	public function getRecursionConfigData($categoryIds, $storeId)
	{
		if (empty($categoryIds)) {
			return false;
		}

		//add root category to filter
		$categoryIds[] = 0;

		$storeId = (int) $storeId;
		$sqlMask = "SELECT * FROM
						`{$this->getMainTable()}` cnf
					WHERE cnf.store_id IN ($storeId, 0) AND category_id = %d AND is_custom = false";

		$sqlParts = array();

		$generateSqlParts = function ($categoryIds) use (&$sqlParts, $sqlMask, &$generateSqlParts) {
			foreach ($categoryIds as $itemCategory) {
				if (is_array($itemCategory)) {
					$generateSqlParts($itemCategory);
				} else {
					$sqlParts[] = sprintf($sqlMask, $itemCategory);
				}
			}
		};
		$generateSqlParts($categoryIds);

		$sql = implode(' UNION ALL ', $sqlParts);
		$sql .= ' ORDER BY store_id DESC';

		return $this->getReadConnection()->fetchAll($sql);
	}

}