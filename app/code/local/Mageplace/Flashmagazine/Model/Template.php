<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Model_Template extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/template');
	}
	
	public function getName()
	{
		return $this->getTemplateName();
	}
}
