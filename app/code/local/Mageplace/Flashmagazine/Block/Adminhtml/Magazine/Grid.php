<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->setId('flashmagazine_magazine_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('magazine_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Magazine_Collection */
		$collection = Mage::getResourceModel('flashmagazine/magazine_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();


		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('magazine_id',
			array(
				'type'		=> 'number',
				'header'	=> $this->__('Book ID'),
				'width'		=> '80px',
				'index'		=> 'magazine_id',
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineIdCondition'
				)
			)
		);

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id',
				array(
					'header'					=> Mage::helper('cms')->__('Store view'),
					'index'						=> 'store_id',
					'type'						=> 'store',
					'store_all'					=> true,
					'store_view'				=> true,
					'sortable'					=> false,
					'filter_condition_callback'	=> array(
						$this,
						'_filterStoreCondition'
					)
				)
			);
		}

		$this->addColumn(
			'magazine_name',
			array(
				'type'		=> 'text',
				'header'	=> $this->__('Book Name'),
				'index'		=> 'magazine_title',
			)
		);

		$this->addColumn('magazine_category_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Category'),
				'index'						=> 'magazine_category_id',
				'options'					=> $this->_getMagazineCategories(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineCategoryCondition'
				)
			)
		);

		$this->addColumn('magazine_template_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Template'),
				'index'						=> 'magazine_template_id',
				'options'					=> $this->_getMagazineTemplates(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineTemplateCondition'
				)
			)
		);

		$this->addColumn('magazine_resolution_id',
			array(
				'type'						=> 'options',
				'header'					=> $this->__('Book Resolution'),
				'index'						=> 'magazine_resolution_id',
				'options'					=> $this->_getMagazineResolutions(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineResolutionCondition'
				)
			)
		);

		$this->addColumn('sort_order',
			array(
				'type'		=> 'number',
				'header'	=> $this->__('Position'),
				'index'		=> 'magazine_sort_order',
//				'editable'	=> true
			)
		);

		$this->addColumn('is_active',
			array(
				'type'		=> 'options',
				'header'	=> Mage::helper('cms')->__('Active'),
				'index'		=> 'is_active',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);

		$this->addColumn('action',
			array(
				'type'		=> 'action',
				'header'	=> Mage::helper('adminhtml')->__('Action'),
				'width'		=> '50px',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Edit'),
						'url'		=> array(
							'base' => '*/*/edit'
						),
						'field'		=> 'magazine_id'
					),
					array(
						'caption'	=> $this->__('Enable/Disable'),
						'url'		=> array(
							'base' => '*/*/enable'
						),
						'field'		=> 'magazine_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'magazine_id'
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
		$this->setMassactionIdField('magazine_id');
		$this->getMassactionBlock()->setFormFieldName('magazinetable');

		$this->getMassactionBlock()
			->addItem('enable',
				array(
					'label'	=> $this->__('Enable/Disable'),
					'url'	=> $this->getUrl('*/*/massEnable')
				)
			)
			->addItem('delete',
				array(
					'label'	=> Mage::helper('adminhtml')->__('Delete'),
					'url'	=> $this->getUrl('*/*/massDelete')
				)
			);

		return $this;
	}  

	/**
	 * Helper function to add magazine id filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterMagazineIdCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addMagazineIdFilter($value);
	}

	/**
	 * Helper function to add store filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addStoreFilter($value);
	}

	/**
	 * Helper function to load category collection
	 */
	protected function _getMagazineCategories()
	{
		return Mage::getResourceModel('flashmagazine/category_collection')->toOptionHash();
	}

	/**
	 * Helper function to add category filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterMagazineCategoryCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addCategoryFilter($value);
	}

	/**
	 * Helper function to load templates collection
	 */
	protected function _getMagazineTemplates()
	{
		return Mage::getResourceModel('flashmagazine/template_collection')->toOptionHash();
	}

	/**
	 * Helper function to add template filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterMagazineTemplateCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addTemplateFilter($value);
	}

	/**
	 * Helper function to load resolution collection
	 */
	protected function _getMagazineResolutions()
	{
		return Mage::getResourceModel('flashmagazine/resolution_collection')->toOptionHash();
	}

	/**
	 * Helper function to add resolution filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterMagazineResolutionCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addResolutionFilter($value);
	}

	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Mageplace_Flashmagazine_Model_Magazine $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('magazine_id' => $row->getId()));
	}
}
