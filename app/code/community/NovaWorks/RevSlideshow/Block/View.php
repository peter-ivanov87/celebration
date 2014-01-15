<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_View extends Mage_Core_Block_Template
{
	public function getSlideshows()
	{
		$store_id = Mage::app()->getStore()->getId();
		$collection = Mage::getModel('revslideshow/slideshow')->getCollection();
		$collection->addStoreFilter($store_id);
		$collection->addFieldToFilter('is_enabled',1);
		$collection->setOrder('sort_order','ASC');
		return $collection;
	}
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		
		if (!$this->getTemplate()) {
			$this->setTemplate('revslideshow/default.phtml');
		}
		return $this;	
	}
}
