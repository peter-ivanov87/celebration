<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/ 
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tab_Condition extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Meta_Helper_Data */
        $hlp = Mage::helper('ammeta');

        $fldCond = $form->addFieldset('attr', array('legend'=> $hlp->__('Category & Stores')));

        $fldCond->addField('category_id', 'select', array(
            'label'     => $hlp->__('Category is'),
            'name'      => 'category_id',
            'values'    => $hlp->getTree(),
        ));             
        
        $fldCond->addField('stores', 'multiselect', array(
            'label'     => $hlp->__('Show In'),
            'name'      => 'stores[]',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(), 
        ));               

        //set form values
        $form->setValues(Mage::registry('ammeta_config')->getData()); 
        
        return parent::_prepareForm();
    } 
}