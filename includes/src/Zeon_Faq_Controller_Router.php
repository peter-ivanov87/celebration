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

/**
 * Faq Controller Router
 *
 * @category    Zeon
 * @package     Zeon Faq
 * @author      Zeon Solutions Core Team
 */
class Zeon_Faq_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('faq', $this);
    }

    /**
     * Validate and Match FAQ Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $router = 'faq';
        $identifier = trim(str_replace('/faq/', '', $request->getPathInfo()), '/');

        $condition = new Varien_Object(
            array(
                'identifier' => $identifier,
                'continue'   => true
            )
        );

        Mage::dispatchEvent(
            'faq_controller_router_match_before', array(
                'router'    => $this,
                'condition' => $condition
            )
        );

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }
        $category   = Mage::getModel('zeon_faq/category');
        $categoryId = $category->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (!$categoryId && $identifier == 'mfaq') {

            $request->setModuleName('faq')
                ->setControllerName('index')
                ->setActionName('index');
            if (Mage::helper('zeon_faq')->getIsDisplayMfaq()) {
                $request->setParam('mfaq', 1);
            }
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $router
            );
            return true;
        } elseif ($categoryId) {
            $request->setModuleName('faq')
                ->setControllerName('index')
                ->setActionName('index')
                ->setParam('category_id', $categoryId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $router.'/'.$identifier
            );
            return true;
        } else {
            return false;
        }
        return false;
    }
}
