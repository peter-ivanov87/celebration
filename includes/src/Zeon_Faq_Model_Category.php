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

class Zeon_Faq_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zeon_faq/category');
    }

    /**
     * Check if faq identifier exist
     * return faq category id if faq exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    function addError($error)
    {
        $this->_errors[] = $error;
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function resetErrors()
    {
        $this->_errors = array();
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }
}