<?php class NovaWorks_ThemeOptions_Model_Productzoom
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Cloud Zoom')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Standard zoom'))            
        );
    }

}?>