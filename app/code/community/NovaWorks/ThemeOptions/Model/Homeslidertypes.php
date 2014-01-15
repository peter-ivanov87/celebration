<?php class NovaWorks_ThemeOptions_Model_Homeslidertypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'slide', 'label'=>Mage::helper('themeoptions')->__('Slide')),
            array('value'=>'fade', 'label'=>Mage::helper('themeoptions')->__('Fade'))            
        );
    }

}?>