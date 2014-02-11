<?php class NovaWorks_ThemeOptions_Model_Presets
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Light Preset')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Dark Present')),
 
        );
    }

}?>