<?php class NovaWorks_ThemeOptions_Model_Slideshowtype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Default Slider')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Fullwidth Slider')),
            array('value'=>3, 'label'=>Mage::helper('themeoptions')->__('Responsive Slider'))         
        );
    }

}?>