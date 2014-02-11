<?php
if (! Amasty_SeoToolKit_Helper_Data::urlRewriteHelperEnabled()) {
	abstract class Amasty_SeoToolKit_Helper_Product_Url_Rewrite_Abstract
	{}
} else {
	abstract class Amasty_SeoToolKit_Helper_Product_Url_Rewrite_Abstract extends Mage_Catalog_Helper_Product_Url_Rewrite
	{}
};

class Amasty_SeoToolKit_Helper_Product_Url_Rewrite
    extends Amasty_SeoToolKit_Helper_Product_Url_Rewrite_Abstract
{
	protected $_productUrlCache = array();

    /**
     * Adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

	/**
	 * @var bool
	 */
	protected $_useDefaultUrlSettings;

	/**
	 * Initialize resource and connection instances
	 *l
	 * @param array $args
	 */
	public function __construct(array $args = array())
	{
		$this->_resource = Mage::getSingleton('core/resource');
		$this->_connection = !empty($args['connection']) ? $args['connection'] : $this->_resource
			->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);

		$this->_useDefaultUrlSettings = Mage::helper('amseotoolkit')->useDefaultProductUrlRules();
	}

	/**
	 * Prepare and return select
	 *
	 * @param array $productIds
	 * @param int $categoryId
	 * @param int $storeId
	 * @param array $items
	 *
	 * @return Varien_Db_Select|Varien_Db_Select
	 */
	public function getTableSelect(array $productIds, $categoryId, $storeId, $items = array('product_id', 'request_path'))
    {
		if ($this->_useDefaultUrlSettings) {
			return parent::getTableSelect($productIds, $categoryId, $storeId);
		}

		$storeId = Mage::registry('amseotoolkit_store_id') ? Mage::registry('amseotoolkit_store_id') : $storeId;
		/*$nestedQuery = $this->_getNestedQuery($storeId, $this->_resource->getTableName('core/url_rewrite'), 'product_id');*/

		$select = $this->_connection->select()
            ->from($this->_resource->getTableName('core/url_rewrite'), $items)
            ->where('store_id = ?', (int) $storeId)
            ->where('is_system = ?', 1)
			->where('category_id IS NOT NULL')
            ->where('product_id IN(?)', $productIds)
			->order('LENGTH(request_path) ' . $this->_getSortOrder());

		Mage::unregister('amseotoolkit_store_id');

        return $select;
    }

	/**
	 * @param Mage_Catalog_Model_Product $product
	 *
	 * @return string
	 */
	public function getProductPath(Mage_Catalog_Model_Product $product)
	{
		if ($this->_useDefaultUrlSettings) {
			return null;
		}

		if (! isset($this->_productUrlCache[$product->getId()])) {
			$select = $this->getTableSelect(
				array($product->getId()),
				null,
				Mage::app()->getStore()->getId(),
				array('request_path')
			)->limit(1);

			$this->_productUrlCache[$product->getId()] = $this->_connection->fetchOne($select);
		}

		return $this->_productUrlCache[$product->getId()];
	}

    /**
     * Prepare url rewrite left join statement for given select instance and store_id parameter.
     *
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
		if ($this->_useDefaultUrlSettings) {
			return parent::joinTableToSelect($select, $storeId);
		}

		$rewriteIds = $this->_connection->fetchCol($this->_getNestedQuery($storeId));

		$select->joinLeft(
			array('url_rewrite' => $this->_resource->getTableName('core/url_rewrite')),
			'url_rewrite.product_id = main_table.entity_id AND ' .
			$this->_connection->quoteInto('url_rewrite.url_rewrite_id IN (?)', $rewriteIds),
			array('request_path' => 'url_rewrite.request_path'));
		return $this;
    }

	/**
	 * @param $storeId
	 * @param array $productIds
	 * @return Varien_Db_Select
	 */
	protected function _getNestedQuery($storeId, $productIds = array())
	{
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $isActive */
		$isActive = Mage::getResourceModel('catalog/category')->getAttribute('is_active');

		$nestedQuery = $this->_connection->select()
			->from(array('nested' => $this->_resource->getTableName('core/url_rewrite')), array('url_rewrite_id', 'product_id'))
			/*->joinLeft(
				array('cce' => $this->_resource->getTableName('catalog/category')),
				'cce.entity_id = nested.category_id',
				array()
			)
			->joinLeft(
				array('att' => $isActive->getBackend()->getTable()),
				$this->_connection->quoteInto('att.' . $isActive->getBackend()->getEntityIdField(
					) . ' = cce.entity_id AND
					att.entity_type_id  = cce.entity_type_id AND att.attribute_id = ?', $isActive->getId()
				),
				array()
			)*/
			->where('nested.store_id = ?', (int) $storeId)
			->where('nested.is_system = ?', 1)
			->where('nested.category_id IS NOT NULL')
			->where('nested.product_id IS NOT NULL')
			/*->where('att.value = 1 OR cce.entity_id IS NULL')*/
			->order('LENGTH(nested.request_path) ' . $this->_getSortOrder());

		if (! empty($productIds)) {
			$nestedQuery->where('nested.product_id IN(?)', $productIds);
		}

		$mainQuery = $this->_connection->select()
			->from(new Zend_Db_Expr('(' . $nestedQuery . ')'), 'url_rewrite_id')
			->group('product_id');

		return $mainQuery;
	}

	protected function _getSortOrder()
	{
		$urlFormat = Mage::helper('amseotoolkit')->getProductUrlType();
		return $urlFormat == Amasty_SeoToolKit_Helper_Data::PRODUCT_URL_PATH_SHORTEST
			? Varien_Data_Collection::SORT_ORDER_ASC
			: Varien_Data_Collection::SORT_ORDER_DESC;
	}
}
