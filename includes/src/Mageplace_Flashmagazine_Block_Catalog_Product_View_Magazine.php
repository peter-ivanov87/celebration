<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Catalog_Product_View_Magazine extends Mage_Catalog_Block_Product_View_Abstract
{
	public function getMagazines()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Magazine_Collection */
		$collection = Mage::getResourceModel('flashmagazine/magazine_collection');
		$collection->setProductId($this->getProduct()->getId())
			->addProductAttachedFilter(1)
			->addIsActiveFilter()
			->setOrderByPosition()
			->addStoreFilter(Mage::app()->getSafeStore())
			->getItems();

		return $collection;
	}

	public function getPopupWidth($magazine)
	{
		return $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 >= 700 ? $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 : 700;
	}

	public function getPopupHeight($magazine)
	{
		return $magazine->getResolutionHeight() + 140;
	}

	public function getMagazineUrl($magazine)
	{
		return Mage::helper('flashmagazine')->getMagazineUrl($magazine);
	}
}
