<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */


class NovaWorks_RevSlideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Determine whether the extension is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfig('revslideshow/settings/enabled');
	}
}
