<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Frontend_Magazine extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$magazine = $this->getMagazine();

		if ($magazine !== false && $head = $this->getLayout()->getBlock('head')) {
			$head->setTitle($this->htmlEscape($magazine->getName()) . ' - ' . $head->getTitle());
		}

		return $this;
	}

	/**
	 * Function to gather the current magazine
	 *
	 * @return Mageplace_Flashmagazine_Model_Magazine The current magazine
	 */
	public function getMagazine()
	{
		$magazine = $this->getData('magazine');
		if (is_null($magazine)) {
			$magazine = Mage::registry('flashmagazine_current_magazine');
			$this->setData('magazine', $magazine);
		}

		return $magazine;
	}

	public function getHeight()
	{
		$height = $this->getData('height');
		if (is_null($height)) {
			$size = $this->_getSize();
			$height = $this->getData('height');
		}

		return $height;
	}

	public function getWidth()
	{
		$width = $this->getData('width');
		if (is_null($width)) {
			$size = $this->_getSize();
			$width = $this->getData('width');
		}

		return $width;
	}

	protected function _getSize()
	{
		$magazine = $this->getMagazine();
		if ($magazine->getIsPopupView()){
			$height = ($magazine->getResolutionHeight() + 130) . 'px';
			$width = (($magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 >= 760) ? $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 : 760) . 'px';
		} else {
			if($magazine->getMagazinePopup()) {
				$width = '100%';
				$height = '100%';
			} else {
				$height = ($magazine->getResolutionHeight() + 130).'px';
				$width = (($magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 >= 760) ? $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 : 760).'px';
			}
		}

		$this->setData('width', $width);
		$this->setData('height', $height);
	}

	public function getSwfUrl()
	{
		return Mage::helper('flashmagazine')->getPathUrl('flash').'/FlashMagazine.swf';
	}

	public function getCfgUrl()
	{
		return urlencode(Mage::helper('flashmagazine')->getMagazineUrl($this->getMagazine(), 'configXml'));
	}

	public function getLangUrl()
	{
		return urlencode(Mage::helper('flashmagazine')->getMagazineUrl($this->getMagazine(), 'langXml'));
	}

	public function getParamUrl()
	{
		return urlencode(Mage::helper('flashmagazine')->getMagazineUrl($this->getMagazine(), 'paramXml'));
	}

	public function getRelativeUrl()
	{
		return urlencode(str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '', Mage::helper('flashmagazine')->getPathUrl('flash')));
	}

	public function getBaseUrl()
	{
		return urlencode(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
	}
}