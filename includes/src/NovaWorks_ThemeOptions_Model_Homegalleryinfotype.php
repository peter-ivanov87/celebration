<?php class NovaWorks_ThemeOptions_Model_Homegalleryinfotype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Products')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Images Slideshow')), 
            array('value'=>3, 'label'=>Mage::helper('themeoptions')->__('Static Content'))              
        );
    }

}?>