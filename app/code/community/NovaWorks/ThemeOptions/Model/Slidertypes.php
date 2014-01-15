<?php class NovaWorks_ThemeOptions_Model_Slidertypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Default List')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Slider'))            
        );
    }

}?>