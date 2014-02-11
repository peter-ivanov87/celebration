<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/
class Amasty_Meta_Block_Adminhtml_Config_Edit_Tab_Tags extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Meta_Helper_Data */
        $hlp   = Mage::helper('ammeta');
        $model = Mage::registry('ammeta_config');
        
        $fldInfo = $form->addFieldset('tags', array('legend'=> $hlp->__('Tags')));
        
        $fldInfo->addField('title', 'text', array(
            'label'     => $hlp->__('Page Title'),
            'name'      => 'title',
        ));         

        $fldInfo->addField('keywords', 'text', array(
            'label'     => $hlp->__('Keywords'),
            'name'      => 'keywords',
        ));         

        $fldInfo->addField('description', 'textarea', array(
            'label'     => $hlp->__('Meta Description'),
            'name'      => 'description',
        )); 

        $fldInfo->addField('short_description', 'textarea', array(
            'label'     => $hlp->__('Product Short Description'),
            'name'      => 'short_description',
        )); 

        $fldInfo->addField('full_description', 'textarea', array(
            'label'     => $hlp->__('Product Full Description'),
            'name'      => 'full_description',
        )); 
        
        //set form values
        $form->setValues($model->getData()); 
        
        return parent::_prepareForm();
    }
}