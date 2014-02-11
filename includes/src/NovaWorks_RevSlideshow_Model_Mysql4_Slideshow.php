<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Model_Mysql4_Slideshow extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('revslideshow/slideshow', 'slideshow_id');
	}
	
	/**
	 * Logic performed before saving the model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return NovaWorks_RevSlideshow_Model_Mysql4_Slideshow
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{	
		return parent::_beforeSave($object);
	}
	/**
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
	    $condition = $this->_getWriteAdapter()->quoteInto('slideshow_id = ?', $object->getId());
	    $this->_getWriteAdapter()->delete($this->getTable('revslideshow/stores'), $condition);
	
	    foreach ((array)$object->getData('stores') as $store) {
	        $storeArray = array();
	        $storeArray['slideshow_id'] = $object->getId();
	        $storeArray['store_id'] = $store;
	        $this->_getWriteAdapter()->insert($this->getTable('revslideshow/stores'), $storeArray);
	    }
	
	    return parent::_afterSave($object);
	 }
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }

        return parent::_afterLoad($object);
    }
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('revslideshow_stores' => $this->getTable('revslideshow/stores')),
                $this->getMainTable() . '.slideshow_id = revslideshow_stores.slideshow_id',
                array())
                ->where('is_enabled = ?', 1)
                ->where('revslideshow_stores.store_id IN (?)', $storeIds)
                ->order('revslideshow_stores.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    public function lookupStoreIds($pageId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('revslideshow/stores'), 'store_id')
            ->where('slideshow_id = ?',(int)$pageId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Resource_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }

}
