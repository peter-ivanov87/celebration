<?php
/**
 * Zeon Solutions, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Faq
 * @copyright   Copyright (c) 2012 Zeon Solutions, Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

class Zeon_Faq_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the faq_id refers to the key field in your database table.
        $this->_init('zeon_faq/faq', 'faq_id');
    }

    /**
     * Process news data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Zeon_News_Model_Mysql4_News
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'faq_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('zeon_faq/store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process category data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Zeon_Faq_Model_Mysql4_Faq
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'title',
            'title' => Mage::helper('zeon_faq')->__('FAQ with the same title')
        ));
        return $this;
    }

    /**
     * Load store Ids array
     *
     * @param Zeon_Faq_Model_Faq $object
     */
    public function loadStoreIds(Zeon_Faq_Model_Faq $object)
    {
        $faqId   = $object->getId();
        $storeIds = array();
        if ($faqId) {
            $storeIds = $this->lookupStoreIds($faqId);
        }
        $object->setStoreIds($storeIds);
    }
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()->from(
                $this->getTable('zeon_faq/store'),
                'store_id'
            )
            ->where("{$this->getIdFieldName()} = :id_field"),
            array(':id_field' => $id)
        );
    }
    /**
     * Delete current faq from the table zeon_faq_store and then
     * insert to update "faq to store" relations
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function saveFaqStore(Mage_Core_Model_Abstract $object)
    {
        /** stores */
        $deleteWhere = $this->_getReadAdapter()->quoteInto('faq_id = ?', $object->getId());
        $this->_getReadAdapter()->delete($this->getTable('zeon_faq/store'), $deleteWhere);
        foreach ($object->getStoreIds() as $storeId) {
            $faqStoreData = array(
            'faq_id'   => $object->getId(),
            'store_id'  => $storeId
            );
            $this->_getWriteAdapter()->insert($this->getTable('zeon_faq/store'), $faqStoreData);
        }
    }
}
