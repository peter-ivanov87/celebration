<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('slideshow_grid');
		$this->setDefaultSort('sort_order');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('revslideshow/slideshow_collection');

		$this->setCollection($collection);
	
		return parent::_prepareCollection();
	}
	
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('slideshow_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'slideshow_id',
		));
		$this->addColumn('sort_order', array(
			'header'	=> $this->__('Order'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'sort_order',
		));
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => true,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
		$this->addColumn('title', array(
			'header'		=> $this->__('Title'),
			'align'			=> 'left',
			'index'			=> 'title',
		));

		$this->addColumn('is_enabled', array(
			'header'	=> $this->__('Enabled'),
			'width'		=> '90px',
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));
	
		$this->addColumn('action',
			array(
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Edit'),
						'url'     => array(
						'base'=>'*/*/edit',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));
		$this->addColumn('slide_captions',
			array(
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Slide Captions'),
						'url'     => array(
						'base'=>'*/*/slidecaptions',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));
		return parent::_prepareColumns();
	}
	
	/**
	 * Retrieve the URL used to modify the grid via AJAX
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid');
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('slideshow_id');
		$this->getMassactionBlock()->setFormFieldName('slideshow');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
	}
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
  protected function _filterStoreCondition($collection, $column)
  {
    if (!$value = $column->getFilter()->getValue()) {
      return;
    }

    $this->getCollection()->addStoreFilter($value);
  } 
}