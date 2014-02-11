<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('flashmagazine_category_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('category_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Category_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Category_Collection */
		$collection = Mage::getResourceModel('flashmagazine/category_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();

		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Category_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('category_id',
			array(
				'header'	=> $this->__('Category ID'),
				'width'		=> '80px',
				'type'		=> 'number',
				'index'		=> 'category_id'
			)
		);

		$this->addColumn(
			'category_name',
			array(
				'header'	=> $this->__('Category Name'),
				'index'		=> 'category_name',
			)
		);

		/*$this->addColumn('is_active',
			array(
				'header'	=> Mage::helper('cms')->__('Active'),
				'index'		=> 'is_active',
				'type'		=> 'options',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);*/

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
						'field'		=> 'category_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'category_id'
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
		$this->setMassactionIdField('category_id');
		$this->getMassactionBlock()->setFormFieldName('categorytable');

		$this->getMassactionBlock()
			->addItem('delete',
				array(
					'label'	=> Mage::helper('adminhtml')->__('Delete'),
					'url'	=> $this->getUrl('*/*/massDelete')
				)
			);


		return $this;
	}  

	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Mageplace_Flashmagazine_Model_Category $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('category_id' => $row->getId()));
	}
}
