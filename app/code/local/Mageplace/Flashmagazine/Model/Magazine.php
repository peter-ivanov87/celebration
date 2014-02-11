<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Magazine extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/magazine');
	}

	public function getName()
	{
		return $this->getMagazineTitle();
	}

	public function getThumbUrl()
	{
		return Mage::helper('flashmagazine')->getPathUrl('thumb').'/'.$this->getMagazineThumb();
	}
}
