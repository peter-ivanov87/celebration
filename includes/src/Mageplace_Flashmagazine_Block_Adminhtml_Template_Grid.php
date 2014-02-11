<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('flashmagazine_template_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('template_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Template_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Template_Collection */
		$collection = Mage::getResourceModel('flashmagazine/template_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();
		
		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Template_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('template_id',
			array(
				'header'	=> $this->__('Template ID'), 
				'width'		=> '80px', 
				'type'		=> 'number', 
				'index'		=> 'template_id'
			)
		);
		
		$this->addColumn(
			'template_name',
			array(
				'header'	=> $this->__('Template Name'), 
				'index'		=> 'template_name',
			)
		);
		
		$this->addColumn('template_type_id', 
			array(
				'header'					=> $this->__('Template Type'), 
				'index'						=> 'template_type_id', 
				'type'						=> 'options',
				'width'						=> '200px', 
				'options'					=> $this->_getTemplateTypes(),
				'sortable'					=> false, 
				'filter_condition_callback'	=> array(
					$this, 
					'_filterTemplateTypeCondition'
				)
			)
		);
		
		$this->addColumn('action', 
			array(
				'header'	=> Mage::helper('adminhtml')->__('Action'), 
				'width'		=> '50px',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Edit'), 
						'url'		=> array(
							'base' => '*/*/edit'
						), 
						'field'		=> 'template_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'template_id'
					),
				),
				'filter'	=> false, 
				'sortable'	=> false, 
				'is_system'	=> true,
			)
		);
		
		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('template_id');
		$this->getMassactionBlock()->setFormFieldName('templatetable');

		$this->getMassactionBlock()->addItem('delete',
			array(
				'label'	=> Mage::helper('adminhtml')->__('Delete'),
				'url'	=> $this->getUrl('*/*/massDelete')
			)
		);

		return $this;
	}  
	
	/**
	 * Helper function to load categories collection
	 */
	protected function _getTemplateTypes()
	{
		return Mage::getResourceModel('flashmagazine/template_type_collection')->toOptionHash();
	}
	
	/**
	 * Helper function to add category filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterTemplateTypeCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addTemplateTypeFilter($value);
	}
	
	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Mageplace_Flashmagazine_Model_Template $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('template_id' => $row->getId()));
	}
}
