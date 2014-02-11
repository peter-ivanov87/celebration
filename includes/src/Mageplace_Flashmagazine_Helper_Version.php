<?php
/**
 * Mageplace Flashmagazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
 
class Mageplace_Flashmagazine_Helper_Version extends Mage_Core_Helper_Abstract
{
	const EDITION_COMMUNITY    = 'Community';
	const EDITION_ENTERPRISE   = 'Enterprise';
	const EDITION_PROFESSIONAL = 'Professional';
	
	public function getEdition()
	{
		static $edition;
		
		if(is_null($edition)) {
			if(@method_exists('Mage', 'getEdition')) {
				$edition = $this->_newEdition();
			} else {
				$edition = $this->_oldEdition();
			}
		}

		return $edition;
	}
	
	protected function _newEdition()
	{
		return Mage::getEdition();
	}
	
	protected function _oldEdition()
	{
		if(file_exists('LICENSE_EE.txt')) {
			return self::EDITION_ENTERPRISE;
		} elseif(file_exists('LICENSE_PRO.html')) {
			return self::EDITION_PROFESSIONAL;
		} else {
			if(@class_exists('Enterprise_Cms_Helper_Data')) {
				return self::EDITION_ENTERPRISE;
			} else {
				return self::EDITION_COMMUNITY;
			}			
		}
	}
	
	public function isCE()
	{
		return $this->getEdition() == self::EDITION_COMMUNITY;
	}
	
	public function isPE()
	{
		return $this->getEdition() == self::EDITION_PROFESSIONAL;
	}
	
	public function isEE()
	{
		return $this->getEdition() == self::EDITION_ENTERPRISE;
	}
	
	public function isOld()
	{
		return ($this->getEdition()=='EE' && version_compare(Mage::getVersion(), '1.11.0.0.', '<')===true)
			|| ($this->getEdition()=='PE' && version_compare(Mage::getVersion(), '1.11.0.0.', '<')===true)
			|| ($this->getEdition()=='CE' && version_compare(Mage::getVersion(), '1.6.0.0.', '<')===true);
	}
}