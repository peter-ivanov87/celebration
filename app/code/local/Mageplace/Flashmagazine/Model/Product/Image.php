<?php
/**
 * Mageplace Flash Magazine
 *
 * @category	Mageplace
 * @package		Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license	 	http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_Product_Image extends Mage_Catalog_Model_Product_Image
{
	protected $_suffix;

	public function setSuffix($suffix)
	{
		$this->_suffix = $suffix;

		return $this;
	}

	public function getSuffix()
	{
		if($this->_suffix) {
			return $this->_suffix;
		} else {
			return '-'.$this->getWidth().'x'.$this->getHeight();
		}
	}

	public function setBaseFile($file)
	{
		$this->_isBaseFilePlaceholder = false;

		if (!$this->_checkMemory($file)) {
			$file = null;
		}

		if ((!$file) || (!file_exists($file))) {
			throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
		}

		$this->_baseFile = $file;

		$info = pathinfo($file);
		$file_name = basename($file,'.'.$info['extension']).$this->getSuffix().'.'.$info['extension'];

		$this->_newFile = dirname($file).DS.$file_name;

		return $this;
	}
}
