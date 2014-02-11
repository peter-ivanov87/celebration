<?php

class Amasty_SeoToolKit_Model_Resource_Reports_Product_Index_Viewed_Collection
    extends Mage_Reports_Model_Resource_Product_Index_Viewed_Collection
{
	protected function _addUrlRewrite()
	{
		/** @var Amasty_SeoToolKit_Helper_Data $helper */
		$helper = Mage::helper('amseotoolkit');
		if ($helper::urlRewriteHelperEnabled() || $helper->useDefaultProductUrlRules()) {
			parent::_addUrlRewrite();
		} else {
			$urlRewrites = null;
			if ($this->_cacheConf) {
				if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'] . 'urlrewrite'))) {
					$urlRewrites = null;
				} else {
					$urlRewrites = unserialize($urlRewrites);
				}
			}

			if (!$urlRewrites) {
				$productIds = array();
				foreach($this->getItems() as $item) {
					$productIds[] = $item->getEntityId();
				}
				if (!count($productIds)) {
					return;
				}

				/** @var Amasty_SeoToolKit_Helper_Product_Url_Rewrite $helper */
				$helper = Mage::helper('amseotoolkit/product_url_rewrite');
				$storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
				$select = $helper
					->getTableSelect($productIds, $this->_urlRewriteCategory, $storeId);

				foreach ($this->getConnection()->fetchAll($select) as $row) {
					if (!isset($urlRewrites[$row['product_id']])) {
						$urlRewrites[$row['product_id']] = $row['request_path'];
					}
				}

				if ($this->_cacheConf) {
					Mage::app()->saveCache(
						serialize($urlRewrites),
						$this->_cacheConf['prefix'] . 'urlrewrite',
						array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
						$this->_cacheLifetime
					);
				}
			}

			foreach($this->getItems() as $item) {
				if (isset($urlRewrites[$item->getEntityId()])) {
					$item->setData('request_path', $urlRewrites[$item->getEntityId()]);
				} else {
					$item->setData('request_path', false);
				}
			}
		}
	}
}
