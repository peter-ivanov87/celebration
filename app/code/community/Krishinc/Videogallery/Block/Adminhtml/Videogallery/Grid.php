<?php
    class Krishinc_Videogallery_Block_Adminhtml_Videogallery_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('videogalleryGrid');
            // This is the primary key of the database
            $this->setDefaultSort('videogallery_id');
            $this->setDefaultDir('DESC');
            $this->setSaveParametersInSession(true);
			//$this->setUseAjax(true);
        }
     	 protected function _prepareCollection() {

        $collection = Mage::getModel('videogallery/videogallery')->getCollection(); 
		$session = Mage::getSingleton('adminhtml/session');
		if($this->getRequest()->getParam('dir'))
			$dir=$this->getRequest()->getParam('dir');
		else
			$dir=(($videogalleryGrid=$session->getData('videogalleryGrid')) ? $videogalleryGrid : 'DESC');

		if($session->getData('videogalleryGridsort'))
			$videogalleryGridsort = $session->getData('videogalleryGridsort');
		else 
			$videogalleryGridsort = 'videogallery_id';

		if($sort=$this->getRequest()->getParam('sort'))
			$collection->getSelect()->order("$sort $dir");
		else
			$collection->getSelect()->order("$videogalleryGridsort $dir");
			
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

     
        protected function _prepareColumns() {
        $this->addColumn('videogallery_id', array(
            'header' => Mage::helper('videogallery')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'videogallery_id',
            'filter_index' => 'videogallery_id',
            'type'  => 'number',
            'sortable'  => true
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('videogallery')->__('Name'),
            'align' => 'left',
            'index' => 'name',
             'filter_index' => 'name',
			 'sortable'  => true 
        ));
		$this->addColumn('videogallery_category', array(
            'header' => Mage::helper('videogallery')->__('Category'),
            'align' => 'left',
            'index' => 'videogallery_category',
             'filter_index' => 'videogallery_category',
			 'sortable'  => true 
        ));
		$this->addColumn('image', array(
            'header' => Mage::helper('videogallery')->__('Image'),
            'align' => 'left',
            'index' => 'image',
			'type'         => 'image',
            'filter_index' => 'image',
			'width' => '75px',
			'height' => '75px',
			'filter' => false,
			'sortable'  => false,
			'renderer'  => 'videogallery/renderer_image',
        )); 
       
       
        $this->addColumn('action',
                array(
                    'header' => Mage::helper('videogallery')->__('Action'),
                    'width' => '100',
                    'type' => 'action',
                    'getter' => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('videogallery')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'videogallery_id',
                        'confirm'  => Mage::helper('videogallery')->__('Are you sure?')
                    )
                ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('videogallery')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('videogallery')->__('XML'));

        return parent::_prepareColumns();
    }
        protected function _prepareMassaction()
        {
            $this->setMassactionIdField('videogallery_id');
            $this->getMassactionBlock()->setFormFieldName('videogallery');

            $this->getMassactionBlock()->addItem('delete', array(
                 'label'    => Mage::helper('videogallery')->__('Delete'),
                 'url'      => $this->getUrl('*/*/massDelete'),
                 'confirm'  => Mage::helper('videogallery')->__('Are you sure?')
            ));

           // $statuses = Mage::getSingleton('videogallery/status')->getOptionArray();

           
            return $this;
        }
     
        public function getRowUrl($row)
        {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        public function getAllManu()     {       
            $product = Mage::getModel('catalog/product');       
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                ->addFieldToFilter('attribute_code', 'videogallery');      
            $attribute = $attributes->getFirstItem()->setEntity($product->getResource());       
            $videogallery = $attribute->getSource()->getAllOptions(false);      
             return $videogallery;                  
         }
		//public function getGridUrl()
//		{
//				return $this->getUrl('*/*/grid', array('_current'=>true));
//		} 
     
     
    }