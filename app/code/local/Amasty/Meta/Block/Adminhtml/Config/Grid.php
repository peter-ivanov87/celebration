<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Block_Adminhtml_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('configGrid');
		$this->setDefaultSort('config_id');
	}

	protected function _prepareCollection()
	{
		/** @var Amasty_Meta_Model_Mysql4_Config_Collection $collection */
		$collection = Mage::getModel('ammeta/config')->getCollection();

		$root =  $collection->getConnection()->quote(' - ' . Mage::helper('ammeta')->__('Root'));
		$title = Mage::getResourceModel('catalog/category')->getAttribute('name');

		$collection->getSelect()
			->joinLeft(
				array('cce' => $collection->getTable('catalog/category')),
				'cce.entity_id = main_table.category_id',
				array()
			)
			->joinLeft(
				array('att' => $title->getBackend()->getTable()),
				$collection->getConnection()->quoteInto('att.' . $title->getBackend()->getEntityIdField(
					) . ' = cce.entity_id AND
					att.entity_type_id  = cce.entity_type_id AND att.attribute_id = ?', $title->getId()
				),
				array('category_name' => new Zend_Db_Expr("COALESCE(value, $root)"))
			);


		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$hlp = Mage::helper('ammeta');

		$this->addColumn('config_id',
			array(
				'header' => $hlp->__('ID'),
				'align'  => 'right',
				'width'  => '50px',
				'index'  => 'config_id',
			));

		if (! Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'     => $hlp->__('Store'),
				'index'      => 'store_id',
				'type'       => 'store',
				'renderer'   => 'Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Renderer_Store',
				'filter'     => 'Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Filter_Store',
				'store_view' => true,
				'sortable'   => false
			));
		}

		$this->addColumn('category_id',
			array(
				'header'  => $hlp->__('Category'),
				'index'   => 'category_id',
				'renderer'   => 'Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Renderer_Category',
				'filter'    => 'Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select',
				'options' => $hlp->getTree(true),
			));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('config_id');
		$this->getMassactionBlock()->setFormFieldName('configs');

		$this->getMassactionBlock()->addItem('delete',
			array(
				'label'   => Mage::helper('ammeta')->__('Delete'),
				'url'     => $this->getUrl('*/*/massDelete'),
				'confirm' => Mage::helper('ammeta')->__('Are you sure?')
			));

		return $this;
	}
}