<?php

class Amasty_SeoToolKit_Model_Data
{
	/** @var int */
	protected $_storeId;

	public function __construct()
	{
		$this->_storeId = Mage::app()->getStore()->getId();
	}

	/**
	 * Product collection
	 *
	 * @param null $storeId
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getProductCollection($storeId = null)
	{
		/* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
		$collection = Mage::getModel('catalog/product')->getCollection();

		$collection->addAttributeToSelect('name');
		$collection->addAttributeToSelect('url_key');
		$collection->addAttributeToSelect('thumbnail');
		$collection->addAttributeToSelect('thumbnail_label');
		$collection->addAttributeToSelect('url_path');
		$collection->addStoreFilter($storeId ? $storeId : $this->_storeId);
		$collection->addUrlRewrite();
		if ($storeId) {
			$collection->setStoreId($storeId);
			Mage::register('amseotoolkit_store_id', $storeId);
		}

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

		return $collection;
	}

	/**
	 * @return Mage_Catalog_Model_Resource_Category_Collection
	 */
	public function getCategoryCollection()
	{
		/** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
		$collection = Mage::getResourceModel('catalog/category_collection');

		$collection
			->addAttributeToSelect('url_key')
			->addAttributeToSelect('name')
			->addAttributeToSelect('thumbnail')
			->setStoreId($this->_storeId)
			->addAttributeToFilter('level', array('gt' => 1))
			;

		$collection->addAttributeToFilter('is_active', 1);

		$urCondions = array(
			'e.entity_id=ur.category_id',
			'ur.product_id IS NULL',
			'ur.store_id= ' . $this->_storeId,
			'ur.is_system = 1'
		);

		$collection->getSelect()->joinLeft(
			array('ur' => Mage::getSingleton('core/resource')->getTableName('core/url_rewrite')),
			join(' AND ', $urCondions),
			array('url' => 'request_path')
		);

		return $collection;
	}

}