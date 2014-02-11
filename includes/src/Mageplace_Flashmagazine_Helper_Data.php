<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

if(Mage::helper('flashmagazine/version')->isEE()) {
	class Mageplace_Flashmagazine_Helper_Data extends Mageplace_Flashmagazine_Helper_Enterprise
	{
	}
} else {
	class Mageplace_Flashmagazine_Helper_Data extends Mageplace_Flashmagazine_Helper_Community
	{
	}
}
