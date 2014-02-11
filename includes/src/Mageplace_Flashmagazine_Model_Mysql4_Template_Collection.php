<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Model_Mysql4_Template_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('flashmagazine/template');
	}
		
	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('template_id', 'template_name');
	}
	
	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('template_id', 'template_name');
	}
	
	/**
	 * Add Filter by template type
	 * 
	 * @param int|Mageplace_Flashmagazine_Model_Template_Type|Mageplace_Flashmagazine_Model_Template $type Type to be filtered
	 * @return Mageplace_Flashmagazine_Model_Mysql4_Template_Type_Collection
	 */
	public function addTemplateTypeFilter($type)
	{
		if ($type instanceof Mageplace_Flashmagazine_Model_Template_Type) {
			$type = $type->getId();
		} else if ($type instanceof Mageplace_Flashmagazine_Model_Template) {
			$type = $type->getTemplateTypeId();
		}
		
		$type = (int)$type;
		
		$select = $this->getSelect()
			->join(
					array(
						'template_type_table' => $this->getTable('flashmagazine/template_type')
					),
					'main_table.template_type_id = template_type_table.type_id',
					array()
				)
			->where(
				'template_type_table.type_id IN (?)',
				array (
					0, 
					$type
				)
			)->group(
				'main_table.template_type_id'
			);

		return $this;
	}
}
