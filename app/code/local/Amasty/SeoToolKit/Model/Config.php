<?php
class Amasty_SeoToolKit_Model_Config
{
	const PRODUCT_URL_PATH_SHORTEST = 1;
	const PRODUCT_URL_PATH_LONGEST = 2;

	public function getProductUrlType()
	{
		return Mage::getStoreConfig('amseotoolkit/general/product_url_type');
	}

}