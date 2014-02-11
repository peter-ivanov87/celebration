<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Resolution_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('flashmagazine_resolution_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('resolution_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Resolution_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Resolution_Collection */
		$collection = Mage::getResourceModel('flashmagazine/resolution_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();
		
		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Resolution_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('resolution_id',
			array(
				'header'	=> $this->__('Resolution ID'), 
				'width'		=> '80px', 
				'type'		=> 'number', 
				'index'		=> 'resolution_id'
			)
		);
		
		$this->addColumn(
			'resolution_name',
			array(
				'header'	=> $this->__('Resolution Name'), 
				'index'		=> 'resolution_name',
			)
		);
		
		$this->addColumn(
			'resolution_width',
			array(
				'header'	=> $this->__('Resolution Width'), 
				'index'		=> 'resolution_width',
				'type'		=> 'number', 
				'style'		=> 'width:20px!important;',
			)
		);
		
		$this->addColumn(
			'resolution_height',
			array(
				'header'	=> $this->__('Resolution Height'), 
				'index'		=> 'resolution_height',
				'type'		=> 'number', 
				'style'		=> 'width:20px!important;',
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
						'field'		=> 'resolution_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'resolution_id'
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
		$this->setMassactionIdField('resolution_id');
		$this->getMassactionBlock()->setFormFieldName('resolutiontable');

		$this->getMassactionBlock()->addItem('delete',
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
	 * @param Mageplace_Flashmagazine_Model_Resolution $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('resolution_id' => $row->getId()));
	}
}
