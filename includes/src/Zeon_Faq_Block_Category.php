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

class Zeon_Faq_Block_Category extends Mage_Core_Block_Template
{
    protected $_categoryCollection = null;
    protected $_category = null;

    /**
     * Set active category if any.
     *
     * return Zeon_Faq_Block_Category
     */
    protected function _beforeToHtml()
    {
        $categoryId = $this->getRequest()->getParam('category', null);
        $model = Mage::getModel('zeon_faq/category');
        if ($categoryId) {
            $category = $model->load($categoryId);
            $this->setCurrentCategory($category);
        }

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Category collection
     *
     * @return Zeon_Faq_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
         if (is_null($this->_categoryCollection)) {
             $this->_categoryCollection = Mage::getResourceModel('zeon_faq/category_collection')
                                         ->addFieldToFilter('main_table.status', Zeon_Faq_Model_Status::STATUS_ENABLED)
                                         ->addOrder('sort_order', 'asc');
         }
         $this->_categoryCollection->getSelect()->distinct()
            ->join(array('zfc'=> Mage::getResourceModel('zeon_faq/faq')->getTable('zeon_faq/faq')), 'main_table.category_id = zfc.category_id',array('category_id'));
         return $this->_categoryCollection;
    }

    /**
     * Set current category.
     *
     * @param $label
     * @return Zeon_Faq_Block_Category
     */
    public function setCurrentCategory($category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Get current category.
     *
     * @return object
     */
    public function getCurrentCategory()
    {
        return $this->_category;
    }
    /**
     * Check for current category.
     *
     * @return object
     */
    public function isActiveCategory($category)
    {
         if (!is_null($this->getCurrentCategory())) {
             return ($this->getCurrentCategory()->getCategoryId() === $category->getCategoryId());
         }
         return false;
    }
}