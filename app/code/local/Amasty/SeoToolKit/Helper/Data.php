<?php
class Amasty_SeoToolKit_Helper_Data extends Mage_Core_Helper_Abstract
{
	const PRODUCT_URL_PATH_DEFAULT  = 0;
	const PRODUCT_URL_PATH_SHORTEST = 1;
	const PRODUCT_URL_PATH_LONGEST  = 2;

	/**
	 * @return bool
	 */
	public static function urlRewriteHelperEnabled()
	{
		return version_compare(Mage::getVersion(), '1.8') >= 0;
	}

	/**
	 * Check if SeoRichData exists
	 *
	 * @return bool
	 */
	public static function isSeoReviewsExists()
	{
		return Mage::getConfig()->getNode('modules/Amasty_SeoReviews') !== false;
	}

	/**
	 * Check if SeoRichData exists
	 *
	 * @return bool
	 */
	public static function isSeoRichDataExists()
	{
		return Mage::getConfig()->getNode('modules/Amasty_SeoRichData') !== false;
	}

	/**
	 * Check if SeoRichData exists
	 *
	 * @return bool
	 */
	public function isSeoMetaExists()
	{
		return Mage::getConfig()->getNode('modules/Amasty_Meta') !== false;
	}

	/**
	 * Check config for 301 redirect
	 *
	 * @return int
	 */
	public function is301RedirectEnabled()
	{
		return (int) Mage::getStoreConfig('amseotoolkit/general/home_redirect');
	}

	/**
	 * Product url type (shortest/longest/default)
	 *
	 * @return mixed
	 */
	public function getProductUrlType()
	{
		return Mage::getStoreConfig('amseotoolkit/general/product_url_type');
	}

	/**
	 * @return bool
	 */
	public function useDefaultProductUrlRules()
	{
		return (int) $this->getProductUrlType() == self::PRODUCT_URL_PATH_DEFAULT
			   || ! Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY);
	}
}