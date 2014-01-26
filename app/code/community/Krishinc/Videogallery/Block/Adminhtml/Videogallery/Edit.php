<?php
    class Krishinc_Videogallery_Block_Adminhtml_Videogallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();
                   
            $this->_objectId = 'id';
            $this->_blockGroup = 'videogallery';
            $this->_controller = 'adminhtml_videogallery';
     
            $this->_updateButton('save', 'label', Mage::helper('videogallery')->__('Save Video'));
            $this->_updateButton('delete', 'label', Mage::helper('videogallery')->__('Delete Video'));
			$this->_updateButton('delete', 'onclick', 'deleteConfirm(\'Are you sure you want to do this?\', \'' .$this->getUrl('videogallery/adminhtml_videogallery/delete/videogallery_id/', array('videogallery_id' => $this->getRequest()->getParam('id'))).'\')');
        }
     
        public function getHeaderText()
        {
            if( Mage::registry('videogallery_data') && Mage::registry('videogallery_data')->getId() ) {
                     
                return Mage::helper('videogallery')->__("Edit Videogallery '%s'", $this->htmlEscape(Mage::registry('videogallery_data')->getName()));
            } else {
                return Mage::helper('videogallery')->__('Add New Video Information');
            }
        }
    }