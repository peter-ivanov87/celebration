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
class Zeon_Faq_Block_List extends Mage_Core_Block_Template
{
    protected $_faqCollection;
    
    public function __construct()
    {
        parent::__construct();
        $collection = $this->getFaqCollection();
        $this->setCollection($collection);
    }
    /**
     * Retrieve faq collection
     *
     * @return Zeon_Faq_Model_Resource_Faq_Collection
     */
    protected function _getFaqCollection()
    {
        $this->_faqCollection = Mage::getResourceModel('zeon_faq/faq_collection')
								->distinct(true)
                                ->addStoreFilter(Mage::app()->getStore()->getId())
                                ->addFieldToFilter('status', Zeon_Faq_Model_Status::STATUS_ENABLED)
                                ->addOrder('sort_order', 'ASC');
        if ($category = $this->getRequest()->getParam('category_id', null)) {
            $this->_faqCollection = $this->_faqCollection->addFieldToFilter('category_id', $category);
        }
        if ($mfaq = $this->getRequest()->getParam('mfaq', null)) {
            $this->_faqCollection = $this->_faqCollection->addFieldToFilter('is_most_frequently', $mfaq);
        }
        if ($toSearch = $this->getRequest()->getParam('faqsearch')) {
            $this->_faqCollection = $this->_faqCollection->addSearchFilter(array('title','description'), $toSearch);
        }
        return $this->_faqCollection;
    }
    /**
     * Retrieve loaded faq collection
     *
     * @return Zeon_Faq_Model_Resource_Faq_Collection
     */
    public function getFaqCollection()
    {
        return $this->_getFaqCollection();
    }
    /**
     * Prepare global layout
     *
     * @return Zeon_Faq_Block_List
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('zeon_faq');
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array('label'=>$helper->__('Home'), 'title'=>$helper->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            if ($categoryId = $this->getRequest()->getParam('category_id', null)) {
                $breadcrumbs->addCrumb('faq_list', array('label'=>$helper->__('FAQ'), 'title'=>$helper->__('FAQ'), 'link'=>Mage::getUrl('*')));
                $categoryTitle = Mage::getResourceModel('zeon_faq/category')->getFaqCategoryTitleById($categoryId);
                $breadcrumbs->addCrumb('faq_category', array('label'=>$categoryTitle, 'title'=>$categoryTitle));
            } elseif ($mfaq = $this->getRequest()->getParam('mfaq', null)) {
                $breadcrumbs->addCrumb('faq_list', array('label'=>$helper->__('FAQ'), 'title'=>$helper->__('FAQ'), 'link'=>Mage::getUrl('*')));
                $breadcrumbs->addCrumb('faq_category', array('label'=>$helper->__('MFAQ'), 'title'=>$helper->__('MFAQ')));
            } elseif ($toSearch = $this->getRequest()->getParam('faqsearch')) {
                $breadcrumbs->addCrumb('faq_list', array('label'=>$helper->__('FAQ'), 'title'=>$helper->__('FAQ'), 'link'=>Mage::getUrl('*')));
                $breadcrumbs->addCrumb('faq_search', array('label'=>$toSearch, 'title'=>$toSearch));
            } else {
                $breadcrumbs->addCrumb('faq_list', array('label'=>$helper->__('FAQ'), 'title'=>$helper->__('FAQ')));
            }
        }
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($helper->getDefaultTitle());
            $head->setKeywords($helper->getDefaultMetaKeywords());
            $head->setDescription($helper->getDefaultMetaDescription());
        }
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
 
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}