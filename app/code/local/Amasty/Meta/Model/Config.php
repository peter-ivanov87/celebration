<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/
class Amasty_Meta_Model_Config extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ammeta/config');
    }

	/**
	 * @return object
	 */
	public function getCollection()
	{
		$collection = $this->getResourceCollection('ammeta/config_collection')
			->addCategoryFilter();

		return $collection;
	}

	/**
	 * @return mixed
	 */
	public function getCustomCollection()
	{
		$collection = $this->getResourceCollection('ammeta/config_collection')
			->addCustomFilter();

		return $collection;
	}

	/**
	 * @param $url
	 * @param null $storeId
	 *
	 * @return mixed
	 */
	public function getConfigByUrl($url, $storeId = null)
	{
		/** @var Amasty_Meta_Model_Mysql4_Config_Collection $collection */
		$collection = $this->getResourceCollection('ammeta/config_collection');

		$collection->addUrlFilter($url, $storeId);
		$collection->getSelect()
			->order("store_id DESC")
			->order("priority DESC");

		return $collection;
	}

	/**
	 * @return Mage_Core_Model_Abstract
	 * @throws Exception
	 */
	protected function _beforeSave()
	{
		$this->setIsCustom($this->getCategoryId() === null);

		if (Mage::app()->isSingleStoreMode()) {
			$storeId = Mage::app()
				->getWebsite()
				->getDefaultGroup()
				->getDefaultStoreId();
			$this->setStoreId($storeId);
		}

		if ($this->getResource()->ifStoreConfigExists($this)) {
			throw new Exception(Mage::helper('ammeta')->__('Template already exists in chosen store'));
		}

		return parent::_beforeSave();
	}

}