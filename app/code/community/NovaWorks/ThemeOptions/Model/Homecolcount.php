<?php class NovaWorks_ThemeOptions_Model_Homecolcount
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'2', 'label'=>Mage::helper('themeoptions')->__('2 Columns')),
            array('value'=>'1', 'label'=>Mage::helper('themeoptions')->__('1 Column')),
            array('value'=>'0', 'label'=>Mage::helper('themeoptions')->__('Disable block'))           
        );
    }

}?>