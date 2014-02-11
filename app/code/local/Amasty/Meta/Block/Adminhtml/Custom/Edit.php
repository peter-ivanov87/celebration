<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/
class Amasty_Meta_Block_Adminhtml_Custom_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'ammeta';
        $this->_controller = 'adminhtml_custom';
        
        $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('salesrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
       $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') } ";        
    }

    public function getHeaderText()
    {
        $header = Mage::helper('ammeta')->__('New Template');
        if (Mage::registry('ammeta_config')->getId()){
            $header = Mage::helper('ammeta')->__('Edit Template') . ' #' . Mage::registry('ammeta_config')->getId();
        }
        return $header;
    }
}