<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Catalog_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{
	/**
	 * Processing block html after rendering
	 *
	 * @param   string $html
	 * @return  string
	 */
	protected function _afterToHtml($html)
	{
		$html  = parent::_afterToHtml($html);
		$html .= $this->getLayout()->getBlock('product.info.flashmagazine')->toHtml();

		return $html;
	}
}
