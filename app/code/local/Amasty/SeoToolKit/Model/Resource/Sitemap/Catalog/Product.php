<?php

class Amasty_SeoToolKit_Model_Resource_Sitemap_Catalog_Product extends Mage_Sitemap_Model_Resource_Catalog_Product
{
	/**
	 * Get product collection array
	 *
	 * @param int $storeId
	 * @return array
	 */
	public function getCollection($storeId)
	{
		/** @var Amasty_SeoToolKit_Helper_Data $helper */
		$helper = Mage::helper('amseotoolkit');
		if ($helper::urlRewriteHelperEnabled() || $helper->useDefaultProductUrlRules()) {
			return parent::getCollection($storeId);
		}

		$products = array();

		/* @var $store Mage_Core_Model_Store */
		$store = Mage::app()->getStore($storeId);
		if (!$store) {
			return false;
		}

		$this->_select = $this->_getWriteAdapter()->select()
			->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
			->join(
				array('w' => $this->getTable('catalog/product_website')),
				'main_table.entity_id=w.product_id',
				array()
			)
			->where('w.website_id=?', $store->getWebsiteId());

		/** @var Amasty_SeoToolKit_Helper_Product_Url_Rewrite $helper */
		$helper = Mage::helper('amseotoolkit/product_url_rewrite');
		$helper->joinTableToSelect($this->_select, $storeId);

		$this->_addFilter($storeId, 'visibility', Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in');
		$this->_addFilter($storeId, 'status', Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in');

		$query = $this->_getWriteAdapter()->query($this->_select);
		while ($row = $query->fetch()) {
			$product = $this->_prepareProduct($row);
			$products[$product->getId()] = $product;
		}

		return $products;

	}
}
