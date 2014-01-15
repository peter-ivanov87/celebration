<?php class NovaWorks_ThemeOptions_Model_Layoutthreecolumn
{
    public function toOptionArray()
    {
        return array(
            array('value'=>3, 'label'=>Mage::helper('themeoptions')->__('3')),
            array('value'=>4, 'label'=>Mage::helper('themeoptions')->__('4')),
        );
    }

}?>