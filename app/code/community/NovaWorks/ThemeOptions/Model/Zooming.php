<?php class NovaWorks_ThemeOptions_Model_Zooming
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Fancybox')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Pimg')),
            array('value'=>3, 'label'=>Mage::helper('themeoptions')->__('Another Image On Hover'))            
        );
    }

}?>