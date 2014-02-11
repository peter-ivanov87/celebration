<?php class NovaWorks_ThemeOptions_Model_Bgattachment
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'scroll', 'label'=>Mage::helper('themeoptions')->__('Scroll')),
            array('value'=>'fixed', 'label'=>Mage::helper('themeoptions')->__('Fixed'))   
        );
    }

}?>