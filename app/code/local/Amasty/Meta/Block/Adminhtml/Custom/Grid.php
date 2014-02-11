<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/ 
class Amasty_Meta_Block_Adminhtml_Custom_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('configGrid');
        $this->setDefaultSort('config_id');
    }
    
    protected function _prepareCollection()
    {
		$collection = Mage::getModel('ammeta/config')->getCustomCollection();
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('ammeta');

        $this->addColumn('config_id', array(
          'header'    => $hlp->__('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'config_id',
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

		$this->addColumn('priority', array(
			'header'    => $hlp->__('Priority'),
			'index'     => 'priority'
		));

        $this->addColumn('custom_url', array(
            'header'    => $hlp->__('URL'),
            'index'     => 'custom_url'
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
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('ammeta')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('ammeta')->__('Are you sure?')
        ));
        
        return $this; 
    }
}