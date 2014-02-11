<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Catalog_Product_Edit_Tab_Magazine_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_productId;

	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		$this->_productId = $this->getRequest()->getParam('id');

		parent::__construct();

		$this->setId('flashmagazine_magazine_grid'); /* WARNING! DON'T CHANGE GRID ID */
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
		if (!Mage::app()->isSingleStoreMode()) {
			if ($this->getRequest()->getParam('website')) {
				$storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
				$storeId = array_pop($storeIds);
			} else if ($this->getRequest()->getParam('group')) {
				$storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
				$storeId = array_pop($storeIds);
			} else if ($this->getRequest()->getParam('store')) {
				$storeId = (int)$this->getRequest()->getParam('store');
			} else {
				$storeId = '';
			}

			$collection->addStoreFilter($storeId);
		}

		if($this->_productId) {
			$collection->setProductId($this->_productId);
		}


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

		$this->addColumn('product_attached',
			array(
				'type'		=> 'options',
				'header'	=> Mage::helper('cms')->__('Attached'),
				'index'		=> 'product_attached',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				),
				'filter_condition_callback'	=> array(
					$this,
					'_filterProductAttachedCondition'
				)
			)
		);

		$this->addColumn('action',
			array(
				'type'		=> 'action',
				'header'	=> Mage::helper('adminhtml')->__('Action'),
				'align'     => 'center',
				'width'		=> '50px',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Attach/Detach'),
						'onClick'	=> 'flashmagazineJs.setRelation($magazine_id, $product_attached);',
						'url'		=> 'javascript:void(0);',
					),
				),
				'filter'	=> false,
				'sortable'	=> false,
			)
		);

		return parent::_prepareColumns();
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
	 * Helper function to add filter of relations of products and magazines
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterProductAttachedCondition($collection, $column)
	{
		$value = $column->getFilter()->getValue();
		if(is_null($value)) {
			return;
		}

		$this->getCollection()->addProductAttachedFilter($value);
	}

	/**
	 * Helper function to receive grid functionality urls for current grid
	 *
	 * @return string Requested URL
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/flashmagazine/product', array('_current' => true));
	}

	public function getRowUrl($row)
	{
		return '';
	}

	/* Delete this function in next version */
	protected function _prepareMassaction_UNUSABLE()
	{
		$this->setMassactionIdField('magazine_id');
		$this->getMassactionBlock()->setFormFieldName('attachtable');

		$this->getMassactionBlock()->addItem('attach',
			array(
				'label'		=> $this->__('Attach'),
				'url'		=> $this->getUrl('*/flashmagazine/massAttach', array('_current' => true)),
				'complete'	=> 'flashmagazineJs.complete'
			)
		);

		$this->getMassactionBlock()->addItem('detach',
			array(
				'label'		=> $this->__('Detach'),
				'url'		=> $this->getUrl('*/flashmagazine/massDetach', array('_current' => true)),
				'complete'	=> 'flashmagazineJs.complete'
			)
		);

		$this->getMassactionBlock()->setUseAjax(true);


		return $this;
	}
}
