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
class Zeon_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_META_TITLE = 'zeon_faq/frontend/meta_title';
    const XML_PATH_DEFAULT_META_KEYWORDS = 'zeon_faq/frontend/meta_keywords';
    const XML_PATH_DEFAULT_META_DESCRIPTION = 'zeon_faq/frontend/meta_description';
    const XML_PATH_DEFAULT_IS_DISPLAY_MFAQ = 'zeon_faq/frontend/is_display_mfaq';

    public function getIsDisplayMfaq()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_IS_DISPLAY_MFAQ);
    }
    /**
     * Retrieve default title for faq
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_TITLE);
    }

    /**
     * Retrieve default meta keywords for faq
     *
     * @return string
     */
    public function getDefaultMetaKeywords()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_KEYWORDS);
    }

    /**
     * Retrieve default meta description for faq
     *
     * @return string
     */
    public function getDefaultMetaDescription()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_DESCRIPTION);
    }

    /**
     * Retrieve search query param
     *
     * @return string
     */
    public function getQueryParam()
    {
        return $this->_getRequest()->getParam('faqsearch');
    }
    
    /**
     * Retrieve Template processor for Block Content
     *
     * @return Varien_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        return Mage::getModel('zeon_faq/template_filter');
    }
}