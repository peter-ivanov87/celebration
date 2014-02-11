<?php class NovaWorks_ThemeOptions_Model_Homeinfotype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Products')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Static Block'))         
        );
    }

}?>