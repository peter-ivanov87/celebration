<?php class NovaWorks_ThemeOptions_Model_Homeproductsperrow
{
    public function toOptionArray()
    {
        return array(
            array('value'=>4, 'label'=>Mage::helper('themeoptions')->__('4')),
            array('value'=>5, 'label'=>Mage::helper('themeoptions')->__('5')),         
        );
    }

}?>